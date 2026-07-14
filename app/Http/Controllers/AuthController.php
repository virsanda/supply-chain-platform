<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email'=>'Email atau password salah.'])->withInput($request->only('email'));
        }
        if (!Auth::user()->is_active) {
            Auth::logout();
            return back()->withErrors(['email'=>'Akun Anda tidak aktif.']);
        }
        $request->session()->regenerate();
        UserActivityLog::record(Auth::id(), 'login', null, ['ip'=>$request->ip()]);
        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required','confirmed',Password::min(8)],
        ]);
        $user = User::create(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password),'role'=>'user']);
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        UserActivityLog::record(Auth::id(), 'logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
