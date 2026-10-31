<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    // Tampilkan daftar semua user (Hanya Admin)
    public function index()
    {
        $users = User::orderBy('created_at')->orderBy('id')->get();

        return view('admin.users.index', compact('users'));
    }

    // Form Tambah User Baru (Admin)
    public function create()
    {
        return view('admin.users.create');
    }

    // Simpan User Baru (Admin)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,kasir',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(), // User yang dibuat langsung oleh admin ditandai terverifikasi
        ]);

        ActivityLog::record(
            action: 'create_user',
            description: 'Admin '.auth()->user()->name." membuat akun pengguna baru: {$user->name} ({$user->email}, role: {$user->role}).",
            model: $user
        );

        return redirect()->route('users.index')->with('success', 'User baru '.$request->name.' ('.ucfirst($request->role).') berhasil dibuat!');
    }

    // Hapus user (Hanya Admin)
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cegah admin menghapus diri sendiri
        if ($user->id === auth()->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        ActivityLog::record(
            action: 'delete_user',
            description: 'Admin '.auth()->user()->name." menghapus akun pengguna: {$user->name} ({$user->email}).",
            model: $user
        );

        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }

    // Set/Ganti Password untuk user (Admin)
    public function setPassword($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.set-password', compact('user'));
    }

    public function storeSetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record(
            action: 'admin_set_password',
            description: 'Admin '.auth()->user()->name." mengatur password baru secara manual untuk user: {$user->name}.",
            model: $user
        );

        return redirect()->route('users.index')->with('success', 'Password user '.$user->name.' berhasil diubah!');
    }

    // Kirim tautan reset password user melalui email
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        try {
            $status = Password::broker()->sendResetLink(['email' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim tautan reset password.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);

            return back()->with('error', 'Tautan reset password gagal dikirim.');
        }

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->with('error', 'Tautan reset password gagal dikirim.');
        }

        ActivityLog::record(
            action: 'admin_reset_password',
            description: 'Admin '.auth()->user()->name." mereset password untuk user {$user->name}.",
            model: $user
        );

        return back()->with('success', "Tautan reset password telah dikirim ke {$user->email}.");
    }

    // Edit user (Hanya Admin)
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $displayId = User::where(function ($query) use ($user) {
            $query->where('created_at', '<', $user->created_at)
                ->orWhere(function ($sameTimestamp) use ($user) {
                    $sameTimestamp->where('created_at', $user->created_at)
                        ->where('id', '<=', $user->id);
                });
        })->count();

        return view('admin.users.edit', compact('user', 'displayId'));
    }

    // Update user (Hanya Admin)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,kasir',
        ]);

        $oldData = $user->only(['name', 'email', 'role']);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        ActivityLog::record(
            action: 'update_user',
            description: 'Admin '.auth()->user()->name." memperbarui profil user: {$user->name}.",
            model: $user,
            oldValues: $oldData,
            newValues: $user->only(['name', 'email', 'role'])
        );

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }
}
