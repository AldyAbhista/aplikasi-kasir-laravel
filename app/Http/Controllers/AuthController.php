<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ================= BAGIAN LOGIN =================
    public function index()
    {
        // Jika sudah login, arahkan ke dashboard yang sesuai
        if (Auth::check()) {
            if (auth()->user()->role === 'admin') {
                return redirect('/admin');
            }

            return redirect('/kasir');
        }

        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            ActivityLog::record(
                action: 'login',
                description: "User {$user->name} ({$user->email}) berhasil masuk ke sistem.",
                model: $user
            );

            if ($user->role === 'admin') {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/kasir');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // ================= BAGIAN REGISTRASI & VERIFIKASI EMAIL =================
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.register');
    }

    public function storeRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kasir', // Registrasi publik selalu membuat akun Kasir (aman)
        ]);

        // Kirim notifikasi verifikasi email via Laravel Notification
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        ActivityLog::record(
            action: 'register',
            description: "User baru {$user->name} ({$user->email}) telah mendaftar dan dikirimkan email verifikasi.",
            model: $user
        );

        return redirect()->route('verification.notice')->with('success', 'Akun berhasil dibuat! Silakan verifikasi email Anda.');
    }

    public function verificationNotice()
    {
        if (Auth::user() && Auth::user()->hasVerifiedEmail()) {
            return redirect('/');
        }

        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Tautan verifikasi tidak valid atau telah kedaluwarsa.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));

            ActivityLog::record(
                action: 'email_verified',
                description: "User {$user->name} ({$user->email}) berhasil memverifikasi email.",
                model: $user
            );
        }

        if (Auth::check()) {
            return redirect('/')->with('success', 'Email berhasil diverifikasi! Selamat datang.');
        }

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Silakan masuk.');
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Tautan verifikasi email baru telah dikirimkan ke email Anda.');
    }

    // ================= BAGIAN LUPA & RESET PASSWORD (SECURE TOKEN VIA NOTIFIKASI) =================
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function storeForgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Menggunakan broker bawaan Laravel Notification (kirim token ke email yang terdaftar)
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            ActivityLog::record(
                action: 'forgot_password_request',
                description: "Permintaan link reset password dikirim ke email {$request->email}."
            );

            return back()->with('success', 'Tautan reset password telah dikirimkan ke email Anda! Silakan cek kotak masuk (atau log sistem pada mode lokal).');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function storeResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                ActivityLog::record(
                    action: 'password_reset_success',
                    description: "Password user {$user->name} ({$user->email}) berhasil direset via token email.",
                    model: $user
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password Anda berhasil direset! Silakan login dengan password baru.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    // ================= BAGIAN GANTI PASSWORD (USER LOGIN) =================
    public function changePassword()
    {
        if (! Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('auth.change-password');
    }

    public function storeChangePassword(Request $request)
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password_baru' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password_lama.current_password' => 'Password lama Anda tidak sesuai.',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        ActivityLog::record(
            action: 'change_password',
            description: "User {$user->name} berhasil mengubah password dari dashboard.",
            model: $user
        );

        return back()->with('success', 'Password berhasil diubah!');
    }

    // ================= BAGIAN LOGOUT =================
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            ActivityLog::record(
                action: 'logout',
                description: "User {$user->name} ({$user->email}) telah keluar dari sistem.",
                model: $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Anda telah berhasil logout.')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Sun, 02 Jan 1990 00:00:00 GMT',
            ]);
    }
}
