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

BACKUP_ROOT="$ROOT_DIR/backups"

usage() {
  cat <<'USAGE'
Uso:
  ./scripts/db_restore_pg.sh [backup_dir] [modo] [opciones]

Argumentos:
  backup_dir          Directorio del backup (opcional; si no, se pregunta)
  modo                data | all (opcional; si no, se pregunta)

Opciones:
  -m, --model <nombre>     Modelo a restaurar (solo backups por modelo)
      --truncate           TRUNCATE tables antes de restaurar (usa CASCADE)
      --strict             Falla si falta alguna tabla del modelo
  -h, --help               Muestra esta ayuda

Notas:
- Detecta backups por modelo en backups/db_pg_*/models/<modelo>/
- Mantiene compatibilidad con backups legacy (structure/data SQL unificado).
USAGE
}

BACKUP_DIR=""
MODE=""
MODEL=""
TRUNCATE_BEFORE="false"
STRICT_MISSING="false"

if [[ -n "${1:-}" && "${1}" != "-"* ]]; then
  BACKUP_DIR="$1"
  shift
fi

if [[ -n "${1:-}" && "$1" != "-"* ]]; then
  MODE="$1"
  shift
fi

while [[ $# -gt 0 ]]; do
  case "$1" in
    -m|--model)
      MODEL="$2"
      shift 2
      ;;
    --truncate)
      TRUNCATE_BEFORE="true"
      shift
      ;;
    --strict)
      STRICT_MISSING="true"
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

list_backups() {
  local base="$1"
  if [[ ! -d "$base" ]]; then
    return 0
  fi
  find "$base" -type d -name 'db_pg_*' | sort -r
}

prompt_backup() {
  local backups
  mapfile -t backups < <(list_backups "$BACKUP_ROOT")
  if [[ ${#backups[@]} -eq 0 ]]; then
    echo "No se encontraron backups en $BACKUP_ROOT"
    echo "Ejecuta primero: ./scripts/db_backup_pg.sh"
    exit 1
  fi

  echo "Selecciona un backup:" >&2
  local i=1
  for b in "${backups[@]}"; do
    local label
    label="$(basename "$b")"
    echo "  [$i] $label" >&2
    i=$((i + 1))
  done

  local choice
  read -r -p "Opcion: " choice
  if ! [[ "$choice" =~ ^[0-9]+$ ]] || (( choice < 1 || choice > ${#backups[@]} )); then
    echo "Opcion invalida"
    exit 1
  fi

  echo "${backups[$((choice - 1))]}"
}

prompt_mode() {
  local default="${1:-}"
  echo "Selecciona modo de restauracion:" >&2
  if [[ "$default" == "data" ]]; then
    echo "  [1] Solo datos (default)" >&2
    echo "  [2] Estructura + datos" >&2
  elif [[ "$default" == "all" ]]; then
    echo "  [1] Solo datos" >&2
    echo "  [2] Estructura + datos (default)" >&2
  else
    echo "  [1] Solo datos" >&2
    echo "  [2] Estructura + datos" >&2
  fi
  local choice
  read -r -p "Opcion${default:+ (Enter = ${default})}: " choice
  if [[ -z "$choice" && -n "$default" ]]; then
    echo "$default"
    return
  fi
  case "$choice" in
    1) echo "data" ;;
    2) echo "all" ;;
    *) echo "Opcion invalida"; exit 1 ;;
  esac
}

prompt_model_from_backup() {
  local base="$1"
  local -a options=()
  local -a labels=()

  while IFS= read -r path; do
    local name
    name="$(basename "$path")"
    options+=("$name")
    if [[ -f "$path/manifest.env" ]]; then
      local desc=""
      while IFS= read -r line; do
        if [[ "$line" == MODEL=* ]]; then
          desc="$(echo "$line" | cut -d'=' -f2-)"
          break
        fi
      done < "$path/manifest.env"
      labels+=("$desc")
    else
      labels+=("")
    fi
  done < <(find "$base/models" -mindepth 1 -maxdepth 1 -type d | sort)

  if [[ ${#options[@]} -eq 0 ]]; then
    echo "No se encontraron modelos en $base/models"
    exit 1
  fi

  echo "Selecciona un modelo:" >&2
  local i=1
  for idx in "${!options[@]}"; do
    echo "  [$i] ${options[$idx]}${labels[$idx]:+ - ${labels[$idx]}}" >&2
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

table_exists() {
  local table="$1"
  docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -Atc \
    "SELECT 1 FROM pg_tables WHERE schemaname='public' AND tablename='${table}' LIMIT 1;"
}

if [[ -z "$BACKUP_DIR" ]]; then
  BACKUP_DIR="$(prompt_backup)"
fi

if [[ ! -d "$BACKUP_DIR" ]]; then
  echo "Directorio de backup no encontrado: $BACKUP_DIR"
  exit 1
fi

# Detecta formato nuevo (por modelo).
NEW_FORMAT="false"
if [[ -d "$BACKUP_DIR/models" ]]; then
  shopt -s nullglob
  manifests=("$BACKUP_DIR/models"/*/manifest.env)
  shopt -u nullglob
  if [[ ${#manifests[@]} -gt 0 ]]; then
    NEW_FORMAT="true"
  fi
fi

if [[ "$NEW_FORMAT" == "true" ]]; then
  if [[ -z "$MODEL" ]]; then
    MODEL="$(prompt_model_from_backup "$BACKUP_DIR")"
  fi

  MODEL_DIR="$BACKUP_DIR/models/$MODEL"
  MANIFEST="$MODEL_DIR/manifest.env"
  DATA_DIR="$MODEL_DIR/data"
  SCHEMA_DIR="$MODEL_DIR/schema"

  if [[ ! -f "$MANIFEST" ]]; then
    echo "No existe manifest para el modelo '$MODEL' en: $MODEL_DIR"
    exit 1
  fi

  # shellcheck disable=SC1090
  source "$MANIFEST"
  TABLES_RAW="${TABLES:-}"
  unset TABLES

  if [[ -z "$TABLES_RAW" ]]; then
    echo "Manifest sin tablas: $MANIFEST"
    exit 1
  fi

  IFS=' ' read -r -a TABLES <<< "$TABLES_RAW"

  if [[ -z "$MODE" ]]; then
    default_mode="${DEFAULT_MODE:-}"
    if [[ -z "$default_mode" ]]; then
      if [[ "${INCLUDE_SCHEMA:-false}" == "true" ]]; then
        default_mode="all"
      else
        default_mode="data"
      fi
    fi
    MODE="$(prompt_mode "$default_mode")"
  fi
  echo "Modo de restauracion: $MODE"

  if [[ "$MODE" == "all" ]]; then
    if [[ ! -d "$SCHEMA_DIR" ]]; then
      echo "No existe directorio de estructura: $SCHEMA_DIR"
      exit 1
    fi
  fi

  # Truncate opcional.
  if [[ "$TRUNCATE_BEFORE" == "true" ]]; then
    existing_tables=()
    for tbl in "${TABLES[@]}"; do
      if [[ "$(table_exists "$tbl")" == "1" ]]; then
        existing_tables+=("$tbl")
      fi
    done
    if [[ ${#existing_tables[@]} -gt 0 ]]; then
      table_list=$(IFS=','; echo "${existing_tables[*]}")
      echo "TRUNCATE tablas: $table_list"
      docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -v ON_ERROR_STOP=1 -c \
        "TRUNCATE TABLE $table_list RESTART IDENTITY CASCADE;" >/dev/null
    fi
  fi

  if [[ "$MODE" == "all" ]]; then
    echo "Restaurando estructura (modelo: $MODEL)..."
    for tbl in "${TABLES[@]}"; do
      if [[ "$(table_exists "$tbl")" != "1" ]]; then
        if [[ "$STRICT_MISSING" == "true" ]]; then
          echo "Tabla faltante: $tbl"
          exit 1
        fi
        echo "Aviso: tabla '$tbl' no existe, se omite estructura."
        continue
      fi
      schema_file="$SCHEMA_DIR/$tbl.sql"
      if [[ -f "$schema_file" ]]; then
        docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -v ON_ERROR_STOP=1 < "$schema_file"
      else
        echo "Aviso: no existe archivo de estructura para $tbl."
      fi
    done
  fi

  echo "Restaurando datos (modelo: $MODEL)..."
  for tbl in "${TABLES[@]}"; do
    if [[ "$(table_exists "$tbl")" != "1" ]]; then
      if [[ "$STRICT_MISSING" == "true" ]]; then
        echo "Tabla faltante: $tbl"
        exit 1
      fi
      echo "Aviso: tabla '$tbl' no existe, se omite data."
      continue
    fi
    data_file="$DATA_DIR/$tbl.sql"
    if [[ -f "$data_file" ]]; then
      docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -v ON_ERROR_STOP=1 \
        -c "SET session_replication_role = replica;" \
        -f /dev/stdin \
        -c "SET session_replication_role = DEFAULT;" < "$data_file"
    else
      echo "Aviso: no existe archivo de datos para $tbl."
    fi
  done

else
  if [[ -z "$MODE" ]]; then
    MODE="$(prompt_mode)"
  fi
  # Legacy: backup con estructura/data unificados.
  STRUCT_FILE="$BACKUP_DIR/${DB_DATABASE}_structure.sql"
  DATA_FILE="$BACKUP_DIR/${DB_DATABASE}_data.sql"

  if [[ "$MODE" == "all" && ! -f "$STRUCT_FILE" ]]; then
    echo "No existe el archivo de estructura: $STRUCT_FILE"
    exit 1
  fi

  if [[ ! -f "$DATA_FILE" ]]; then
    echo "No existe el archivo de datos: $DATA_FILE"
    exit 1
  fi

  if [[ "$MODE" == "all" ]]; then
    echo "Restaurando estructura desde: $STRUCT_FILE"
    docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -v ON_ERROR_STOP=1 < "$STRUCT_FILE"
  fi

  echo "Restaurando datos desde: $DATA_FILE"
  docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -v ON_ERROR_STOP=1 -c \
    "SET session_replication_role = replica;" >/dev/null

  docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -v ON_ERROR_STOP=1 < "$DATA_FILE"

  docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -v ON_ERROR_STOP=1 -c \
    "SET session_replication_role = DEFAULT;" >/dev/null
fi

# Recalcula secuencias (si existen serial/identity).
echo "Recalculando secuencias..."
seq_sql=$(docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -Atc \
  "SELECT format('SELECT setval(%L, COALESCE(MAX(%I),0)+1, false) FROM %I;', seq, col, tbl) FROM (\
     SELECT\
       quote_ident(t.relname) AS tbl,\
       quote_ident(a.attname) AS col,\
       pg_get_serial_sequence(quote_ident(t.relname), a.attname) AS seq\
     FROM pg_class t\
     JOIN pg_attribute a ON a.attrelid = t.oid\
     JOIN pg_attrdef d ON d.adrelid = t.oid AND d.adnum = a.attnum\
     WHERE t.relkind='r'\
       AND a.attnum > 0\
       AND NOT a.attisdropped\
       AND pg_get_expr(d.adbin, d.adrelid) LIKE 'nextval(%'\
  ) s WHERE seq IS NOT NULL;")

if [[ -n "$seq_sql" ]]; then
  docker_exec psql -U "$DB_USERNAME" -d "$DB_DATABASE" -c "$seq_sql" >/dev/null
fi

echo "Restore completado."
