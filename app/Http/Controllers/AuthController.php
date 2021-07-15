<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Register a new user
     *
     * @return Response
     */
    public function register(Request $request)
    {
        $validated = $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users,email',
            'password' => 'required|min:6'
        ]);
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();
        return response($user, 201);
    }

    public function login(Request $request)
    {
        $validated = $this->validate($request, [
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);
        $user = User::where('email', $validated['email'])->first();
        if (!Hash::check($validated['password'], $user->password)) {
            return response(['message' => 'email or password not valid'], 401);
        }
        $payload = [
            'iat' => intval(microtime(true)),
            'exp' => intval(microtime(true)) + (60 * 60 * 1000),
            'uid' => $user->id,
        ];
        $token = JWT::encode($payload, env('JWT_SECRET', 'secretaja'));
        return response(['access_token' => $token]);
    }

}
