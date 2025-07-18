<?php

namespace App\Services;

use App\Models\Pembelian;
use App\Models\PembelianBarang;
use App\Models\GoodsPrice;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PembelianService
{
    public static function createPembelian(array $data): array
    {
        $user = Auth::user();
        $supplier = $data['supplier'];
        $barangList = $data['barangList'];

        $success_ = '';
        $warnings_ = '';

        $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', "{$data['year']}-{$data['month']}-{$data['day']} " . now()->format('H:i:s'));

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::create([
                'supplier_id' => $supplier->id,
                'supplier_nama' => $supplier->nama,
                'creator' => $user->username,
                'created_at' => $createdAt,
            ]);

            $isiMap = [];

            foreach ($data['barang_id'] as $i => $barang_id) {
                $barang = $barangList[$barang_id] ?? null;
                $harga_t = (float) $data['harga_t'][$i];
                $jumlah_main = (int) $data['jumlah_main'][$i];

                if (!$barang || $harga_t == 0 || $jumlah_main == 0) {
                    $warnings_ .= "-failed to process-index: $i-";
                    continue;
                }

                $harga_main = round((float) $data['harga_main'][$i], 2);
                $harga_sub = round($harga_main * $jumlah_main, 2);

                $pembelian_barang = PembelianBarang::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $barang->id,
                    'barang_nama' => $barang->nama,
                    'satuan_main' => $barang->satuan_main,
                    'jumlah_main' => $jumlah_main * 100,
                    'harga_main' => $harga_main,
                    'satuan_sub' => $barang->satuan_sub,
                    'jumlah_sub' => (int) $data['jumlah_sub'][$i] * 100,
                    'harga_sub' => $harga_sub,
                    'harga_t' => round($harga_t, 2),
                    'creator' => $user->username,
                ]);

                $success_ .= '-pembelian_barang created-';

                // Update GoodsPrice jika harga berubah
                $last_goods_price = GoodsPrice::where('goods_id', $barang->id)->latest('created_at')->first();
                if (!$last_goods_price || $last_goods_price->price != $harga_main) {
                    GoodsPrice::create([
                        'goods_id' => $barang->id,
                        'goods_slug' => $barang->nama,
                        'supplier_id' => $barang->supplier_id,
                        'supplier_name' => $barang->supplier_nama,
                        'unit' => $barang->satuan_main,
                        'price' => $harga_main,
                        'created_by' => $user->username,
                    ]);

                    // Update harga di Barang
                    $barang->jumlah_main = $pembelian_barang->jumlah_main;
                    $barang->harga_main = $harga_main;
                    $barang->jumlah_sub = $pembelian_barang->jumlah_sub;
                    $barang->harga_sub = $harga_sub;
                    $barang->harga_total_main = round($harga_main * $jumlah_main, 2);
                    $barang->harga_total_sub = round($harga_sub * (int) $data['jumlah_sub'][$i], 2);
                    $barang->save();

                    $success_ .= '-goods_price created-';
                    $success_ .= '-barang updated-';
                }

                $key_main = strtolower($barang->satuan_main);
                $isiMap[$key_main] = ($isiMap[$key_main] ?? 0) + $pembelian_barang->jumlah_main;

                if ($barang->satuan_sub) {
                    $key_sub = strtolower($barang->satuan_sub);
                    $isiMap[$key_sub] = ($isiMap[$key_sub] ?? 0) + $pembelian_barang->jumlah_sub;
                }
            }

            // Buat isi JSON
            $isi = [];
            foreach ($isiMap as $satuan => $jumlah) {
                $isi[] = ['satuan' => $satuan, 'jumlah' => $jumlah];
            }

            $nomor_nota = $data['nomor_nota'] ?? "N-{$pembelian->id}";

            $pembelian->update([
                'nomor_nota' => $nomor_nota,
                'isi' => json_encode($isi),
                'harga_total' => round((float) $data['harga_total'], 2),
            ]);

            DB::commit();

            return [
                'success' => $success_ . '-pembelian created-',
                'warnings' => $warnings_,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
