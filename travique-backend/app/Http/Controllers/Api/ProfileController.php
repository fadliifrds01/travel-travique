<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(Request $request) {
        $user = $request->user()->only(['name', 'username', 'email', 'role']);
        //  $user = User::select('name', 'username', 'email', 'role')->get();
        return response()->json([
            'success' => true,
            'message' => "Profile Pengguna Berhasil Diambil",
            'data' => $user,
        ], 200);
    }

}
