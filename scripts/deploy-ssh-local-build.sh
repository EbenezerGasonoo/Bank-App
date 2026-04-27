#!/usr/bin/env bash

set -Eeuo pipefail

# ---------------------------------------------------------
# Local-to-server deploy (build assets locally, deploy via SSH)
# Designed for servers without Node/npm installed.
# ---------------------------------------------------------
SSH_TARGET="${SSH_TARGET:-easyintern-api}"
REMOTE_DIR="${REMOTE_DIR:-~/poisecommerce.com}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"   # true|false
RESTART_QUEUES="${RESTART_QUEUES:-true}"   # true|false

log() {
  printf "\n[%s] %s\n" "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

require_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Missing required command: $1" >&2
    exit 1
  fi
}

cleanup() {
  rm -rf "${TMP_DIR:-}"
}
trap cleanup EXIT

main() {
  require_cmd git
  require_cmd npm
  require_cmd ssh
  require_cmd scp
  require_cmd zip

  log "Building frontend assets locally"
  npm run build

  TMP_DIR="$(mktemp -d)"
  DEPLOY_ZIP="${TMP_DIR}/deploy.zip"

  log "Packaging tracked application files from HEAD"
  git archive --format=zip -o "$DEPLOY_ZIP" HEAD

  if [[ ! -f "public/build/manifest.json" ]]; then
    echo "Build output missing: public/build/manifest.json" >&2
    exit 1
  fi

  log "Appending built frontend assets to package"
  zip -qr "$DEPLOY_ZIP" public/build

  log "Uploading deployment package to ${SSH_TARGET}:${REMOTE_DIR}"
  scp "$DEPLOY_ZIP" "${SSH_TARGET}:${REMOTE_DIR}/deploy.zip"

  log "Applying package and running production tasks on server"
  ssh "$SSH_TARGET" "
    cd ${REMOTE_DIR} &&
    unzip -oq deploy.zip &&
    rm -f deploy.zip &&
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist &&
    composer deploy &&
    php artisan optimize
  "

  if [[ "$RUN_MIGRATIONS" == "true" ]]; then
    log "Running database migrations"
    ssh "$SSH_TARGET" "cd ${REMOTE_DIR} && php artisan migrate --force"
  fi

  if [[ "$RESTART_QUEUES" == "true" ]]; then
    log "Restarting queue workers"
    ssh "$SSH_TARGET" "cd ${REMOTE_DIR} && php artisan queue:restart"
  fi

  log "Deployment complete"
}

main "$@"
