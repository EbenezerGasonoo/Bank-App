#!/usr/bin/env bash

set -Eeuo pipefail

# -----------------------------
# Edit these for your server
# -----------------------------
APP_DIR="/var/www/poisecommerce"
BRANCH="main"
PHP_FPM_SERVICE=""        # e.g. php8.3-fpm (leave empty to skip)
WEB_SERVICE=""            # e.g. nginx or apache2 (leave empty to skip)
RUN_MIGRATIONS="true"     # true|false
RESTART_QUEUES="true"     # true|false

log() {
  printf "\n[%s] %s\n" "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

run_if_service_set() {
  local service_name="$1"
  local action="$2"
  if [[ -n "$service_name" ]]; then
    log "systemctl ${action} ${service_name}"
    sudo systemctl "${action}" "${service_name}"
  fi
}

main() {
  log "Starting deployment"

  if [[ ! -d "$APP_DIR" ]]; then
    echo "APP_DIR does not exist: $APP_DIR" >&2
    exit 1
  fi

  cd "$APP_DIR"

  log "Pulling latest code from ${BRANCH}"
  git fetch origin "$BRANCH"
  git checkout "$BRANCH"
  git pull --ff-only origin "$BRANCH"

  log "Installing PHP dependencies"
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

  log "Installing JS dependencies and building assets"
  npm ci
  npm run build

  log "Running app deploy pipeline"
  composer deploy

  if [[ "$RUN_MIGRATIONS" == "true" ]]; then
    log "Running database migrations"
    php artisan migrate --force
  fi

  if [[ "$RESTART_QUEUES" == "true" ]]; then
    log "Restarting Laravel queue workers"
    php artisan queue:restart
  fi

  run_if_service_set "$PHP_FPM_SERVICE" reload
  run_if_service_set "$WEB_SERVICE" reload

  log "Deployment complete"
}

main "$@"
