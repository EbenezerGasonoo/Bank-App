<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LoginActivity;
use Illuminate\Http\Request;

class AdminSecurityController extends Controller
{
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('user')) {
            $needle = $request->user;
            $query->whereHas('user', function ($builder) use ($needle) {
                $builder->where('name', 'like', '%' . $needle . '%')
                    ->orWhere('email', 'like', '%' . $needle . '%');
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.security.audit-logs', compact('logs'));
    }

    public function loginActivity(Request $request)
    {
        $query = LoginActivity::with('user')->latest('logged_in_at');

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        $activities = $query->paginate(30)->withQueryString();

        return view('admin.security.login-activity', compact('activities'));
    }
}

