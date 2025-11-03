<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // max 2MB
        ]);

        try {
            // Handle foto profile
            if ($request->hasFile('profile_photo')) {
                Log::info('Processing profile photo upload for user: ' . $user->id);

                // Pastikan direktori profile_photos ada
                if (!Storage::disk('public')->exists('profile_photos')) {
                    Storage::disk('public')->makeDirectory('profile_photos');
                }

                // Hapus foto lama jika ada
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                    Log::info('Deleted old profile photo: ' . $user->profile_photo_path);
                }

                // Hapus foto lama di folder photo jika ada (untuk kompatibilitas)
                if ($user->photo && Storage::disk('public')->exists('photo/' . $user->photo)) {
                    Storage::disk('public')->delete('photo/' . $user->photo);
                    Log::info('Deleted old photo from photo folder: ' . $user->photo);
                }

                // Simpan foto baru
                $file = $request->file('profile_photo');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_photos', $filename, 'public');

                Log::info('New profile photo saved: ' . $path);

                // Update field foto
                $user->profile_photo_path = $path;
            }

            // Update informasi user lainnya
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            Log::info('Profile updated successfully for user: ' . $user->id);

            return redirect()->route('profile.show')->with('success', 'Profile berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui profile: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete profile photo.
     */
    public function deletePhoto()
    {
        try {
            $user = Auth::user();

            Log::info('Deleting profile photo for user: ' . $user->id);

            // Reset field foto
            $user->profile_photo_path = null;
            $user->save();

            Log::info('Profile photo deleted successfully for user: ' . $user->id);

            return redirect()->route('profile.show')->with('success', 'Foto profile berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting profile photo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the change password form.
     */
    public function changePasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Update the user's password.
     */
    // public function updatePassword(Request $request)
    // {
    //     $request->validate([
    //         'current_password' => ['required'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);

    //     $user = Auth::user();

    //     if (!Hash::check($request->current_password, $user->password)) {
    //         return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
    //     }

    //     try {
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         Log::info('Password updated successfully for user: ' . $user->id);

    //         return redirect()->route('profile.show')->with('success', 'Password berhasil diubah!');
    //     } catch (\Exception $e) {
    //         Log::error('Error updating password: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah password: ' . $e->getMessage());
    //     }
    
}
