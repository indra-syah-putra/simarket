<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create(CaptchaService $captcha)
    {
        $captchaData = $captcha->generate();
        return view('auth.login', compact('captchaData'));
    }

    public function store(Request $request, CaptchaService $captcha)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string'],
        ]);

        if (!$captcha->validate($request->input('captcha'))) {
            return back()->withErrors(['captcha' => 'Jawaban captcha salah.'])->onlyInput('email');
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();
        session()->flash('success', 'Selamat datang, ' . Auth::user()->name . '!');
        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
