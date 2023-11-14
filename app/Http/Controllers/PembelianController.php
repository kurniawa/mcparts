<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Barang;
use App\Models\Menu;
use App\Models\Pembelian;
use App\Models\PembelianBarang;
use App\Models\Supplier;
use App\Models\SupplierAlamat;
use App\Models\SupplierKontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    function index(Request $request) {
        $get = $request->query();

        // $from = date('Y') . "-" . date('m') . "-01 00:00:00";
        // $until = date('Y') . "-" . date('m') . "-" . date('d') . " 23:59:59";
        $from = null;
        $until = null;

        $month = (int)date('m');
        if ($month <= 3) {
            $from = date('Y') . "-01" . "-01 00:00:00";
            $t = date('t', strtotime(date('Y') . "-03-01"));
            $until = date('Y') . "-03" . "-$t" . " 23:59:59";
        } elseif ($month <= 6) {
            $from = date('Y') . "-04" . "-01 00:00:00";
            $t = date('t', strtotime(date('Y') . "-06-01"));
            $until = date('Y') . "-06" . "-$t" . " 23:59:59";
        } elseif ($month <= 9) {
            $from = date('Y') . "-07" . "-01 00:00:00";
            $t = date('t', strtotime(date('Y') . "-09-01"));
            $until = date('Y') . "-09" . "-$t" . " 23:59:59";
        } elseif ($month <= 12) {
            $from = date('Y') . "-10" . "-01 00:00:00";
            $t = date('t', strtotime(date('Y') . "-12-01"));
            $until = date('Y') . "-12" . "-$t" . " 23:59:59";
        }

        $pembelians = collect();

        if (count($get) !== 0) {
            // dd($get);
            $all = false;
            $lunas = false;
            $belum_lunas = false;
            $sebagian = false;
            $filter_status_bayar = false;
            if (isset($get['status_bayar'])) {
                if (count($get['status_bayar']) > 0) {
                    foreach ($get['status_bayar'] as $status_bayar) {
                        if ($status_bayar === 'all') {
                            $all = true;
                        } elseif ($status_bayar === 'lunas') {
                            $lunas = true;
                        } elseif ($status_bayar === 'belum') {
                            $belum_lunas = true;
                        } elseif ($status_bayar === 'sebagian') {
                            $sebagian = true;
                        }
                    }
                    $filter_status_bayar = true;
                }
            }

            $filter_tanggal = false;
            if ($get['from_day'] && $get['from_month'] && $get['from_year'] && $get['to_day'] && $get['to_month'] && $get['to_year']) {
                $filter_tanggal = true;
            }

            if (($get['supplier_nama'] || $get['supplier_id']) && !$filter_tanggal && !$filter_status_bayar) {
                // FILTER HANYA BERDASARKAN SUPPLIER
                if ($get['supplier_id']) {
                    $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->latest()->limit(500)->get();
                } else {
                    $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->latest()->limit(500)->get();
                    if (count($pembelians) === 0) {
                        $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->latest()->limit(500)->get();
                    }
                }
                // END - FILTER HANYA BERDASARKAN SUPPLIER
            } elseif (!($get['supplier_nama'] || $get['supplier_id']) && $filter_tanggal && !$filter_status_bayar) {
                // Filter hanya berdasarkan tanggal
                $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->latest()->get();
                // END - Filter hanya berdasarkan tanggal
            } elseif (!($get['supplier_nama'] || $get['supplier_id']) && !$filter_tanggal && $filter_status_bayar) {
                // Filter hanya berdasarkan status_bayar
                if ($all) {
                    $pembelians = Pembelian::latest()->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM')->latest()->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                } else {
                    dd('error - filter hanya berdasarkan status_bayar');
                }
                // END - Filter hanya berdasarkan status_bayar
            } elseif (($get['supplier_nama'] || $get['supplier_id']) && $filter_tanggal && !$filter_status_bayar) {
                // Filter berdasarkan nama dan tanggal
                $from = "$get[from_year]-$get[from_month]-$get[from_day] 00:00:00";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";

                if ($get['supplier_id']) {
                    $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } else {
                    $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                    if (count($pembelians) === 0) {
                        $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                    }
                }
                // END - Filter berdasarkan nama dan tanggal
            } elseif (($get['supplier_nama'] || $get['supplier_id']) && !$filter_tanggal && $filter_status_bayar) {
                // Filter berdasarkan nama dan status_bayar
                if ($lunas && !$belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                        }
                    }
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                        })->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                        })->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                            })->latest()->limit(500)->get();
                        }
                    }
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                            })->latest()->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                            })->latest()->limit(500)->get();
                        }
                    }
                } else {
                    dd('error - filter berdasarkan nama dan status_bayar');
                }
                // END - Filter berdasarkan nama dan status_bayar
            } elseif (!($get['supplier_nama'] || $get['supplier_id']) && $filter_tanggal && $filter_status_bayar) {
                // Filter berdasarkan tanggal dan status_bayar
                $from = "$get[from_year]-$get[from_month]-$get[from_day] 00:00:00";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";

                if ($all) {
                    $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM')->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'SEBAGIAN')->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM')->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN')->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN')->whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
                } else {
                    dd('error - filter hanya berdasarkan status_bayar');
                }

                // END - Filter berdasarkan tanggal dan status_bayar
            } elseif (($get['supplier_nama'] || $get['supplier_id']) && $filter_tanggal && $filter_status_bayar) {

                $from = "$get[from_year]-$get[from_month]-$get[from_day] 00:00:00";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";

                if ($lunas && !$belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->latest()->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM')->latest()->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->latest()->limit(500)->get();
                        }
                    }
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                        })->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                        })->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM');
                            })->latest()->limit(500)->get();
                        }
                    }
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                            })->latest()->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                        })->latest()->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'BELUM')->orWhere('status_bayar', 'SEBAGIAN');
                            })->latest()->limit(500)->get();
                        }
                    }
                } else {
                    dd('error - filter berdasarkan nama, tanggal dan status_bayar');
                }

            } else {
                dd('tidak menemukan filter yang cocok...');
            }
        } else {
            $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->latest()->limit(500)->get();
            // $pembelians = Pembelian::latest()->limit(100)('created_at')->get();
            // dump($from, $until);
            // dd($pembelians);
        }


        $pembelian_barangs_all = collect();
        $alamats = collect();
        $kontaks = collect();
        $grand_total = 0;
        $lunas_total = 0;

        foreach ($pembelians as $pembelian) {
            $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian->id)->get();
            $pembelian_barangs_all->push($pembelian_barangs);
            $supplier_alamat = SupplierAlamat::where('supplier_id', $pembelian->supplier_id)->where('tipe', 'UTAMA')->first();
            if ($supplier_alamat!== null) {
                $alamat = Alamat::find($supplier_alamat->alamat_id);
                $alamats->push($alamat);
            } else {
                $alamats->push(null);
            }
            $supplier_kontak = SupplierKontak::where('supplier_id', $pembelian->supplier_id)->where('tipe', 'UTAMA')->first();
            $kontaks->push($supplier_kontak);
            $grand_total += $pembelian->harga_total;
            if ($pembelian->status_bayar === 'LUNAS') {
                $lunas_total += $pembelian->harga_total;
            }
        }

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        // Pembelian Total Supplier
        // dump($pembelians);
        $pembelian_grouped_supplier = $pembelians->groupBy('supplier_nama');
        // dump($pembelian_grouped_supplier);
        $pembelian_total_suppliers = collect();
        foreach ($pembelian_grouped_supplier as $pembelian_grouped_supp) {
            $pembelian_total = 0;
            $pembelian_lunas = 0;
            $pembelian_belum_lunas = 0;
            $supplier_nama = '';
            foreach ($pembelian_grouped_supp as $pembelian_grouped_s) {
                $pembelian_total += (float)$pembelian_grouped_s->harga_total;
                if ($pembelian_grouped_s->status_bayar === 'BELUM') {
                    $pembelian_belum_lunas += (float)$pembelian_grouped_s->harga_total;
                } elseif ($pembelian_grouped_s->status_bayar === 'LUNAS') {
                    $pembelian_lunas += (float)$pembelian_grouped_s->harga_total;
                }
                $supplier_nama = $pembelian_grouped_s->supplier_nama;
            }
            $pembelian_total_suppliers->push([
                'supplier_nama' => $supplier_nama,
                'pembelian_total' => $pembelian_total,
                'pembelian_lunas' => $pembelian_lunas,
                'pembelian_belum_lunas' => $pembelian_belum_lunas,
            ]);
        }
        // dd($pembelian_total_suppliers);
        // END - Pembelian Total Supplier
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'pembelians' => $pembelians,
            'pembelian_barangs_all' => $pembelian_barangs_all,
            'alamats' => $alamats,
            'kontaks' => $kontaks,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
            'grand_total' => $grand_total,
            'lunas_total' => $lunas_total,
            'from' => $from,
            'until' => $until,
            'pembelian_total_suppliers' => $pembelian_total_suppliers,
        ];
        // dd($pembelians);
        // dump($from);
        // dd($until);
        return view('pembelians.index', $data);
    }

    function show(Pembelian $pembelian) {
        dd($pembelian);
    }

    function store(Request $request) {
        $post = $request->post();
        // dd($post);
        // dump((int)$post['harga_t'][0]);
        // dump((float)$post['harga_t'][0]);
        // dd((float)$post['harga_t'][1]);
        $request->validate([
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
            'supplier_nama' => 'required',
            'supplier_id' => 'required',
        ]);

        $supplier = Supplier::find($post['supplier_id']);
        $user = Auth::user();

        $pembelian_new = Pembelian::create([
            'supplier_id' => $supplier->id,
            'supplier_nama' => $supplier->nama,
            'creator' => $user->username,
            'created_at' => date('Y-m-d H:i:s', strtotime("$post[year]-$post[month]-$post[day]" . " " . date("H:i:s"))),
        ]);

        // $isi = collect();
        $isi = array();
        $success_ = '';
        $warnings_ = '';

        $indexes_to_process = array();

        for ($i=0; $i < count($post['barang_id']); $i++) {
            $barang = Barang::find($post['barang_id'][$i]);
            // dump($barang);
            // dump((float)$post['harga_t'][$i]);
            // dump((int)$post['jumlah_main'][$i]);
            if ($barang !== null && (float)$post['harga_t'][$i] !== 0.0 && (int)$post['jumlah_main'][$i] !== 0) {
                $indexes_to_process[] = $i;
            } else {
                $warnings_ .= "-failed to process-index: $i-";
            }
        }
        // dd('end');
        for ($i=0; $i < count($indexes_to_process); $i++) {
            $barang = Barang::find($post['barang_id'][$indexes_to_process[$i]]);
            // dump($barang);
            // dump((float)$post['harga_t'][$indexes_to_process[$i]]);
            // dd((int)$post['jumlah_main'][$indexes_to_process[$i]]);
            // if ($barang == null || (float)$post['harga_t'][$indexes_to_process[$i]] == 0 || (int)$post['jumlah_main'][$indexes_to_process[$i]] == 0) {
            //     break;
            // }
            $harga_main = round((float)$post['harga_main'][$indexes_to_process[$i]],2);
            $harga_sub = round($harga_main * (int)$post['jumlah_main'][$indexes_to_process[$i]],2);

            $pembelian_barang = PembelianBarang::create([
                'pembelian_id' => $pembelian_new->id,
                'barang_id' => $barang->id,
                'barang_nama' => $barang->nama,
                'satuan_main' => $barang->satuan_main,
                'jumlah_main' => (int)$post['jumlah_main'][$indexes_to_process[$i]] * 100,
                'harga_main' => $harga_main,
                'satuan_sub' => $barang->satuan_sub,
                'jumlah_sub' => (int)$post['jumlah_sub'][$indexes_to_process[$i]] * 100,
                'harga_sub' => $harga_sub,
                'harga_t' => round((float)$post['harga_t'][$indexes_to_process[$i]],2),
                // 'status_bayar' => null,
                // 'keterangan_bayar' => null,
                // 'tanggal_lunas' => null,
                // 'created_at' => $pembelian_barang->created_at, // sudah otomatis
                // 'updated_at' => $pembelian_barang->updated_at,
                'creator' => $user->username,
                // 'updater' => $user->username,
            ]);

            $success_ .= '-pembelian_barang created-';

            $exist_satuan_main = false;
            $exist_satuan_sub = false;
            if (count($isi) != 0) {
                for ($j=0; $j < count($isi); $j++) {
                    if ($isi[$j]['satuan'] == $pembelian_barang->satuan_main) {
                        $isi[$j]['jumlah'] = (int)$isi[$j]['jumlah'] + (int)($pembelian_barang->jumlah_main);
                        // dump($isi[$j]['jumlah']);
                        // dump($pembelian_barang->jumlah_main);
                        // dump('isi:');
                        // dump($isi);
                        $exist_satuan_main = true;
                    }
                    if ($isi[$j]['satuan'] == $pembelian_barang->satuan_sub) {
                        $isi[$j]['jumlah'] = (int)$isi[$j]['jumlah'] + (int)($pembelian_barang->jumlah_sub);
                        $exist_satuan_sub = true;
                    }
                }
                // foreach ($isi as $isi_item) {
                //     if ($isi_item['satuan'] == $pembelian_barang->satuan_main) {
                //         $isi_item['jumlah'] = (int)$isi_item['jumlah'] + (int)($pembelian_barang->jumlah_main);
                //         dump($isi_item['jumlah']);
                //         dump($pembelian_barang->jumlah_main);
                //         dump('isi:');
                //         dump($isi);
                //         $exist_satuan_main = true;
                //     }
                //     if ($isi_item['satuan'] == $pembelian_barang->satuan_sub) {
                //         $isi_item['jumlah'] = (int)$isi_item['jumlah'] + (int)($pembelian_barang->jumlah_sub);
                //         $exist_satuan_sub = true;
                //     }
                // }
            }
            if (!$exist_satuan_main) {
                // $isi->push([
                //     'satuan' => $pembelian_barang->satuan_main,
                //     'jumlah' => (int)($pembelian_barang->jumlah_main),
                // ]);
                $isi[]=[
                    'satuan' => $pembelian_barang->satuan_main,
                    'jumlah' => (int)($pembelian_barang->jumlah_main),
                ];
                // dump('first isi:');
                // dump((int)($pembelian_barang->jumlah_main));
            }
            if (!$exist_satuan_sub) {
                if ($pembelian_barang->satuan_sub != null) {
                    // $isi->push([
                    //     'satuan' => $pembelian_barang->satuan_sub,
                    //     'jumlah' => (int)($pembelian_barang->jumlah_sub),
                    // ]);
                    $isi[]=[
                        'satuan' => $pembelian_barang->satuan_sub,
                        'jumlah' => (int)($pembelian_barang->jumlah_sub),
                    ];
                }
            }
        }
        // dump('isi:');
        // dump($isi);
        // cek apakah ada yang diinput ke pembelian_barang?
        $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian_new->id)->get();
        if (count($pembelian_barangs) == 0) {
            $pembelian_new->delete();
            return back()->with('warnings_', '-pembelian di cancel karena tidak terdeteksi adanya barang-');
        }

        $nomor_nota = "N-$pembelian_new->id";
        if ($post['nomor_nota'] != null) {
            $nomor_nota = $post['nomor_nota'];
        }

        $pembelian_new->update([
            'nomor_nota' => $nomor_nota,
            'isi' => json_encode($isi),
            'harga_total' => round((float)$post['harga_total'],2),
            // 'status_bayar' => $status_bayar,
            // 'keterangan_bayar' => $keterangan_bayar,
            // 'tanggal_lunas' => $tanggal_lunas,
            // 'created_at' => $tanggal_lunas,
        ]);
        $success_ .= '-pembelian new created-';

        $feedback = [
            'success_' => $success_,
            'warnings_' => $warnings_,
        ];

        return back()->with($feedback);
    }

    function delete(Pembelian $pembelian) {
        // dd($pembelian);
        $pembelian->delete();
        $feedback = [
            'danger_' => '-pembelian deleted!-'
        ];
        return back()->with($feedback);
    }

    function pelunasan(Pembelian $pembelian, Request $request) {
        $post = $request->post();
        // dump($pembelian);
        // dd($post);

        $request->validate([
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        $pembelian->tanggal_lunas = date('Y-m-d', strtotime("$post[year]-$post[month]-$post[day]")) . " " . date('H:i:s');
        $pembelian->status_bayar = 'LUNAS';
        $pembelian->keterangan_bayar = $post['keterangan_bayar'];
        $pembelian->save();

        return back()->with('success_', '-data_pelunasan updated-');
    }

    function pembatalan_pelunasan(Pembelian $pembelian) {
        $pembelian->tanggal_lunas = null;
        $pembelian->status_bayar = 'BELUM';
        $pembelian->keterangan_bayar = null;
        $pembelian->save();

        return back()->with('warnings_', '-pelunasan dibatalkan-');
    }

    function edit(Pembelian $pembelian) {
        $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian->id)->get();

        $label_supplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'pembelian' => $pembelian,
            'pembelian_barangs' => $pembelian_barangs,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
        ];
        return view('pembelians.edit', $data);
    }

    function update(Pembelian $pembelian, Request $request) {
        $post = $request->post();

        // dump($post);
        // dump($pembelian);

        $request->validate([
            'day' => 'required',
            'month' => 'required',
            'year' => 'required',
            'supplier_nama' => 'required',
            'supplier_id' => 'required',
        ]);

        $supplier = Supplier::find($post['supplier_id']);
        $user = Auth::user();

        $nomor_nota = "N-$pembelian->id";
        if ($post['nomor_nota'] !== null) {
            $nomor_nota = $post['nomor_nota'];
        }

        $pembelian->update([
            'nomor_nota' => $nomor_nota,
            'supplier_id' => $supplier->id,
            'supplier_nama' => $supplier->nama,
            'updater' => $user->username,
            'created_at' => date('Y-m-d H:i:s', strtotime("$post[year]-$post[month]-$post[day]" . " " . date("H:i:s"))),
        ]);

        // $isi = collect();
        $isi = array();
        $success_ = '';

        for ($i=0; $i < count($post['pembelian_barang_id']); $i++) {
            // if ($barang === null) { // kasus dimana barang memang sudah dihapus namun apa yang sudah tercantum pada nota pembelian, tidak terhapus, namun barang_id menjadi null
            // }
            if ($post['pembelian_barang_id'][$i] === 'new') {
                // dd($barang);
                $barang = Barang::find($post['barang_id'][$i]);

                $harga_main = round((float)$post['harga_main'][$i],2);
                $harga_sub = round($harga_main * (int)$post['jumlah_main'][$i],2);

                $pembelian_barang = PembelianBarang::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $barang->id,
                    'barang_nama' => $barang->nama,
                    'satuan_main' => $barang->satuan_main,
                    'jumlah_main' => (int)$post['jumlah_main'][$i] * 100,
                    'harga_main' => $harga_main,
                    'satuan_sub' => $barang->satuan_sub,
                    'jumlah_sub' => (int)$post['jumlah_sub'][$i] * 100,
                    'harga_sub' => $harga_sub,
                    'harga_t' => round((float)$post['harga_t'][$i],2),
                    // 'status_bayar' => null,
                    // 'keterangan_bayar' => null,
                    // 'tanggal_lunas' => null,
                    // 'created_at' => $pembelian_barang->created_at, // sudah otomatis
                    // 'updated_at' => $pembelian_barang->updated_at,
                    'creator' => $user->username,
                    // 'updater' => $user->username,
                ]);

                $success_ .= '-pembelian_barang created-';
            } else {
                $pembelian_barang = PembelianBarang::find($post['pembelian_barang_id'][$i]);
                // dd($pembelian_barang);
                $harga_main = round((float)$post['harga_main'][$i],2);
                $harga_sub = round($harga_main * (int)$post['jumlah_main'][$i],2);

                try {
                    $pembelian_barang->update([
                        'barang_id' => $pembelian_barang->barang_id,
                        'barang_nama' => $pembelian_barang->barang_nama,
                        'satuan_main' => $pembelian_barang->satuan_main,
                        'jumlah_main' => (int)$post['jumlah_main'][$i] * 100,
                        'harga_main' => $harga_main,
                        'satuan_sub' => $pembelian_barang->satuan_sub,
                        'jumlah_sub' => (int)$post['jumlah_sub'][$i] * 100,
                        'harga_sub' => $harga_sub,
                        'harga_t' => round((float)$post['harga_t'][$i],2),
                        // 'status_bayar' => null,
                        // 'keterangan_bayar' => null,
                        // 'tanggal_lunas' => null,
                        // 'created_at' => $pembelian_barang->created_at, // sudah otomatis
                        // 'updated_at' => $pembelian_barang->updated_at,
                        // 'creator' => $user->username,
                        'updater' => $user->username,
                    ]);
                } catch (\Throwable $th) {
                    //throw $th;
                    dump($th);
                    dd($post['pembelian_barang_id'][$i]);
                }
            }

            $exist_satuan_main = false;
            $exist_satuan_sub = false;
            if (count($isi) !== 0) {
                for ($j=0; $j < count($isi); $j++) {
                    if ($isi[$j]['satuan'] === $pembelian_barang->satuan_main) {
                        $isi[$j]['jumlah'] = (int)$isi[$j]['jumlah'] + (int)($pembelian_barang->jumlah_main);
                        // dump($isi[$j]['jumlah']);
                        // dump($pembelian_barang->jumlah_main);
                        // dump('isi:');
                        // dump($isi);
                        $exist_satuan_main = true;
                    }
                    if ($isi[$j]['satuan'] === $pembelian_barang->satuan_sub) {
                        $isi[$j]['jumlah'] = (int)$isi[$j]['jumlah'] + (int)($pembelian_barang->jumlah_sub);
                        $exist_satuan_sub = true;
                    }
                }
            }
            if (!$exist_satuan_main) {
                $isi[]=[
                    'satuan' => $pembelian_barang->satuan_main,
                    'jumlah' => (int)($pembelian_barang->jumlah_main),
                ];
            }
            if (!$exist_satuan_sub) {
                if ($pembelian_barang->satuan_sub !== null) {
                    $isi[]=[
                        'satuan' => $pembelian_barang->satuan_sub,
                        'jumlah' => (int)($pembelian_barang->jumlah_sub),
                    ];
                }
            }
        }

        $pembelian->update([
            'isi' => json_encode($isi),
            'harga_total' => round((float)$post['harga_total'],2),
            // 'status_bayar' => $status_bayar,
            // 'keterangan_bayar' => $keterangan_bayar,
            // 'tanggal_lunas' => $tanggal_lunas,
            // 'created_at' => $tanggal_lunas,
        ]);
        $success_ .= '-pembelian updated-';

        $feedback = [
            'success_' => $success_,
        ];

        return back()->with($feedback);
    }

    function delete_pembelian_barang(Pembelian $pembelian, PembelianBarang $pembelian_barang) {
        // dump($pembelian);
        // dd($pembelian_barang);

        $pembelian_barang->delete();
        $isi = Pembelian::get_isi($pembelian->id);
        $harga_total = Pembelian::get_harga_total($pembelian->id);

        // dump($pembelian->isi);
        // dd($isi);

        $pembelian->harga_total = $harga_total;
        $pembelian->isi = json_encode($isi);
        $pembelian->save();

        return back()->with('success_', '-item pembelian deleted, pembelian updated-');

    }
}
