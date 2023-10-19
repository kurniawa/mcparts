$from = null;
$until = null;
$pembelians = PembelianTemp::orderBy('created_at')->get();
$a = 0;
dump($pembelians[0]);
dump($pembelians[count($pembelians) - 1]);
dd(count($pembelians));

while ($a < count($pembelians)) {
    // dump($a);
    if ($a === 0) {
        $pembelian = PembelianTemp::first();
        $from = date('Y-m-d', strtotime($pembelian->created_at));
        $from .= ' 23:59:59';
        // dump($from);
        $until = $from;
        $pembelian_tanggal_terkaits = PembelianTemp::where('created_at', '<=', $from)->get();
    } else {
        // dump($from);
        $pembelian_tanggal_terkaits = PembelianTemp::where('created_at', '>', $from)->where('created_at', '<=', $until)->orderBy('created_at')->get();
    }
    // dump($pembelian_tanggal_terkaits);
    $pembelian_grouped_suppliers = $pembelian_tanggal_terkaits->groupBy('supplier');
    foreach ($pembelian_grouped_suppliers as $pembelian_grouped_supplier) {
        // if ($key === 0) {
        //     dump(date('Y-m-d', strtotime($pembelian_tanggal_terkait->created_at)));
        // }
        // dump($count_pembelians++);
        $supplier = Supplier::where('nama', $pembelian_grouped_supplier[0]->supplier)->first();
        $nomor_nota = null;
        if ($supplier === null) {
            if (str_contains($pembelian_grouped_supplier[0]->supplier, 'MAX')) {
                // if (strtok($pembelian_grouped_supplier[0]->supplier) === 'MAX') {
                //     $supplier = Supplier::where('nama', 'MAX')->first();
                //     $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                // }
                if (explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[0] === 'MAX') {
                    $supplier = Supplier::where('nama', 'MAX')->first();
                    $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                }
            } elseif (str_contains($pembelian_grouped_supplier[0]->supplier, 'ROYAL')) {
                // if (strtok($pembelian_grouped_supplier[0]->supplier) === 'ROYAL') {
                //     $supplier = Supplier::where('nama', 'ROYAL')->first();
                //     $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                // }
                if (explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[0] === 'ROYAL') {
                    $supplier = Supplier::where('nama', 'ROYAL')->first();
                    $nomor_nota = explode(' ', trim($pembelian_grouped_supplier[0]->supplier))[1];
                }
            } elseif (str_contains($pembelian_grouped_supplier[0]->supplier, 'ISMAIL')) {
                $supplier = Supplier::where('nama', 'Bpk. ISMAIL')->first();
            } elseif (str_contains($pembelian_grouped_supplier[0]->supplier, 'TOKO BARU')) {
                $supplier = Supplier::where('nama', 'TOKO BARU')->first();
            }
        }

        if ($supplier === null) {
            dump($pembelian_grouped_supplier);
        }

        $pembelian_new = Pembelian::create([
            'supplier_id' => $supplier->id,
            'supplier_nama' => $supplier->nama,
        ]);

        $isi = array();
        $harga_total = 0;
        $status_bayar = 'BELUM';
        $jumlah_lunas = 0;
        $keterangan_bayar = '';
        $tanggal_lunas = null;
        $created_at = null;

        foreach ($pembelian_grouped_supplier as $pembelian_barang) {
            $barang = Barang::where('nama', $pembelian_barang->nama_barang)->first();
            $jumlah_sub = null;
            if ($pembelian_barang->jumlah_rol !== null) {
                $jumlah_sub = (int)($pembelian_barang->jumlah_rol * 100);
            }
            $pembelian_barang_new = PembelianBarang::create([
                'pembelian_id' => $pembelian_new->id,
                'barang_id' => $barang->id,
                'barang_nama' => $barang->nama,
                'satuan_main' => $pembelian_barang->satuan_meter,
                'jumlah_main' => (int)($pembelian_barang->jumlah_meter * 100),
                'harga_main' => (int)$pembelian_barang->harga_meter,
                'satuan_sub' => $pembelian_barang->satuan_rol,
                'jumlah_sub' => $jumlah_sub,
                'harga_sub' => null,
                'harga_t' => (int)$pembelian_barang->harga_total,
                'status_bayar' => $pembelian_barang->status_pembayaran,
                'keterangan_bayar' => $pembelian_barang->keterangan_pembayaran,
                'tanggal_lunas' => $pembelian_barang->tanggal_lunas,
                'created_at' => $pembelian_barang->created_at,
                'updated_at' => $pembelian_barang->updated_at,
                'creator' => $pembelian_barang->created_by,
                'updater' => $pembelian_barang->updated_by,
            ]);
            $harga_total += (int)$pembelian_barang_new->harga_t;
            $exist_satuan_main = false;
            $exist_satuan_sub = false;
            if (count($isi) !== 0) {
                for ($i=0; $i < count($isi); $i++) {
                    if ($isi[$i]['satuan'] === $pembelian_barang->satuan_meter) {
                        $isi[$i]['jumlah'] += (int)($pembelian_barang->jumlah_meter * 100);
                        $exist_satuan_main = true;
                    }
                    if ($isi[$i]['satuan'] === $pembelian_barang->satuan_rol) {
                        $isi[$i]['jumlah'] += (int)($pembelian_barang->jumlah_rol * 100);
                        $exist_satuan_sub = true;
                    }
                }
            }
            if (!$exist_satuan_main) {
                $isi[] = [
                    'satuan' => $pembelian_barang->satuan_meter,
                    'jumlah' => (int)($pembelian_barang->jumlah_meter * 100),
                ];
            }
            if (!$exist_satuan_sub) {
                if ($pembelian_barang->satuan_rol !== null) {
                    $isi[] = [
                        'satuan' => $pembelian_barang->satuan_rol,
                        'jumlah' => (int)($pembelian_barang->jumlah_rol * 100),
                    ];
                }
            }
            if ($pembelian_barang->status_pembayaran === 'LUNAS') {
                $jumlah_lunas++;
            }
            $keterangan_bayar = $pembelian_barang->keterangan_pembayaran;
            if ($tanggal_lunas === null) {
                $tanggal_lunas = $pembelian_barang->tanggal_lunas;
            } else {
                if ($pembelian_barang->tanggal_lunas !== null) {
                    if (date('Y-m-d H:i:s', strtotime($tanggal_lunas)) < date('Y-m-d H:i:s', strtotime($pembelian_barang->tanggal_lunas))) {
                        $tanggal_lunas = $pembelian_barang->tanggal_lunas;
                    }
                }
            }

            if ($created_at === null) {
                $created_at = $pembelian_barang->created_at;
            } else {
                if (date('Y-m-d H:i:s', strtotime($created_at)) > date('Y-m-d H:i:s', strtotime($pembelian_barang->created_at))) {
                    $created_at = $pembelian_barang->created_at;
                }
            }
        }
        // Perlu diupdate: nomor_nota, isi, status_bayar, keterangan_bayar, tanggal_lunas

        if ($jumlah_lunas === count($pembelian_grouped_supplier)) {
            $status_bayar = 'LUNAS';
        } elseif ($jumlah_lunas < count($pembelian_grouped_supplier) && $jumlah_lunas > 0) {
            $status_bayar = 'SEBAGIAN';
        }

        if ($nomor_nota === null) {
            $nomor_nota = "N-$pembelian_new->id";
        }

        $pembelian_new->update([
            'nomor_nota' => $nomor_nota,
            'isi' => json_encode($isi),
            'harga_total' => $harga_total,
            'status_bayar' => $status_bayar,
            'keterangan_bayar' => $keterangan_bayar,
            'tanggal_lunas' => $tanggal_lunas,
            'created_at' => $tanggal_lunas,
        ]);
    }
    $pembelian_tanggal_selanjutnya = PembelianTemp::where('created_at', '>', $until)->orderBy('created_at')->first();
    // if ($until > date('Y-m-d H:i:s', strtotime('2023-05-08 23:59:59'))) {
    //     dump('anomali');
    //     dump($until);
    //     dump($pembelian_tanggal_selanjutnya);
    // }
    if ($pembelian_tanggal_selanjutnya === null) {
        // dd($a);
        break;
    }
    // dump($pembelian_tanggal_selanjutnya->created_at);
    $until = date('Y-m-d', strtotime($pembelian_tanggal_selanjutnya->created_at));
    $until .= ' 23:59:59';
    $from = date('Y-m-d', strtotime($pembelian_tanggal_selanjutnya->created_at));
    $from .= ' 00:00:00';
    // $pembelian_tanggal_terkaits = Pembelian::where('created_at', '>', $from)->where('created_at', '<=', $until)->get();
    // dd($pembelian_tanggal_selanjutnya);
    // dump(count($pembelian_tanggal_terkaits));
    $a += count($pembelian_tanggal_terkaits);
}

// END - Pengelompokkan Pembelian yang sudah ada


PembelianBarang::create([
    'pembelian_id' => $pembelian->id,
    'barang_id' => $barang->id,
    'barang_nama' => $barang->nama,
    'satuan_main' => $pembelian_temp->satuan_meter,
    'jumlah_main' => (int)($pembelian_temp->jumlah_meter * 100),
    'harga_main' => (int)$pembelian_temp->harga_meter,
    'satuan_sub' => $pembelian_temp->satuan_rol,
    'jumlah_sub' => $jumlah_sub,
    'harga_sub' => null,
    'harga_t' => (int)$pembelian_temp->harga_total,
    'status_bayar' => $pembelian_temp->status_pembayaran,
    'keterangan_bayar' => $pembelian_temp->keterangan_pembayaran,
    'tanggal_lunas' => $pembelian_temp->tanggal_lunas,
    'created_at' => $pembelian_temp->created_at,
    'updated_at' => $pembelian_temp->updated_at,
    'creator' => $pembelian_temp->created_by,
    'updater' => $pembelian_temp->updated_by,
]);

list($isi, $harga_total, $status_bayar, $keterangan_bayar, $tanggal_lunas, $nomor_nota) = Pembelian::lengkapi_data_pembelian($pembelian, $pembelian_temp);

$pembelian->update([
    'nomor_nota' => $nomor_nota,
    'isi' => json_encode($isi),
    'harga_total' => $harga_total,
    'status_bayar' => $status_bayar,
    'keterangan_bayar' => $keterangan_bayar,
    'tanggal_lunas' => $tanggal_lunas,
]);

list($isi, $harga_total, $status_bayar, $keterangan_bayar, $tanggal_lunas, $nomor_nota) = Pembelian::lengkapi_data_pembelian($pembelian, $pembelian_temp);

$pembelian->update([
    'nomor_nota' => $nomor_nota,
    'isi' => json_encode($isi),
    'harga_total' => $harga_total,
    'status_bayar' => $status_bayar,
    'keterangan_bayar' => $keterangan_bayar,
    'tanggal_lunas' => $tanggal_lunas,
]);

if ($get['from_day'] === null || $get['from_month'] === null || $get['from_year'] === null || $get['to_day'] === null || $get['to_month'] === null || $get['to_year'] === null) {
    // Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
    if ($all || (!$all && !$lunas && !$belum_lunas && !$sebagian) || ($lunas && $belum_lunas && $sebagian)) {

    } else {
        if ($lunas && !$belum_lunas && !$sebagian) {
            if ($get['supplier_id']) {
                $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
            } else {
                $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                if (count($pembelians) === 0) {
                    $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                }
            }
        } elseif (!$lunas && $belum_lunas && !$sebagian) {
            if ($get['supplier_id']) {
                $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
            } else {
                $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                if (count($pembelians) === 0) {
                    $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                }
            }
        } elseif (!$lunas && !$belum_lunas && $sebagian) {
            if ($get['supplier_id']) {
                $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
            } else {
                $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                if (count($pembelians) === 0) {
                    $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                }
            }
        } elseif ($lunas && $belum_lunas && !$sebagian) {
            if ($get['supplier_id']) {
                $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->where(function ($query) {
                    $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                })->latest()->limit(500)->get();
            } else {
                $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->where(function ($query) {
                    $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                })->latest()->limit(500)->get();
                if (count($pembelians) === 0) {
                    $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->where(function ($query) {
                        $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                    })->latest()->limit(500)->get();
                }
            }
        } elseif ($lunas && !$belum_lunas && $sebagian) {
            if ($get['supplier_id']) {
                $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->where(function ($query) {
                    $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                })->latest()->limit(500)->get();
            } else {
                $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->where(function ($query) {
                    $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                })->latest()->limit(500)->get();
                if (count($pembelians) === 0) {
                    $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->where(function ($query) {
                        $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                    })->latest()->limit(500)->get();
                }
            }
        } elseif (!$lunas && $belum_lunas && $sebagian) {
            if ($get['supplier_id']) {
                $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->where(function ($query) {
                    $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                })->latest()->limit(500)->get();
            } else {
                $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->where(function ($query) {
                    $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                })->latest()->limit(500)->get();
                if (count($pembelians) === 0) {
                    $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->where(function ($query) {
                        $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                    })->latest()->limit(500)->get();
                }
            }
        }
    }
    // End - Filter Berdasarkan Nama Pelanggan - Tanpa Tanggal
    } else {
        // Filter Berdasarkan Nama Pelanggan + Tanggal
        $from = "$get[from_year]-$get[from_month]-$get[from_day]";
        $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
        if ($all || (!$all && !$lunas && !$belum_lunas && !$sebagian) || ($lunas && $belum_lunas && $sebagian)) {

        } else {
            if ($lunas && !$belum_lunas && !$sebagian) {

            } elseif (!$lunas && $belum_lunas && !$sebagian) {

            } elseif (!$lunas && !$belum_lunas && $sebagian) {

            } elseif ($lunas && $belum_lunas && !$sebagian) {

            } elseif ($lunas && !$belum_lunas && $sebagian) {

            } elseif (!$lunas && $belum_lunas && $sebagian) {

            }
        }
    $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->latest()->get();
    // End - Filter Berdasarkan Nama Pelanggan + Tanggal
    }
