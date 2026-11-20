<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EcoProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function register()
    {
        return view('auth.register');
        
    }
    

public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'min:8', 'confirmed'],
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);

    EcoProfile::create([
        'user_id' => $user->id,
        'xp' => 0,
        'level' => 1,
    ]);

    return redirect()->route('login')
        ->with('success', 'Registrasi berhasil! Silakan login.');
}

public function guest(Request $request)
{
    $user = User::create([
        'name' => 'Guest ' . \Illuminate\Support\Str::random(6),
        'email' => \Illuminate\Support\Str::uuid() . '@guest.local',
        'password' => Hash::make(
            \Illuminate\Support\Str::random(32)
        ),
        'is_guest' => true,
    ]);

    EcoProfile::create([
        'user_id' => $user->id,
        'xp' => 0,
        'level' => 1,
    ]);

    Auth::login($user);

    $request->session()->regenerate();

    return redirect('/dashboard')
        ->with('success', 'Berhasil masuk sebagai Guest!');
}


public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('success', 'Berhasil logout!');
}


}

