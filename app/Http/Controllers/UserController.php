<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //
    function list(){
        return User::all();
    }


function updateUser(Request $req, $id)
{
    // 1️⃣ Check if user exists
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    // 2️⃣ Check email already exists (except this user)
    if ($req->email) {
        $emailExists = User::where('email', $req->email)
            ->where('id', '!=', $id)
            ->exists();

        if ($emailExists) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists'
            ], 409);
        }
    }

    // 3️⃣ Update fields
    $user->name = $req->name ?? $user->name;
    $user->email = $req->email ?? $user->email;
    $user->phone = $req->phone ?? $user->phone;

    if ($req->password) {
        $user->password = bcrypt($req->password);
    }

    $user->save();

    // 4️⃣ Response
    return response()->json([
        'success' => true,
        'message' => 'User updated successfully',
        'user' => $user
    ], 200);
}

function deleteUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $user->delete();

    return response()->json([
        'success' => true,
        'message' => 'User deleted successfully'
    ], 200);
}



function getUserById($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'user' => $user
    ], 200);
}


}