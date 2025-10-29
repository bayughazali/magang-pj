<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the admins.
     */
    public function index()
    {
        // Hanya tampilkan user dengan role admin
        $admins = User::where('role', 'admin')
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('admin.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        return redirect()->route('admins.index')
                        ->with('success', 'Admin berhasil ditambahkan!');
    }

    /**
     * Display the specified admin.
     */
    public function show(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('admin.edit', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'role' => ['required', 'in:admin'],
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'admin', // Pastikan selalu admin
        ]);

        return redirect()->route('admins.index')
                        ->with('success', 'Data admin berhasil diperbarui!');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        // Cegah admin menghapus dirinya sendiri
        if (auth()->id() === $admin->id) {
            return redirect()->route('admins.index')
                           ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // Hapus foto profil jika ada
        if ($admin->profile_photo_path && file_exists(storage_path('app/public/' . $admin->profile_photo_path))) {
            unlink(storage_path('app/public/' . $admin->profile_photo_path));
        }

        if ($admin->photo && file_exists(storage_path('app/public/photo/' . $admin->photo))) {
            unlink(storage_path('app/public/photo/' . $admin->photo));
        }

        $admin->delete();

        return redirect()->route('admins.index')
                        ->with('success', 'Admin berhasil dihapus!');
    }
}
