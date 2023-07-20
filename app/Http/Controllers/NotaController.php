<?php

namespace App\Http\Controllers;

use App\Models\SpkProduk;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    function create_or_edit_jumlah_spk_produk_nota(SpkProduk $spk_produk, Request $request) {
        $post = $request->post();
        dump($post);
        dd($spk_produk);
    }
}
