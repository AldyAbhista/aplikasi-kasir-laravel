<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $setting = StoreSetting::getSetting();

        return view('admin.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'footer_message' => 'nullable|string|max:500',
            'qris_enabled' => 'boolean',
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'qris_image_remove' => 'nullable|boolean',
        ]);

        $setting = StoreSetting::getSetting();
        if ($request->boolean('qris_enabled') && ! $request->hasFile('qris_image') && ! $setting->qris_image) {
            return back()->withInput()->withErrors([
                'qris_image' => 'Upload gambar QRIS sebelum mengaktifkan pembayaran QRIS.',
            ]);
        }

        $oldValues = $setting->only(['store_name', 'address', 'phone', 'footer_message', 'qris_enabled', 'qris_image']);
        $qrisImage = $setting->qris_image;

        if ($request->hasFile('qris_image')) {
            if ($qrisImage) {
                $this->deleteQrisImage($qrisImage);
            }
            $qrisDirectory = public_path('qris');
            if (! is_dir($qrisDirectory) && ! mkdir($qrisDirectory, 0755, true) && ! is_dir($qrisDirectory)) {
                throw new \RuntimeException('Folder gambar QRIS tidak dapat dibuat.');
            }

            $extension = $request->file('qris_image')->extension();
            $fileName = Str::random(40).'.'.$extension;
            $request->file('qris_image')->move($qrisDirectory, $fileName);
            $qrisImage = 'qris/'.$fileName;
        } elseif ($request->boolean('qris_image_remove')) {
            if ($qrisImage) {
                $this->deleteQrisImage($qrisImage);
            }
            $qrisImage = null;
        }

        $setting->update([
            'store_name' => $request->store_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'footer_message' => $request->footer_message,
            'qris_enabled' => $request->boolean('qris_enabled'),
            'qris_image' => $qrisImage,
        ]);

        ActivityLog::record(
            action: 'update_settings',
            description: 'Admin '.auth()->user()->name." memperbarui profil/pengaturan toko {$setting->store_name}.",
            model: $setting,
            oldValues: $oldValues,
            newValues: $setting->only(['store_name', 'address', 'phone', 'footer_message', 'qris_enabled', 'qris_image'])
        );

        return redirect()->route('settings.index')->with('success', 'Pengaturan toko berhasil diperbarui!');
    }

    private function deleteQrisImage(string $path): void
    {
        $relativePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($path, '/\\'));
        $fullPath = public_path($relativePath);

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Halaman Log Aktivitas / Audit Trail Toko (Khusus Admin)
     */
    public function activityLogs(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(25)->withQueryString();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.activity-logs', compact('logs', 'actions'));
    }
}
