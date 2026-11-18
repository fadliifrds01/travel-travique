<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Function Register
        $validatorRegister = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_num', 'min:5','max:20', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'phone' => ['required', 'string', 'min:10', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            // role sudah di tentukan otomatis
        ]);

        if ($validatorRegister->fails()) { // Validasi register gagal
            return response()->json([
                'success' => false,
                'message' => 'Validasi Register Gagal',
                'errors' => $validatorRegister->errors()
            ], 422); // Response register gagal
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' =>'user', // Role otomatis menjadi user
            'password' => $request->password // Password sudah di hash di model
        ]); // Untuk menyimpan data user ke DB

        $createToken = $user->createToken('auth_token')->plainTextToken; // Membuat token otomatis

        return response()->json([
            'success' => true,
            'message' => 'Register Berhasil',
            'data' => [
                'user' => $user,
                'access_token' => $createToken,
                'token_type' => 'Bearer'
            ]
        ]); // Response JSON Register berhasil
    }



    public function login(Request $request)
    {
        // Function Login, Validasi Login, Cek Username / Email, Create Token.
        $validatorLogin = Validator::make($request->all(), [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]); // Validasi Input Form User

        if ($validatorLogin->fails()) { // Validasi ketika login gagal
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validatorLogin->errors()
            ], 422);
        } // Response JSON validasi gagal

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username'; // Cek login menggunakan email apa username
        $user = User::where($loginField, $request->login)->first(); // Untuk mencari akun user terdaftar apa tidak di database

        if (!$user || !Hash::check($request->password, $user->password)) { // Mengecek data pengguna apakah ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Login Gagal, Silahkah Cek Username / Email Dan Password Yang Dimasukkan',
            ], 401);
        }
        // $request->password : Mengecek Password Yang Dimasukkan
        // $user->password : Membandingkan hash password didalam database

        $createToken = $user->createToken('auth_token')->plainTextToken; // Membuat token otomatis setelah user Register Atau Login bertipe teks

        return response()->json([
                'success' => true,
                'message' => 'Login Berhasil',
                'data' => [
                    'user' => $user,
                    'access_token' => $createToken,
                    'token_type' => 'Bearer'
                ]
        ]); // Response JSON Login berhasil

    }


    // Function Logout, hapus token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete(); // Untuk menghapus token

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil, Token Dihapus.',
        ]);
    }
}


// Next Test API
// Next Tabel Destinasi
