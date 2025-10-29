<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Jika admin login, tampilkan hanya user dengan role 'user'
        // Jika user biasa login, juga hanya tampilkan user dengan role 'user'
        // Data admin dikelola di AdminController
        $users = User::where('role', 'user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya admin yang bisa akses halaman create
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Anda tidak memiliki akses untuk menambah user!');
        }

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Hanya admin yang bisa create user
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Anda tidak memiliki akses untuk menambah user!');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user', // Hanya bisa create role user
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $photo = null;
        // Jika ada upload foto, simpan
        if ($request->hasFile('photo')) {
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('photo', $fileName, 'public');
            $photo = $fileName;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Paksa role jadi user
            'photo' => $photo,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // User biasa tidak bisa lihat detail admin
        if (auth()->user()->role !== 'admin' && $user->role === 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Anda tidak memiliki akses untuk melihat data ini!');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Hanya admin yang bisa edit
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Anda tidak memiliki akses untuk mengedit user!');
        }

        // Admin tidak bisa edit user yang role-nya admin (harus lewat route admins)
        if ($user->role === 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Untuk mengedit admin, gunakan menu Admin!');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Hanya admin yang bisa update
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Anda tidak memiliki akses untuk mengubah data user!');
        }

        // Admin tidak bisa edit user yang role-nya admin
        if ($user->role === 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Untuk mengedit admin, gunakan menu Admin!');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:user', // Hanya bisa update ke role user
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update data user
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'user', // Paksa tetap user
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle upload foto baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo && Storage::exists('public/photo/' . $user->photo)) {
                Storage::delete('public/photo/' . $user->photo);
            }

            // Upload foto baru
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('photo', $fileName, 'public');
            $userData['photo'] = $fileName;
        }

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Hanya admin yang bisa delete
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Anda tidak memiliki akses untuk menghapus user!');
        }

        // Admin tidak bisa hapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                           ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // Admin tidak bisa hapus user yang role-nya admin (harus lewat route admins)
        if ($user->role === 'admin') {
            return redirect()->route('users.index')
                           ->with('error', 'Untuk menghapus admin, gunakan menu Admin!');
        }

        try {
            // Hapus foto jika ada
            if ($user->photo && Storage::exists('public/photo/' . $user->photo)) {
                Storage::delete('public/photo/' . $user->photo);
            }

            // Hapus profile_photo_path jika ada
            if ($user->profile_photo_path && Storage::exists('public/' . $user->profile_photo_path)) {
                Storage::delete('public/' . $user->profile_photo_path);
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
