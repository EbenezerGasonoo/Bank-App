<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActions
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethodSafe() && $request->user()?->isAdmin()) {
            $targetModel = collect($request->route()?->parameters() ?? [])
                ->first(fn ($value) => $value instanceof Model);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => $request->route()?->getName() ?? $request->method() . ' ' . $request->path(),
                'model_type' => $targetModel ? class_basename($targetModel) : null,
                'model_id' => $targetModel?->getKey(),
                'changes' => [
                    'method' => $request->method(),
                    'payload' => $request->except(['_token', 'password', 'password_confirmation']),
                    'status_code' => $response->getStatusCode(),
                    'user_agent' => $request->userAgent(),
                ],
                'ip_address' => $request->ip(),
            ]);
        }

        return $response;
    }
}

