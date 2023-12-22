<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Produk;
use App\Models\ProdukPhoto;
use Illuminate\Http\Request;

class ProdukPhotoController extends Controller
{
    function store_photo(Produk $produk, Request $request) {
        // $post = $request->post();
        $photo = $request->file('photo');
        // dd($photo);
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);


        $produk_photos = ProdukPhoto::where('produk_id', $produk->id)->get();
        $count_produk_photos = count($produk_photos);

        $role = 'subsidiary';
        if ($count_produk_photos === 0) {
            $role = 'default';
        } else if ( $count_produk_photos > 0 && $count_produk_photos < 5 ) {
            $role = 'subsidiary';
        } else {
            return back()->with('error_', '-max foto produk: 5 foto-');
        }

        $file_name = time() . "." . $photo->extension();
        // dd($file_name);
        $photo->storeAs('product_photos/', $file_name);

        $new_photo = Photo::create([
            'path' => "product_photos/$file_name"
        ]);

        ProdukPhoto::create([
            "produk_id" => $produk->id,
            "photo_id" => $new_photo->id,
            'role' => $role
        ]);

        return back()->with('success_', '-photo product stored-');

    }

    function delete_photo(Produk $produk, ProdukPhoto $produk_photo, Photo $photo) {
        // dump($produk);
        // dd($photo);
        // cek apakah ada produk yang menggunakan foto yang sama?
        $warnings_ = '';
        $success_ = '';
        $other_produk_photos = ProdukPhoto::where('photo_id', $photo->id)->where('produk_id','!=',$produk->id)->get();

        $is_photo_default = $produk_photo->role;

        if (count($other_produk_photos) === 0) {
            $produk_photo->delete();
            $warnings_ .= '-produk_foto dihapus-';
            $photo->delete();
            $warnings_ .= '-foto dihapus-';
        } else {
            $produk_photo->delete();
            $warnings_ .= '-terdapat produk yang menggunakan foto yang sama, file foto tidak dihapus-';
        }


        if ($is_photo_default === 'default') {
            $produk_photo_aktual = ProdukPhoto::where('produk_id', $produk->id)->first();
            if ($produk_photo_aktual !== null) {
                $produk_photo_aktual->role = 'default';
                $produk_photo_aktual->save();
                $success_ .= '-another photo set to default-';
            }
        }

        $feedback = [
            'warnings_' => $warnings_,
            'success_' => $success_
        ];
        return back()->with($feedback);
    }

    function jadikan_default(Produk $produk, ProdukPhoto $produk_photo, Photo $photo, Request $request) {
    }
}
