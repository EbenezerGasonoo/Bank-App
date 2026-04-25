#!/usr/bin/env bash

set -Eeuo pipefail

# -----------------------------
# Edit these for your server
# -----------------------------
APP_DIR="/var/www/poisecommerce"
BRANCH="main"
PHP_FPM_SERVICE=""         # e.g. php8.3-fpm (leave empty to skip)
WEB_SERVICE=""             # e.g. nginx or apache2 (leave empty to skip)
RUN_MIGRATIONS="true"      # true|false
RESTART_QUEUES="true"      # true|false
MAINTENANCE_RETRY="60"     # seconds for Retry-After header

APP_WENT_DOWN="false"

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

bring_app_up() {
  if [[ "$APP_WENT_DOWN" == "true" ]]; then
    log "Bringing application out of maintenance mode"
    php artisan up || true
  fi
}

on_error() {
  local exit_code=$?
  log "Deployment failed with exit code ${exit_code}"
  bring_app_up
  exit "$exit_code"
}

trap on_error ERR

main() {
  log "Starting zero-downtime deployment"

  if [[ ! -d "$APP_DIR" ]]; then
    echo "APP_DIR does not exist: $APP_DIR" >&2
    exit 1
  fi

  cd "$APP_DIR"

  local bypass_secret
  bypass_secret="$(php -r "echo bin2hex(random_bytes(16));")"

  log "Enabling maintenance mode (with bypass secret)"
  php artisan down --retry="$MAINTENANCE_RETRY" --secret="$bypass_secret" --render="errors::503"
  APP_WENT_DOWN="true"

  log "Maintenance bypass URL: /${bypass_secret}"

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

  bring_app_up
  log "Zero-downtime deployment complete"
}

main "$@"
