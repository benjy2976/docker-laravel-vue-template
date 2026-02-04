#!/usr/bin/env bash

# Modelo -> tablas (separadas por espacios).
# Nota: mantiene nombres en "public" y evita esquemas.

declare -A MODEL_TABLES=(
  [projects]="projects"
  [auth]="users personal_access_tokens"
  [permissions]="roles permissions model_has_roles model_has_permissions role_has_permissions"
  [full]="users personal_access_tokens roles permissions model_has_roles model_has_permissions role_has_permissions projects cache cache_locks jobs job_batches failed_jobs"
)

# Dependencias opcionales entre modelos.
declare -A MODEL_DEPENDS=()

# Descripciones para listados.
declare -A MODEL_DESC=(
  [projects]="Proyectos"
  [auth]="Usuarios y tokens"
  [permissions]="Roles/permisos (Spatie)"
  [full]="Todos los modelos/tablas"
)

list_models() {
  for model in "${!MODEL_TABLES[@]}"; do
    printf '%s\t%s\n' "$model" "${MODEL_DESC[$model]:-}"
  done | sort
}

resolve_model_tables() {
  local model="$1"
  local include_deps="${2:-false}"
  local -A seen=()
  local -a result=()

  _resolve_model_tables_inner() {
    local m="$1"
    if [[ -n "${seen[$m]:-}" ]]; then
      return
    fi
    seen[$m]=1

    if [[ "$include_deps" == "true" ]]; then
      for dep in ${MODEL_DEPENDS[$m]:-}; do
        _resolve_model_tables_inner "$dep"
      done
    fi

    for tbl in ${MODEL_TABLES[$m]:-}; do
      result+=("$tbl")
    done
  }

  _resolve_model_tables_inner "$model"

  if [[ ${#result[@]} -eq 0 ]]; then
    return 1
  fi

  printf '%s\n' "${result[@]}"
}
