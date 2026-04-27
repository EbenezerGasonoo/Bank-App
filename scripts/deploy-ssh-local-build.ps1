param(
    [string]$SshTarget = "easyintern-api",
    [string]$RemoteDir = "~/poisecommerce.com",
    [bool]$RunMigrations = $true,
    [bool]$RestartQueues = $true
)

$ErrorActionPreference = "Stop"

function Write-Log {
    param([string]$Message)
    $ts = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host ""
    Write-Host "[$ts] $Message"
}

function Assert-Command {
    param([string]$Name)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Missing required command: $Name"
    }
}

Assert-Command "git"
Assert-Command "npm"
Assert-Command "ssh"
Assert-Command "scp"

$repoRoot = Split-Path -Parent $PSScriptRoot
Set-Location $repoRoot

$deployZip = Join-Path $repoRoot "deploy.zip"

try {
    Write-Log "Building frontend assets locally"
    npm run build

    if (-not (Test-Path "public/build/manifest.json")) {
        throw "Build output missing: public/build/manifest.json"
    }

    Write-Log "Packaging tracked application files from HEAD"
    if (Test-Path $deployZip) {
        Remove-Item -Force $deployZip
    }
    git archive --format=zip -o $deployZip HEAD

    Write-Log "Uploading deployment package"
    scp $deployZip "$SshTarget`:$RemoteDir/deploy.zip"

    Write-Log "Applying package and running production tasks on server"
    ssh $SshTarget "cd $RemoteDir && unzip -oq deploy.zip && rm -f deploy.zip && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist && composer deploy && php artisan optimize"

    Write-Log "Uploading prebuilt frontend assets to production"
    scp -r "public/build" "$SshTarget`:$RemoteDir/public/"

    if ($RunMigrations) {
        Write-Log "Running database migrations"
        ssh $SshTarget "cd $RemoteDir && php artisan migrate --force"
    }

    if ($RestartQueues) {
        Write-Log "Restarting queue workers"
        ssh $SshTarget "cd $RemoteDir && php artisan queue:restart"
    }

    Write-Log "Deployment complete"
}
finally {
    if (Test-Path $deployZip) {
        Remove-Item -Force $deployZip
    }
}
