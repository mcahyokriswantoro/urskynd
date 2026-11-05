<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Silakan masukkan alamat email akun Anda.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Kami tidak menemukan akun yang terdaftar dengan alamat email tersebut.']);
        }

        // Buat token reset password resmi di tabel password_reset_tokens
        $token = Password::broker()->createToken($user);

        // Coba kirimkan juga notifikasi link reset ke email (jika SMTP aktif, atau ke log jika lokal)
        try {
            $user->sendPasswordResetNotification($token);
        } catch (\Throwable $e) {
            // Tetap lanjutkan agar user tetap bisa klik tombol lanjut reset langsung
        }

        $directResetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        return back()
            ->with('status', 'Tautan pemulihan kata sandi telah dibuat dan dikirimkan ke email Anda.')
            ->with('direct_reset_url', $directResetUrl)
            ->with('reset_email', $user->email);
    }
}
