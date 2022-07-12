<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Session;

class AuthController extends Controller
{

    public function register() {
        return view ("auth.register");
    }

    public function login() {
        return view ("auth.login");
    }

    public function dashboard() {
        return view('dashboard');
    }
    
    public function registerUser(Request $request) {
        $fields = $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            

        ]);

        $user = User::create([
            'firstName' => $fields['firstName'],
            'lastName' => $fields['lastName'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            

        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        //return response($response, 201);

        if($response) {
            return back()->with('success', 'You have registered successfuly');
        } else {
            return back()->with('fail', 'Something Wrong');
        }
    }


    //Login
    public function loginUser(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check Email
        $user = User::where('email', $fields['email'])->first();

        // Check Password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return back()->with('fail', 'Something Wrong');
            // return response([
            //     'message' => 'Bad Creds'
            // ], 401);
        } else {
            return redirect('dashboard');
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        //return response($response, 201);
       
    }
    
    // Logout
    public function logout(Request $request) {
        //auth()->user()->tokens()->delete();

        return redirect('login');
    }   
}