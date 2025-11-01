<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       $fields =  $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password'=>'required|string|confirmed',
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name);

        return [
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ];

    }

    public function login(Request $request)
    {
        $fields =  $request->validate([
            'email'=>'required|string|exists:users',
            'password'=>'required',
        ]);

        $user = User::where('email',$fields['email'])->first();
        if(!$user || !Hash::check($request->password,$user->password)){
           return ['message'=>'Bad creds'];
        }

        $token = $user->createToken($user->name);

        return [
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ];

    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ['message'=>'Logged out'];
    }
}
