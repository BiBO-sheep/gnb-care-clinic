<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'blood_type' => 'nullable|string|max:5',
            'height' => 'nullable|string',
            'weight' => 'nullable|string',
            'password' => 'nullable|string|min:8'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('address')) $user->address = $request->address;
        if ($request->has('blood_type')) $user->blood_type = $request->blood_type;
        if ($request->has('height')) $user->height = $request->height;
        if ($request->has('weight')) $user->weight = $request->weight;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ]);
    }
}
