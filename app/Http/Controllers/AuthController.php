<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $post = $request->post();
        /**
         * Sebelum proses login menyamakan password
         * cek terlebih dahulu apakah user exist?
         * lalu apakah user merupakan admin?
         */
        $user = User::where('username',$post['username'])->first();
        // dd($user);
        if ($user === null) {
            $request->validate(['error'=>'required'],['error.required'=>'none']);
        }
        // if (!User::apakah_admin($user->id)) {
        //     $request->validate(['error'=>'required'],['error.required'=>'no enou']);
        // }
        $credentials=$request->validate([
            "username"=>"required",
            "password"=>"required"
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->with('failed_', 'Login gagal!');
        // return back()->withErrors([
        //     'email' => 'The provided credentials do not match our records.',
        // ])->onlyInput('email');

    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('login'));
    }

    public function home()
    {
        // dd('goback');
        return view('app');
    }

    public function register() {
        $data = [
            'goback' => 'home',
        ];
        return view('login.register', $data);
    }

    function register_db(Request $request) {
        $post = $request->post();
        // dd($post);
        $request->validate([
            'nama'=>'required|string',
            'username'=>'required|string',
            'password'=>'required|string',
            'confirm_password'=>'required|string',
        ]);

        if ($post['password'] !== $post['confirm_password']) {
            $request->validate(['error'=>'required'],['error.required'=>'3']);
        }
        User::create([
            'nama'=>$post['nama'],
            'username'=>$post['username'],
            'password'=>bcrypt($post['password']),
        ]);

        return back()->with('success_','new user.');
    }
}
