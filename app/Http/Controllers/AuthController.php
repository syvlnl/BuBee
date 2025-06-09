<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    function login() {
        if(Auth::check()) {
            return redirect(route('home'));
        }
        return view('login');
    }

    function registration() {
        if (Auth::check()) {
            return redirect(route('home'));
        }
        return view('registration');
    }

    function loginPost(Request $request) {
        $request -> validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        $cresidentials = $request -> only('email', 'password');
        if(Auth::attempt($cresidentials)) {
            return redirect() -> intended('/admin');
        }
        return redirect(route('login')) -> with("error", "Login details are not valid");
    }

    function registrationPost(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*\d).+$/'], [
            'password.regex' => 'Password must contain at least one uppercase letter and one number.',
            'password.min' => 'Password must be at least 8 characters.'
            ]);

            if ($validator->fails()) {
                return redirect(route('registration'))->withErrors($validator)->withInput();
            }

        $data['name'] = $request -> name;
        $data['email'] = $request -> email;
        $data['password'] = Hash::make($request -> password);
        $user = User::create($data);
        if(!$user) {
            return redirect(route('registration'))->with("error", "Registration failed. Try again.");
        }
        return redirect(route('login'))->with("success", "Registration success. Login to access the app.");
    }

    function logout() {
        Session::flush();
        Auth::logout();
        Return redirect(route('login'));
    }
}
