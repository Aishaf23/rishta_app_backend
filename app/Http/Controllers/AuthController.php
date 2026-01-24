<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;


class AuthController extends Controller
{
    /**
     * Register new user
     */

public function register(Request $req)
{
    // Check if email already exists
    if (User::where('email', $req->email)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Email already exists',
        ], 409);
    }

    // Validate request
    $validator = Validator::make($req->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'gender' => 'nullable|string|max:50',
        'religion' => 'nullable|string|max:50',
        'community' => 'nullable|string|max:50',
        'livingIn' => 'nullable|string|max:100',
        'mobile' => 'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation Error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Create user
    $user = User::create([
        'name' => $req->name,
        'email' => $req->email,
        'password' => bcrypt($req->password),
        'gender' => $req->gender,
        'religion' => $req->religion,
        'community' => $req->community,
        'livingIn' => $req->livingIn,
        'mobile' => $req->mobile,
    ]);

    // Create token
    $token = $user->createToken('MyAuthApp')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'User created successfully.',
        'token' => $token,
        'user_data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'gender' => $user->gender,
            'religion' => $user->religion,
            'community' => $user->community,
            'livingIn' => $user->livingIn,
            'mobile' => $user->mobile,
            'password' => $user->password,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]
    ], 201);
}



    /**
     * Login user
     */
    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $req->email)->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('MyAuthApp')->plainTextToken;

        return response()->json([
    'success' => true,
    'message' => 'Login successful',
    'token' => $token,
    'user_data' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'password' => $user->password,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ]
], 200);

    }

    /**
     * Logout user (optional)
     */
    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }


    /**
     * Send password reset link
     */
   

public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email'
    ]);

    $otp = rand(1000, 9999);

    User::where('email', $request->email)->update([
        'verified_code' => $otp
    ]);

    try {
        Mail::to($request->email)->send(
            new UserForgotPassword($request->email, $otp)
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP'
        ], 500);
    }
}


public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required'
    ]);

    $user = User::where('email', $request->email)
        ->where('verified_code', $request->otp)
        ->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP'
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => 'OTP verified successfully'
    ], 200);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required',
        'password' => 'required|min:8',
        'cnf_pass' => 'required|same:password'
    ]);

    $user = User::where('email', $request->email)
        ->where('verified_code', $request->otp)
        ->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP or email'
        ], 400);
    }

    $user->update([
        'password' => bcrypt($request->password),
        'verified_code' => null
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Password reset successfully'
    ], 200);
}



}
