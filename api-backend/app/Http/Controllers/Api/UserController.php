<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    public function register(Request $request)
    {
        $request->validate(

            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => '1',
            'message' => 'User created successfully',
            'user' => $user,
        ]);
    }
        

    public function login(Request $request)
    {
        $request->validate(

            [
                'email' => 'required|string|email|max:255',
                'password' => 'required',
            ]
        );

        $user = User::where('email', $request->email)->first();

        if(isset($user->id)){
            if(Hash::check($request->password, $user->password)){

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => '1',
                    'message' => 'User logged in successfully',
                    'access_token' => $token,
                ]);

            }else{
                return response()->json([
                    'status' => '0',
                    'message' => 'Invalid password',
                ],404);
            }
        }else{

            return response()->json([
                'status' => '0',
                'message' => 'Unauthorized'
            ],404);
        }

    }

    public function profile(){

        return response()->json([
            'status' => '1',
            'message' => 'User profile',
            'data' => Auth::user(),
        ]);

    }

    public function logout()
    {
        // auth()->logout();

        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

  
}
