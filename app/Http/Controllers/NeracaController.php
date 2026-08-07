<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\Menu;
use App\Models\Nota;
use App\Models\Pembelian;
use App\Models\UserInstance;
use Illuminate\Http\Request;

class NeracaController extends Controller
{
    function index(Request $request) {
        $get = $request->query();
        $from = $get['from'] ?? date('Y-m-d', strtotime('-1 month'));
        $until = $get['until'] ?? date('Y-m-d');

        /**
         * ASET LANCAR 
         * */
        $userInstances = UserInstance::orderBy('order')->get()->groupBy('parent');
        $total_aset_lancar = 0.00;
        $total_kas_kantor = 0.00;
        $aset_lancar = [];
        $kas_kantor = [];
        foreach ($userInstances as $parents) {
            foreach ($parents as $instance) {
                $accounting = Accounting::where('user_instance_id', $instance->id)
                    ->where('created_at', '<=', $until)
                    ->latest()
                    ->first();
                if ($instance->parent === 'KAS KANTOR') {
                    $kas_kantor[] = [
                        'shown_name' => $instance->shown_name,
                        'amount' => $accounting ? $accounting->saldo : 0.00,
                    ];
                    $total_kas_kantor += $accounting ? $accounting->saldo : 0.00;
                }
                if (!isset($aset_lancar[$instance->parent])) {
                    $aset_lancar[$instance->parent] = [
                        'shown_name' => $instance->parent,
                        'amount' => 0.00
                    ];
                }
                $aset_lancar[$instance->parent]['amount'] += $accounting ? $accounting->saldo : 0.00;
                $total_aset_lancar += $accounting ? $accounting->saldo : 0.00;
            }
        }

        $total_piutang_usaha = Nota::where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN')
            ->where('created_at', '<=', $until)
            ->sum('amount_due');
        $total_aset_lancar += $total_piutang_usaha;
        $aset_lancar['PIUTANG USAHA'] = [
            'shown_name' => 'PIUTANG USAHA',
            'amount' => $total_piutang_usaha,
        ];

        /**
         * ASET TETAP 
         * */
        $tanggal_operasional_kendaraan_angkut = '2021-09-01';
        $lama_operasional_kendaraan_angkut = (int)(floor((strtotime($until) - strtotime($tanggal_operasional_kendaraan_angkut)) / (60 * 60 * 24 * 30))); // dalam bulan, integer, pembulatan ke bawah
        $jumlah_penyusutan_kendaraan_angkut = 1673958 * $lama_operasional_kendaraan_angkut;
        // dd($jumlah_penyusutan_kendaraan_angkut);
        $total_aset_tetap = 0.00;
        $aset_tetap = [
            [
                'shown_name' => 'TANAH + BIAYA PEMBANGUNAN',
                'amount' => 1073996000,
            ],
            [
                'shown_name' => 'PENYUSUTAN TANAH DAN BANGUNAN',
                'amount' => 0,
            ],
            [
                'shown_name' => 'KENDARAAN OPERASIONAL (AVANZA VELOZ)',
                'amount' => 231243000,
            ],
            [
                'shown_name' => 'KENDARAAN ANGKUT (TRAGA)',
                'amount' => 230700000,
            ],
            [
                'shown_name' => "PENYUSUTAN TRAGA ($lama_operasional_kendaraan_angkut bulan)",
                'amount' => $jumlah_penyusutan_kendaraan_angkut,
            ],
            [
                'shown_name' => 'PERALATAN DAN PERLENGKAPAN',
                'amount' => 0,
            ],
            [
                'shown_name' => 'PENYUSUTAN PERALATAN DAN PERLENGKAPAN',
                'amount' => 0,
            ],
        ];
        foreach ($aset_tetap as $item) {
            if (is_numeric($item['amount'])) {
                $total_aset_tetap += $item['amount'];
            }
        }

        $estimasi_total_aset = $total_aset_lancar + $total_aset_tetap;
        
        /**
         * HUTANG USAHA
         */
        $account_payable = Pembelian::where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN')
            ->where('created_at', '<=', $until)
            ->sum('amount_due');
        // dd($account_payable);
        // dd(number_format($total_piutang_usaha, 2, ',', '.'));
        // dump($get);
        // dump($userInstances);
        // dump($aset_lancar);
        // dd($kas_kantor);


        $data = [
            'menus' => Menu::get(),
            'route_now' => 'neraca.index',
            'parent_route' => 'accounting.index',
            'profile_menus' => Menu::get_profile_menus(),
            'accounting_menus' => Menu::get_accounting_menus(),
            'from' => $from,
            'until' => $until,
            'aset_lancar' => $aset_lancar,
            'kas_kantor' => $kas_kantor,
            'total_aset_lancar' => $total_aset_lancar,
            'total_kas_kantor' => $total_kas_kantor,
            'aset_tetap' => $aset_tetap,
            'total_aset_tetap' => $total_aset_tetap,
            'estimasi_total_aset' => $estimasi_total_aset,
            'account_payable' => $account_payable,
        ];
        return view('neraca.index', $data);
    }
}
