<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        // Verifica se a sessão não expirou (opcional - 2 horas)
        $loginTime = Session::get('admin_login_time');
        if ($loginTime && now()->diffInHours($loginTime) > 2) {
            Session::forget(['admin_authenticated', 'admin_user', 'admin_login_time']);
            return redirect()->route('admin.login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        return $next($request);
    }
}