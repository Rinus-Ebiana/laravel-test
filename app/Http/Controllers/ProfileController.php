<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule; // <-- WAJIB di-import
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil.
     */
    public function edit()
    {
        return view('profile');
    }

    /**
     * Update data profil (username dan/atau password).
     * (Sebelumnya bernama updatePassword)
     */
    public function update(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                // Cek unik di tabel 'users', tapi abaikan ID user saat ini
                Rule::unique('users')->ignore(Auth::user()->id),
            ],
            'password_baru' => [
                'nullable', // Password sekarang opsional
                'string',
                'confirmed', // Akan otomatis mencocokkan 'password_baru_confirmation'
                Password::min(8)
                    ->mixedCase() // huruf besar dan kecil
                    ->numbers()   // angka
                    ->symbols(),  // simbol
            ],
        ]);

        // 2. Ambil user yang sedang login
        $user = Auth::user();
        
        // 3. Update username
        $user->username = $request->username;

        // 4. Update password HANYA JIKA diisi
        if ($request->filled('password_baru')) {
            $user->password = Hash::make($request->password_baru);
        }

        // 5. Simpan perubahan ke database
        $user->save();

        // 6. Redirect kembali dengan pesan sukses
        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}