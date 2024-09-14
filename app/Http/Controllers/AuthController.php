<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function register(Request $request){
        $formField = $request->validate([
            "name"=> "required|max:255",
            "email"=> "required|email|unique:users",
            "password"=> "required|confirmed"

        ]);
        User::create($formField);
        // return $request;
        return 'register';

    }
    public function login (Request $request){
        $request->validate([
            "email"=>"required|email|exists:users",
            "password"=>"required"
        ]);
        $user = User::where('email',$request->email)->first();
        if(!$user|| !Hash::check($request->password,$user->password)){
            return [
                "message"=>"The provider credentials are incorrect"
            ];
        }
        $token=$user->createToken($user->name);
        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
        // return 'login';
        // return $user;
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message'=>'You are logged out'
        ];
        // return 'logout';
    }
}
