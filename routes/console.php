<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('deploy:check-assets', function () {
    $hotFile = public_path('hot');
    $manifestFile = public_path('build/manifest.json');

    if (File::exists($hotFile)) {
        $this->error('Deployment check failed: public/hot exists.');
        $this->line('Remove it before deploy: rm public/hot');
        return self::FAILURE;
    }

    if (! File::exists($manifestFile)) {
        $this->error('Deployment check failed: public/build/manifest.json is missing.');
        $this->line('Build frontend assets first: npm ci && npm run build');
        return self::FAILURE;
    }

    $this->info('Deployment asset check passed.');
    return self::SUCCESS;
})->purpose('Fail deployment when Vite assets are unsafe for production');
