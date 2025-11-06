#!/usr/bin/env bash
set -euo pipefail

dev_deploy() {
  local branch="${1:-$(git rev-parse --abbrev-ref HEAD)}"

  echo "Deploying branch: $branch"

  # Optional: per-app pre-reqs
  if command -v direnv >/dev/null 2>&1; then
    eval "$(direnv hook bash)" || true
  fi

  # Node workflow
  if [[ -f package.json ]]; then
    if command -v corepack >/dev/null 2>&1; then corepack enable || true; fi
    if [[ -f pnpm-lock.yaml ]]; then
      pnpm install --frozen-lockfile || pnpm install
      pnpm run build --if-present || true
      pnpm run test --if-present || true
    elif [[ -f yarn.lock ]]; then
      yarn install --frozen-lockfile || yarn install
      yarn build || true
      yarn test || true
    else
      npm ci || npm install
      npm run build --if-present || true
      npm test --if-present || true
    fi
  fi

  # Python workflow
  if [[ -f requirements.txt || -f pyproject.toml ]]; then
    python3 -m venv .venv
    # shellcheck disable=SC1091
    source .venv/bin/activate
    if [[ -f requirements.txt ]]; then pip install -r requirements.txt; fi
    if [[ -f pyproject.toml ]]; then pip install -e .; fi
    if command -v pytest >/dev/null 2>&1; then pytest -q || true; fi
    deactivate || true
  fi

  # Docker workflow
  if [[ -f docker-compose.yml || -f docker-compose.yaml ]]; then
    docker compose pull
    docker compose up -d --build
    docker compose ps
  fi

  echo "Deployment tasks completed."
}

dev_deploy "$@"

