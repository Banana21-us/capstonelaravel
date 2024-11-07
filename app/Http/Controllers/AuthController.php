<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            "password"=> "required",
            // "password_confirmation" => "required"

        ]);
        $formField['password'] = bcrypt($formField['password']);

        Admin::create($formField);
    
        return response()->json(['message' => 'Registration successful'], 201);

    }
    public function updatePass(Request $request)
{
    // Validate incoming request
    $request->validate([
        'admin_id' => 'required|integer|exists:admins,admin_id',
        'oldPassword' => 'nullable|string', // Make oldPassword optional
        'newPassword' => 'nullable|string|min:8|confirmed', // Allow newPassword to be optional
        'fname' => 'required|string|max:255',
        'mname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:admins,email,' . $request->admin_id . ',admin_id', // Check uniqueness for email
        'address' => 'required|string|max:255',
    ]);

    // Retrieve user
    $user = Admin::find($request->admin_id);

    // If old password is provided, check it
    if ($request->oldPassword && !Hash::check($request->oldPassword, $user->password)) {
        return response()->json(['message' => 'Wrong password'], 401);
    }

    // Update user details
    if ($request->newPassword) {
        $user->password = Hash::make($request->newPassword); // Update password if provided
    }
    
    $user->fname = $request->fname;
    $user->mname = $request->mname;
    $user->lname = $request->lname;
    $user->email = $request->email;
    $user->address = $request->address;

    $user->save(); // Save all changes

    return response()->json(['message' => 'User details updated successfully']);
    }
    public function uploadImage(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'admin_id' => 'required|exists:admins,admin_id'
    ]);

    try {
        $admin = Admin::findOrFail($request->input('admin_id'));
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('assets/adminPic');

        // Ensure the directory exists
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Delete the old image if exists
        if ($admin->admin_pic && file_exists($path = $destinationPath . '/' . $admin->admin_pic)) {
            unlink($path);
        }

        // Move the new image and update the admin profile
        $image->move($destinationPath, $imageName);
        $admin->update(['admin_pic' => $imageName]);

        return response()->json([
            'message' => 'Image uploaded successfully.',
            'image_url' => url('assets/adminPic/' . $imageName)
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Image upload failed.'], 500);
    }
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
