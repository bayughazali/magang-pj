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
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

         // ✅ Tambahkan default null agar tidak error
        $profilePicturePath = null;
        // ✅ Jika ada upload foto, simpan
        if ($request->hasFile('photo')) {
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('photo', $fileName, 'public');
            $photo = $fileName;  // <- disimpan ke $photo
        }


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Laravel Route Model Binding otomatis inject User model berdasarkan ID
        // Pastikan parameter di route adalah {user}, bukan {id}
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     * PERBAIKAN: Hapus kemampuan update password dan role terbatas untuk admin saja
     */
<<<<<<< HEAD
    public function update(Request $request, User $user)
=======
   /**
 * Update the specified resource in storage.
 */
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Buat aturan validasi yang dinamis
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'role' => 'required|in:admin,user',
        'photo'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    // ✅ Jika ada foto baru, hapus lama lalu simpan baru
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists('photo/' . $user->photo)) {
                Storage::disk('public')->delete('photo/' . $user->photo);
            }

            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('photo', $fileName, 'public');
            $user->photo = $fileName;
        }

    // Tambahkan validasi password hanya jika field diisi
    if ($request->filled('password')) {
        $rules['password'] = 'string|min:8|confirmed';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Data yang akan diupdate
    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'photo' => $user->photo, // Simpan nama file foto yang sudah diupdate
    ];

    // Tambahkan password ke data update hanya jika diisi
    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    $user->update($updateData);

    return redirect()->route('users.index')
        ->with('success', 'User berhasil diupdate!');
}


    public function destroy($id)
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
    {
        // Cek apakah user yang login adalah admin
        $isAdmin = auth()->user()->role === 'admin';
        
        // Validasi dasar
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ];
        
        // Tambahkan validasi role hanya jika user adalah admin
        if ($isAdmin) {
            $rules['role'] = 'required|in:admin,user';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Data yang akan diupdate
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        // Tambahkan role ke data update hanya jika user adalah admin
        if ($isAdmin && $request->has('role')) {
            $updateData['role'] = $request->role;
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Cegah user menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }
<<<<<<< HEAD

    /**
     * Method khusus untuk reset password (opsional - jika diperlukan)
     * Hanya admin yang bisa reset password user lain
     */
    public function resetPassword(User $user)
    {
        // Pastikan hanya admin yang bisa reset password
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Generate password sementara
        $temporaryPassword = 'temp' . rand(1000, 9999);
        
        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Password user {$user->name} berhasil direset. Password sementara: {$temporaryPassword}");
    }
}
=======
}
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
