# Deployment Runbook

This project includes two deployment scripts:

- `scripts/deploy.sh` - standard deployment
- `scripts/deploy-zero-downtime.sh` - maintenance mode + bypass secret + safety recovery
- `scripts/deploy-ssh-local-build.sh` - deploy from local machine (build assets locally, then upload via SSH)

## 1) One-time setup on server

From the project root on the server:

```bash
chmod +x scripts/deploy.sh
chmod +x scripts/deploy-zero-downtime.sh
```

Edit the config block at the top of each script and set:

- `APP_DIR` (example: `/var/www/poisecommerce`)
- `BRANCH` (example: `main`)
- `PHP_FPM_SERVICE` (optional, example: `php8.3-fpm`)
- `WEB_SERVICE` (optional, example: `nginx`)
- `RUN_MIGRATIONS` and `RESTART_QUEUES` as needed

## 2) Standard deploy

```bash
./scripts/deploy.sh
```

## 3) Zero-downtime deploy

```bash
./scripts/deploy-zero-downtime.sh
```

This script:

- puts app into maintenance mode
- prints a temporary bypass URL secret
- deploys code and assets
- runs `composer deploy`
- runs migrations/queue restart if enabled
- always tries `php artisan up` even if deployment fails

## 4) Expected safety checks

`composer deploy` includes the `deploy:check` guard, which fails if:

- `public/hot` exists
- `public/build/manifest.json` is missing

If it fails:

```bash
rm -f public/hot
npm ci
npm run build
composer deploy
```

## 5) SSH deploy for servers without Node/npm

Use this when your server cannot run `npm`.

Linux/macOS shell:

```bash
chmod +x scripts/deploy-ssh-local-build.sh
./scripts/deploy-ssh-local-build.sh
```

Windows PowerShell:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\deploy-ssh-local-build.ps1
```

Defaults are set for your environment:

- `SSH_TARGET=easyintern-api`
- `REMOTE_DIR=~/poisecommerce.com`
- `RUN_MIGRATIONS=true`
- `RESTART_QUEUES=true`

Override if needed:

```bash
SSH_TARGET=easyintern-api REMOTE_DIR=~/poisecommerce.com RUN_MIGRATIONS=false ./scripts/deploy-ssh-local-build.sh
```

PowerShell override example:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\deploy-ssh-local-build.ps1 -SshTarget easyintern-api -RemoteDir ~/poisecommerce.com -RunMigrations:$false -RestartQueues:$true
```
