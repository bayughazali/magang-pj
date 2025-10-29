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
    // Semua user bisa lihat daftar user
    $users = User::orderBy('created_at', 'desc')->paginate(10);

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

public function edit(User $user)
{
    // Hanya admin yang bisa edit
    if (auth()->user()->role !== 'admin') {
        return redirect()->route('users.index')
                       ->with('error', 'Anda tidak memiliki akses untuk mengedit user!');
    }

    return view('users.edit', compact('user'));
}

public function update(Request $request, User $user)
{
    // Hanya admin yang bisa update
    if (auth()->user()->role !== 'admin') {
        return redirect()->route('users.index')
                       ->with('error', 'Anda tidak memiliki akses untuk mengubah data user!');
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8|confirmed',
        'role' => 'required|in:admin,user',
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
        'role' => $request->role,
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

    try {
        // Hapus foto jika ada
        if ($user->photo && Storage::exists('public/photo/' . $user->photo)) {
            Storage::delete('public/photo/' . $user->photo);
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
