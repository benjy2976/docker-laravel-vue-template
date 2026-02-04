#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

# Carga variables de entorno (root + backend) sin UID/GID.
# shellcheck disable=SC1091
source "$ROOT_DIR/scripts/db_env.sh"
# shellcheck disable=SC1091
source "$ROOT_DIR/scripts/db_models_pg.sh"

DB_DATABASE="${DB_DATABASE:-template}"
DB_USERNAME="${DB_USERNAME:-template}"
DB_PASSWORD="${DB_PASSWORD:-template}"

usage() {
  cat <<'USAGE'
Uso:
  ./scripts/db_backup_pg.sh [opciones]

Opciones:
  -m, --model <nombre>       Modelo a respaldar (ver --list-models)
  -t, --tables <t1,t2>        Lista de tablas separadas por coma
  -o, --out <dir>             Directorio base de salida
      --schema               Incluye respaldo de estructura (por defecto)
      --include-deps         Incluye dependencias del modelo
      --list-models          Lista modelos disponibles
  -h, --help                 Muestra esta ayuda

Notas:
- Por defecto genera backups por modelo en backups/db_pg_<stamp>/models/<modelo>/
- Los dumps se guardan por tabla (data/ y schema/ si aplica).
USAGE
}

MODEL=""
TABLES_RAW=""
OUT_DIR_BASE=""
INCLUDE_SCHEMA="true"
INCLUDE_DEPS="false"
LIST_MODELS="false"

while [[ $# -gt 0 ]]; do
  case "$1" in
    -m|--model)
      MODEL="$2"
      shift 2
      ;;
    -t|--tables)
      TABLES_RAW="$2"
      shift 2
      ;;
    -o|--out)
      OUT_DIR_BASE="$2"
      shift 2
      ;;
    --schema)
      INCLUDE_SCHEMA="true"
      shift
      ;;
    --include-deps)
      INCLUDE_DEPS="true"
      shift
      ;;
    --list-models)
      LIST_MODELS="true"
      shift
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Argumento desconocido: $1"
      usage
      exit 1
      ;;
  esac
done

if [[ "$LIST_MODELS" == "true" ]]; then
  echo "Modelos disponibles:"
  list_models
  exit 0
fi

prompt_model() {
  local -a options=()
  local -a labels=()
  while IFS=$'\t' read -r name desc; do
    options+=("$name")
    labels+=("$desc")
  done < <(list_models)

  if [[ ${#options[@]} -eq 0 ]]; then
    echo "No hay modelos configurados."
    exit 1
  fi

  echo "Selecciona un modelo:"
  local i=1
  for idx in "${!options[@]}"; do
    echo "  [$i] ${options[$idx]}${labels[$idx]:+ - ${labels[$idx]}}"
    i=$((i + 1))
  done

  local choice
  read -r -p "Opcion: " choice
  if ! [[ "$choice" =~ ^[0-9]+$ ]] || (( choice < 1 || choice > ${#options[@]} )); then
    echo "Opcion invalida"
    exit 1
  fi

  echo "${options[$((choice - 1))]}"
}

docker_exec() {
  docker compose exec -T -e PGPASSWORD="$DB_PASSWORD" db "$@"
}

fetch_existing_tables() {
  docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -Atc \
    "SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename;"
}

filter_existing_tables() {
  local -a tables=("$@")
  local -A existing=()
  local -a filtered=()

  local existing_output
  if ! existing_output=$(fetch_existing_tables); then
    echo "Error: no se pudo consultar las tablas en la BD actual." >&2
    return 1
  fi

  while IFS= read -r tbl; do
    [[ -n "$tbl" ]] && existing["$tbl"]=1
  done <<< "$existing_output"

  if [[ ${#existing[@]} -eq 0 ]]; then
    echo "Aviso: no se pudieron listar tablas en la BD actual." >&2
  fi

  for tbl in "${tables[@]}"; do
    if [[ -n "${existing[$tbl]:-}" ]]; then
      filtered+=("$tbl")
    else
      echo "Aviso: la tabla '$tbl' no existe en la BD actual; se omite del backup." >&2
    fi
  done

  printf '%s\n' "${filtered[@]}"
}

if [[ -z "$MODEL" && -z "$TABLES_RAW" ]]; then
  MODEL="$(prompt_model)"
fi

TABLES=()

if [[ -n "$TABLES_RAW" ]]; then
  IFS=',' read -r -a TABLES <<< "$TABLES_RAW"
  if [[ -z "$MODEL" ]]; then
    MODEL="custom"
  fi
else
  if [[ -z "$MODEL" ]]; then
    echo "Debes especificar --model o --tables."
    exit 1
  fi
  mapfile -t TABLES < <(resolve_model_tables "$MODEL" "$INCLUDE_DEPS" || true)
  if [[ ${#TABLES[@]} -eq 0 ]]; then
    echo "Modelo no encontrado o sin tablas: $MODEL"
    echo "Usa --list-models para ver opciones."
    exit 1
  fi
fi

tables_output=$(filter_existing_tables "${TABLES[@]}") || exit 1
mapfile -t TABLES <<< "$tables_output"
if [[ ${#TABLES[@]} -eq 0 ]]; then
  echo "No hay tablas para respaldar."
  exit 1
fi

STAMP="$(date +%Y%m%d_%H%M%S)"
OUT_DIR_BASE="${OUT_DIR_BASE:-$ROOT_DIR/backups/db_pg_$STAMP}"
MODEL_DIR="$OUT_DIR_BASE/models/$MODEL"
DATA_DIR="$MODEL_DIR/data"
SCHEMA_DIR="$MODEL_DIR/schema"

DEFAULT_MODE="data"
if [[ "$INCLUDE_SCHEMA" == "true" ]]; then
  DEFAULT_MODE="all"
fi

mkdir -p "$DATA_DIR"
if [[ "$INCLUDE_SCHEMA" == "true" ]]; then
  mkdir -p "$SCHEMA_DIR"
fi

MANIFEST="$MODEL_DIR/manifest.env"
APP_COMMIT="$(git -C "$ROOT_DIR" rev-parse --short HEAD 2>/dev/null || echo "unknown")"
{
  echo "MODEL=$MODEL"
  echo "DB_DATABASE=$DB_DATABASE"
  echo "STAMP=$STAMP"
  echo "CREATED_AT=$(date -Iseconds)"
  echo "INCLUDE_SCHEMA=$INCLUDE_SCHEMA"
  echo "DEFAULT_MODE=$DEFAULT_MODE"
  echo "APP_COMMIT=$APP_COMMIT"
  printf 'TABLES="%s"\n' "${TABLES[*]}"
} > "$MANIFEST"

printf '%s\n' "${TABLES[@]}" > "$MODEL_DIR/tables.txt"

echo "Backup de modelo '$MODEL' -> $MODEL_DIR"

for tbl in "${TABLES[@]}"; do
  data_file="$DATA_DIR/$tbl.sql"
  echo "Exportando datos: $tbl -> $data_file"
  docker_exec pg_dump -U "$DB_USERNAME" -d "$DB_DATABASE" \
    --data-only --inserts --column-inserts --no-owner --no-privileges --table="$tbl" > "$data_file"

  if [[ "$INCLUDE_SCHEMA" == "true" ]]; then
    schema_file="$SCHEMA_DIR/$tbl.sql"
    echo "Exportando estructura: $tbl -> $schema_file"
    docker_exec pg_dump -U "$DB_USERNAME" -d "$DB_DATABASE" \
      --schema-only --no-owner --no-privileges --table="$tbl" > "$schema_file"
  fi
done

echo "Backup completado."
