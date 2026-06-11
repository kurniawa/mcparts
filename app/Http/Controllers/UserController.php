<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    function profile() {
        $user = Auth::user();
        $data = [
            // 'goback' => 'home',
            // 'user_role' => $user_role,
            'menus' => Menu::get(),
            'route_now' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'user' => $user,
        ];
        // dump(storage_path($user->profile_picture));
        // dd(file_exists(storage_path($user->profile_picture)));
        return view('user.profile', $data);
    }

    function update_nama(User $user, Request $request) {
        $post = $request->post();
        // dd($post);

        $user->nama = $post['nama'];
        $user->save();

        return back()->with('success_', '-nama updated-');
    }

    function update_username(User $user, Request $request) {
        $post = $request->post();
        // dd($post);
        // dump($post);
        // dd($user);
        $user->username = $post['username'];
        $user->save();

        return back()->with('success_', '-username updated-');
    }

    function update_password(Request $request) {
        // 1. Validasi input sesuai dengan field yang Anda post
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'], 
            'new_password'     => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()], // Otomatis mencari 'new_password_confirmation'
        ]);

        // 2. Ambil data user yang sedang login
        $user = User::find(Auth::id());

        // 3. Update password menggunakan field 'new_password'
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // 4. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('status', 'Password berhasil diperbarui.');
    }

    function update_photo(User $user, Request $request) {
        $post = $request->post();
        $photo = $request->file('photo');
        // dump($post);
        // dump($user);
        // dump(random_bytes(20));
        // dd($photo);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // DELETE PHOTO LAMA
        if ($user->profile_picture) {
            if (Storage::exists($user->profile_picture)) {
                Storage::delete($user->profile_picture);
            }
        }
        // END - DELETE PHOTO LAMA
        // $file_name = bin2hex(random_bytes(15)) . "." . $photo->extension();
        $file_name = time() . "." . $photo->extension();
        // dd($file_name);
        $photo->storeAs('profile_pictures/', $file_name);

        $user->profile_picture = "profile_pictures/$file_name";
        $user->save();

        return back()->with('success_', '-profile picture updated-');
    }

    function delete_profile_picture(User $user) {
        // DELETE PHOTO LAMA
        $error_ = '';
        $success_ = '';
        if ($user->profile_picture) {
            if (Storage::exists($user->profile_picture)) {
                Storage::delete($user->profile_picture);
            } else {
                $error_ .= "-$user->profile_picture tidak ditemukan-";
            }

            $user->profile_picture = null;
            $user->save();
            $success_ .= '-profile_picture updated-';
        }

        $feedback = [
            'error_' => $error_,
            'success_' => $success_,
        ];

        return back()->with($feedback);
        // END - DELETE PHOTO LAMA
    }
}
