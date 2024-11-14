<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{

    public function register(Request $request)
    {
        Log::info('Starting registration process.');
    
        $formField = $request->validate([
            "fname" => "required|max:255",
            "lname" => "required|max:255",
            "mname" => "required|max:255",
            "role" => "required|max:255",
            "address" => "required|max:255",
            "email" => "required|email|unique:admins",
            "password" => "required",
        ]);
    
        Log::info('Validation successful.', ['formField' => $formField]);
    
        $formField['password'] = bcrypt($formField['password']);
        Log::info('Password encrypted.');
    
        try {
            $admin = Admin::create($formField);
            Log::info('Admin created successfully.', ['admin_id' => $admin->id]);
        } catch (\Exception $e) {
            Log::error('Error creating admin.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Registration failed'], 500);
        }
    
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

    // message
    public function getMessages(Request $request){
        $uid = $request->input('uid');

        $latestMessages = DB::table('messages')
            ->select('message_sender', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('message_sender');
    
        $msg = DB::table('messages')
            ->leftJoin('students', function ($join) {
                $join->on('messages.message_sender', '=', 'students.LRN');
            })
            ->leftJoin('parent_guardians', function ($join) {
                $join->on('messages.message_sender', '=', 'parent_guardians.guardian_id');
            })
            ->joinSub($latestMessages, 'latest_messages', function ($join) {
                $join->on('messages.message_sender', '=', 'latest_messages.message_sender')
                    ->on('messages.created_at', '=', 'latest_messages.max_created_at');
            })
            ->whereNotIn('messages.message_sender', function ($query) {
                $query->select('admin_id')->from('admins');
            })
            ->where('messages.message_reciever', '=', $uid)
            ->select('messages.*', 
                DB::raw('CASE 
                    WHEN messages.message_sender IN (SELECT LRN FROM students) THEN CONCAT(students.fname, " ",LEFT(students.mname, 1), ". ", students.lname)
                    WHEN messages.message_sender IN (SELECT guardian_id FROM parent_guardians) THEN CONCAT(parent_guardians.fname, " ",LEFT(parent_guardians.mname, 1), ". ", parent_guardians.lname)
                END as sender_name'))
            ->orderBy('messages.created_at', 'desc')
            ->get();
        
        return $msg;
    }
    public function getConvo(Request $request, $sid){
        $uid = $request->input('uid');

        $convo = DB::table('messages')
            ->leftJoin('students', function ($join) {
                $join->on('messages.message_sender', '=', 'students.LRN');
            })
            ->leftJoin('admins', function ($join) {
                $join->on('messages.message_sender', '=', 'admins.admin_id');
            })
            ->leftJoin('parent_guardians', function ($join) {
                $join->on('messages.message_sender', '=', 'parent_guardians.guardian_id');
            })
            ->where(function ($query) use ($uid) {
                $query->where('messages.message_sender', $uid) // Sent messages
                      ->orWhere('messages.message_reciever', $uid); // Received replies
            })     
            ->where(function ($query) use ($sid) {
                $query->where('messages.message_sender', $sid) // Sent messages
                      ->orWhere('messages.message_reciever', $sid); // Received replies
            })        
            ->select('messages.*', 
                DB::raw('CASE 
                    WHEN messages.message_sender IN (SELECT LRN FROM students) THEN CONCAT(students.fname, " ",LEFT(students.mname, 1), ". ", students.lname)
                    WHEN messages.message_sender IN (SELECT guardian_id FROM parent_guardians) THEN CONCAT(parent_guardians.fname, " ",LEFT(parent_guardians.mname, 1), ". ", parent_guardians.lname)
                END as sender_name'),
                DB::raw('CASE 
                    WHEN messages.message_sender IN (SELECT admin_id FROM admins) THEN CONCAT(admins.fname, " ",LEFT(admins.mname, 1), ". ", admins.lname)
                END as me'))
            ->get();
            if ($convo->isEmpty()) {
                return response()->json(['message' => 'No messages found'], 404);
            }

        return $convo;
    }
    public function sendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message_sender' => 'required',
            'message_reciever' => 'required',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $message = Message::create([
            'message_sender' => $request->input('message_sender'), // Ensure the key matches your database column
            'message_reciever' => $request->input('message_reciever'), // Ensure the key matches your database column
            'message' => $request->input('message'), // Ensure the key matches your database column
            'message_date' => now(),
        ]);

        return response()->json($message, 201);
    }
    public function getrecepeints(Request $request)
    {
     $students = DB::table('students')
     ->select(DB::raw('LRN AS receiver_id, CONCAT(fname, " ", lname) AS receiver_name'));
    $guardians = DB::table('parent_guardians')
        ->select(DB::raw('guardian_id AS receiver_id, CONCAT(fname, " ", lname) AS receiver_name'));
    $admins = DB::table('admins')
        ->select(DB::raw('admin_id AS receiver_id, CONCAT(fname, " ", lname) AS receiver_name'));
    $recipients = $students->unionAll($guardians)->unionAll($admins)->get();
    return response()->json($recipients);
    }

    public function composenewmessage(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'message_date' => 'required|date',
            'message_sender' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInStudents = DB::table('students')->where('LRN', $value)->exists();
                    $existsInGuardians = DB::table('parent_guardians')->where('guardian_id', $value)->exists();
                    $existsInAdmins = DB::table('admins')->where('admin_id', $value)->exists();
    
                    if (!$existsInStudents && !$existsInGuardians && !$existsInAdmins) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
            'message_reciever' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInStudents = DB::table('students')->where('LRN', $value)->exists();
                    $existsInGuardians = DB::table('parent_guardians')->where('guardian_id', $value)->exists();
                    $existsInAdmins = DB::table('admins')->where('admin_id', $value)->exists();
    
                    if (!$existsInStudents && !$existsInGuardians && !$existsInAdmins) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
        ]);
    
        try {
            // Create a new message
            $message = new Message();
            $message->message_sender = $validated['message_sender'];
            $message->message_reciever = $validated['message_reciever'];
            $message->message = $validated['message'];
            $message->message_date = $validated['message_date'];
            $message->save();
    
            // Log a success message
            Log::info('Message successfully composed', [
                'message_id' => $message->message_id,
                'sender' => $validated['message_sender'],
                'receiver' => $validated['message_reciever'],
                'message_content' => $validated['message'],
                'message_date' => $validated['message_date'],
            ]);
    
            // Return the updated list of messages
            return $this->getMessages($request);  // Call getMessages method to return updated conversation
        } catch (\Exception $e) {
            // Log any error that occurs
            Log::error('Error sending message: ' . $e->getMessage());
    
            // Return an error response
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }
    




    

}
