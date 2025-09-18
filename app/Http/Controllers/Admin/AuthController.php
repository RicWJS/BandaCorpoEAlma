<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Se já estiver logado, redireciona para o dashboard
        if (Session::has('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ], [
            'username.required' => 'O campo usuário é obrigatório.',
            'password.required' => 'O campo senha é obrigatório.'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('username'));
        }

        // Busca as credenciais do .env
        $adminUser = env('ADMIN_USER', 'Admin');
        $adminPasswordHash = env('ADMIN_PASSWORD_HASH');

        if (!$adminPasswordHash) {
            return back()->with('error', 'Configuração de administrador não encontrada.');
        }

        // Verifica as credenciais
        if ($request->username === $adminUser && Hash::check($request->password, $adminPasswordHash)) {
            // Regenera a sessão para segurança
            $request->session()->regenerate();
            
            // Marca como autenticado
            Session::put('admin_authenticated', true);
            Session::put('admin_user', $adminUser);
            Session::put('admin_login_time', now());
            
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['login' => 'Credenciais inválidas.'])
            ->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        // Remove dados da sessão
        Session::forget(['admin_authenticated', 'admin_user', 'admin_login_time']);
        
        // Regenera a sessão
        $request->session()->regenerate();
        
        return redirect()->route('admin.login')->with('success', 'Logout realizado com sucesso.');
    }
}
