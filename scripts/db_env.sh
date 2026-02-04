#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

load_env_file() {
  local file="$1"
  [[ -f "$file" ]] || return 0
  # Filtra variables conflictivas (UID/GID) y comments vacios.
  while IFS= read -r line; do
    [[ -z "$line" ]] && continue
    [[ "$line" =~ ^# ]] && continue
    [[ "$line" =~ ^UID= ]] && continue
    [[ "$line" =~ ^GID= ]] && continue
    # Exporta solo KEY=VALUE.
    if [[ "$line" =~ ^[A-Za-z_][A-Za-z0-9_]*= ]]; then
      export "$line"
    fi
  done < "$file"
}

load_env_file ./.env
load_env_file ./backend/.env
load_env_file ./.env.db
