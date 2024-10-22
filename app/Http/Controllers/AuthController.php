<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
        $formField = $request->validate([
            "fname"=> "required|max:255",
            "lname"=> "required|max:255",
            "mname"=> "required|max:255",
            "role"=> "required|max:255",
            "address"=> "required|max:255",
            "email"=> "required|email|unique:admins",
            "password"=> "required"

        ]);
        $formField['password'] = bcrypt($formField['password']);

        Admin::create($formField);
    
        return response()->json(['message' => 'Registration successful'], 201);

    }
    public function login(Request $request)
    {
        // $request->validate([
        //     "email" => "required|email|exists:admins,email",
        //     "password" => "required"
        // ]);
        // $admin = Admin::where('email', $request->email)->first();
        // if (!$admin || !Hash::check($request->password, $admin->password)) {
        //     throw ValidationException::withMessages([
        //         'email' => ['The provided credentials are incorrect.'],
        //     ]);
        // }
        // if ($admin->role !== 'Principal') {
        //     return response()->json(['message' => 'Unauthorized: Only Principals can log in.'], 403);
        // }
        // $token = $admin->createToken($admin->fname)->plainTextToken;

        // return response()->json([
        //     'admin' => $admin,
        //     'token' => $token
        // ]);

        //

        $request->validate([
            "email"=>"required|email|exists:admins",
            "password"=>"required"
        ]);
        $admin = Admin::where('email',$request->email)->first();
        if(!$admin|| !Hash::check($request->password,$admin->password)){
            return [
                "message"=>"The provider credentials are incorrect"
            ];
        }
        $token = $admin->createToken($admin->fname);
        // $token = $admin->createToken($admin->fname)->plainTextToken;

        return [
            'admin' => $admin,
            'token' => $token->plainTextToken,
            'id'=> $admin->admin_id
        ];


    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message'=>'You are logged out'
        ];
        // return 'logout';
    }
}
