<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Photo;
use App\Models\Produk;
use App\Models\ProdukPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukPhotoController extends Controller
{
    function add_photo(Produk $produk) {
        $all_products = Produk::all(['id', 'nama']);
        $all_product_photos = array();

        foreach ($all_products as $product) {
            $product_photos = ProdukPhoto::where('produk_id', $product->id)->get();
            $photos_data = array();
            foreach ($product_photos as $product_photo) {
                $photo = Photo::find($product_photo->photo_id);
                $photos_data[] = [
                    'id' => $photo->id,
                    'path' => $photo->path
                ];
            }
            $all_product_photos[] = [
                'id' => $product->id,
                'label' => $product->nama,
                'value' => $product->nama,
                'photos_data' => $photos_data
            ];
        }

        $produk_photos = ProdukPhoto::where('produk_id', $produk->id)->get();
        $photos = array();

        foreach ($produk_photos as $produk_photo) {
            $photo = Photo::find($produk_photo->photo_id);
            $photos[] = $photo;
        }

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'produks.index',
            'parent_route' => 'home',
            'profile_menus' => Menu::get_profile_menus(),
            'spk_menus' => Menu::get_spk_menus(),
            'produk' => $produk,
            'produk_photos' => $produk_photos,
            'photos' => $photos,
            'all_product_photos' => $all_product_photos,
        ];
        // dd($produk_photos);
        // dd($photos[0]->path);
        return view('produks.add-photo', $data);
    }

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
        $error_ = '';
        $other_produk_photos = ProdukPhoto::where('photo_id', $photo->id)->where('produk_id','!=',$produk->id)->get();

        $is_photo_default = $produk_photo->role;

        if (count($other_produk_photos) === 0) {
            if (Storage::exists($photo->path)) {
                Storage::delete($photo->path);
                $warnings_ .= '-file foto deleted-';
            } else {
                $error_ .= "-Storage::$photo->path tidak ditemukan-";
            }

            $produk_photo->delete();
            $warnings_ .= '-produk_foto deleted-';
            $photo->delete();
            $warnings_ .= '-foto deleted-';
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

    function jadikan_default(Produk $produk, ProdukPhoto $produk_photo) {
        // cek apakah ada foto default, jika ada, jadikan subsidiary terlebih dahulu.
        $success_ = '';
        $is_exist_default = ProdukPhoto::where('produk_id', $produk->id)->where('role', 'default')->get();
        $count_is_exist_default = count($is_exist_default);

        if ($count_is_exist_default > 0) {
            foreach ($is_exist_default as $key => $default_photo) {
                $default_photo->role = 'subsidiary';
                $default_photo->save();
            }
            $success_ .= '-semua default foto menjadi subsidiary-';
        }

        $produk_photo->role = "default";
        $produk_photo->save();
        $success_ .= '-foto terkait dijadikan default-';

        $feedback = [
            'success_' => $success_
        ];

        return back()->with($feedback);
    }

    function jadikan_subsidiary(Produk $produk, ProdukPhoto $produk_photo) {
        $success_ = '';

        $produk_photo->role = 'subsidiary';
        $produk_photo->save();

        $success_ .= '-foto dijadikan subsidiary-';

        $feedback = [
            'success_' => $success_
        ];

        return back()->with($feedback);
    }

    function add_photo_from_other_product(Produk $produk, Request $request) {
        // dump($produk);
        $post = $request->post();
        // dd($post);
        // cek apakah produk ini sudah punya foto? Ada berapa foto?
        $cek_produk_photos = ProdukPhoto::where('produk_id', $produk->id)->get();
        $count_cek_produk_photos = count($cek_produk_photos);

        if (($count_cek_produk_photos + count($post['photo_id'])) > 5) {
            $request->validate(['error'=>'required'],['error.required'=>'-jumlah foto lebih dari 5-']);
        }

        foreach ($post['photo_id'] as $key => $photo_id) {
            $role = 'subsidiary';

            if ($count_cek_produk_photos === 0 && $key === 0) {
                $role = 'default';
            }

            ProdukPhoto::create([
                'produk_id' => $produk->id,
                'photo_id' => $photo_id,
                'role' => $role,
            ]);
        }

        $success_ = '-produk_photo created-';

        $feedback = [
            'success_' => $success_
        ];

        return redirect()->route('produks.show', $produk->id)->with($feedback);
    }
}
