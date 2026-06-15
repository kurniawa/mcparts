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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\PembelianService;

class PembelianController extends Controller
{
    public function __construct(
        protected PembelianService $pembelianService
    ) {}

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
                        } elseif ($status_bayar === 'LUNAS') {
                            $lunas = true;
                        } elseif ($status_bayar === 'BELUM_LUNAS') {
                            $belum_lunas = true;
                        } elseif ($status_bayar === 'SEBAGIAN') {
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
                    $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } else {
                    $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    if (count($pembelians) === 0) {
                        $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    }
                }
                // END - FILTER HANYA BERDASARKAN SUPPLIER
            } elseif (!($get['supplier_nama'] || $get['supplier_id']) && $filter_tanggal && !$filter_status_bayar) {
                // Filter hanya berdasarkan tanggal
                $from = "$get[from_year]-$get[from_month]-$get[from_day]";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";
                $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->get();
                // END - Filter hanya berdasarkan tanggal
            } elseif (!($get['supplier_nama'] || $get['supplier_id']) && !$filter_tanggal && $filter_status_bayar) {
                // Filter hanya berdasarkan status_bayar
                if ($all) {
                    $pembelians = Pembelian::latest()->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } else {
                    dd('error - filter hanya berdasarkan status_bayar');
                }
                // END - Filter hanya berdasarkan status_bayar
            } elseif (($get['supplier_nama'] || $get['supplier_id']) && $filter_tanggal && !$filter_status_bayar) {
                // Filter berdasarkan nama dan tanggal
                $from = "$get[from_year]-$get[from_month]-$get[from_day] 00:00:00";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";

                if ($get['supplier_id']) {
                    $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } else {
                    $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    if (count($pembelians) === 0) {
                        $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    }
                }
                // END - Filter berdasarkan nama dan tanggal
            } elseif (($get['supplier_nama'] || $get['supplier_id']) && !$filter_tanggal && $filter_status_bayar) {
                // Filter berdasarkan nama dan status_bayar
                if ($lunas && !$belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS');
                            })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                            })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                            })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
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
                    $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM_LUNAS')->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'SEBAGIAN')->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS')->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN')->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    $pembelians = Pembelian::where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN')->whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                } else {
                    dd('error - filter hanya berdasarkan status_bayar');
                }

                // END - Filter berdasarkan tanggal dan status_bayar
            } elseif (($get['supplier_nama'] || $get['supplier_id']) && $filter_tanggal && $filter_status_bayar) {

                $from = "$get[from_year]-$get[from_month]-$get[from_day] 00:00:00";
                $until = "$get[to_year]-$get[to_month]-$get[to_day] 23:59:59";

                if ($lunas && !$belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'BELUM_LUNAS')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where('status_bayar', 'SEBAGIAN')->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif ($lunas && $belum_lunas && !$sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'BELUM_LUNAS');
                            })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif ($lunas && !$belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                            })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } elseif (!$lunas && $belum_lunas && $sebagian) {
                    if ($get['supplier_id']) {
                        $pembelians = Pembelian::where('supplier_id', $get['supplier_id'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                    } else {
                        $pembelians = Pembelian::where('supplier_nama', $get['supplier_nama'])->whereBetween('created_at', [$from, $until])->where(function ($query) {
                            $query->where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                        })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        if (count($pembelians) === 0) {
                            $pembelians = Pembelian::where('supplier_nama','like', "%$get[supplier_nama]%")->whereBetween('created_at', [$from, $until])->where(function ($query) {
                                $query->where('status_bayar', 'BELUM_LUNAS')->orWhere('status_bayar', 'SEBAGIAN');
                            })->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
                        }
                    }
                } else {
                    dd('error - filter berdasarkan nama, tanggal dan status_bayar');
                }

            } else {
                dd('tidak menemukan filter yang cocok...');
            }
        } else {
            $pembelians = Pembelian::whereBetween('created_at', [$from, $until])->orderBy('supplier_nama')->orderByDesc('created_at')->limit(500)->get();
            // $pembelians = Pembelian::latest()->limit(100)('created_at')->get();
            // dump($from, $until);
            // dd($pembelians);
        }


        $pembelian_barangs_all = collect();
        $real_count_grand_total = 0;
        $alamats = collect();
        $kontaks = collect();
        $grand_total = 0;
        $lunas_total = 0;

        foreach ($pembelians as $pembelian) {
            $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian->id)->get();
            // Menghitung $real_count_grand_total
            $harga_total = 0;
            foreach ($pembelian_barangs as $pembelian_barang) {
                $harga_total += $pembelian_barang->harga_t;
            }
            $pembelian_barangs_all->push($pembelian_barangs);
            $real_count_grand_total += $harga_total;

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
        $label_barang = Barang::select('id', 'nama as label', 'nama as value', 'supplier_id', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $labelSupplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $labelBarang = Barang::select('id', 'nama as label', 'nama as value', 'supplier_id', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        // Pembelian Total Supplier
        // dump($pembelians);
        $pembelian_grouped_supplier = $pembelians->groupBy('supplier_nama');
        // dump($pembelian_grouped_supplier);
        $pembelian_total_suppliers = collect();
        foreach ($pembelian_grouped_supplier as $pembelian_grouped_supp) {
            $pembelian_total = 0;
            $pembelian_lunas = 0;
            $pembelian_BELUM_LUNAS = 0;
            $supplier_nama = '';
            foreach ($pembelian_grouped_supp as $pembelian_grouped_s) {
                $pembelian_total += (float)$pembelian_grouped_s->harga_total;
                if ($pembelian_grouped_s->status_bayar === 'BELUM_LUNAS') {
                    $pembelian_BELUM_LUNAS += (float)$pembelian_grouped_s->harga_total;
                } elseif ($pembelian_grouped_s->status_bayar === 'LUNAS') {
                    $pembelian_lunas += (float)$pembelian_grouped_s->harga_total;
                }
                $supplier_nama = $pembelian_grouped_s->supplier_nama;
            }
            $pembelian_total_suppliers->push([
                'supplier_nama' => $supplier_nama,
                'pembelian_total' => $pembelian_total,
                'pembelian_lunas' => $pembelian_lunas,
                'pembelian_BELUM_LUNAS' => $pembelian_BELUM_LUNAS,
            ]);
        }
        // dd($pembelian_total_suppliers);
        // END - Pembelian Total Supplier
        // if (count($pembelians)) {
        //     dump($pembelians->firstWhere('id', 226));
        //     dump($pembelians->firstWhere('id', 226)->accountingInvoices);
        //     dd($pembelians->firstWhere('id', 226)->latestAccountingInvoice);
        // }
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'pembelians' => $pembelians,
            'pembelian_barangs_all' => $pembelian_barangs_all,
            'real_count_grand_total' => $real_count_grand_total,
            'alamats' => $alamats,
            'kontaks' => $kontaks,
            'label_supplier' => $label_supplier,
            'label_barang' => $label_barang,
            'labelSupplier' => $labelSupplier,
            'labelBarang' => $labelBarang,
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
        // dump($pembelian);
        // dump($pembelian->pembelianBarangs);
        // $pembelian_barangs = $pembelian->pembelianBarangs;
        // foreach($pembelian_barangs as $pembelian_barang) {
        //     dump($pembelian_barang);
        // }
        // dd('stop');
        $label_barang = Barang::select('id', 'nama as label', 'nama as value')->get();
        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.show',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian' => $pembelian,
            'label_barang' => $label_barang,
        ];
        return view('pembelians.show', $data);
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
            'barang_id' => 'required|array',
            'harga_t' => 'required|array',
            'jumlah_main' => 'required|array',
            'jumlah_sub' => 'nullable|array',
            'harga_main.*' => 'nullable|numeric|min:1',
            'jumlah_main.*' => 'nullable|numeric|min:1',
            'jumlah_sub.*' => 'nullable|numeric|min:1',
        ]);

        $supplier = Supplier::find($post['supplier_id']);
        $user = Auth::user();

        $success_ = '';
        $warnings_ = '';

        $barangList = Barang::whereIn('id', $post['barang_id'])->get()->keyBy('id');
        // dd($barangList);
        foreach ($barangList as $barang) {
            if($barang->supplier_id != $supplier->id) {
                $request->validate(['error'=>'required'],['error.required'=>'Barang dan Supplier tidak sesuai']);
            }
        }

        DB::beginTransaction();
        try {
            $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', "{$post['year']}-{$post['month']}-{$post['day']} " . now()->format('H:i:s'));
            $pembelian_new = Pembelian::create([
                'supplier_id' => $supplier->id,
                'supplier_nama' => $supplier->nama,
                'creator' => $user->username,
                'created_at' => $createdAt,
            ]);
            $isiMap = [];
            foreach ($post['barang_id'] as $i => $barang_id) {
                $barang = $barangList[$barang_id] ?? null;
                $harga_t = (float) $post['harga_t'][$i];
                $jumlah_main = (int) $post['jumlah_main'][$i];
                
                if (!$barang || $harga_t == 0 || $jumlah_main == 0) {
                    $warnings_ .= "-failed to process-index: $i-";
                    continue;
                }
    
                // proses pembelian_barang...
                $harga_main = (float)$post['harga_main'][$i];
                $harga_sub = $harga_main * (float)$post['jumlah_main'][$i];
    
                $pembelian_barang = PembelianBarang::create([
                    'pembelian_id' => $pembelian_new->id,
                    'barang_id' => $barang->id,
                    'barang_nama' => $barang->nama,
                    'satuan_main' => $barang->satuan_main,
                    'jumlah_main' => $post['jumlah_main'][$i],
                    'harga_main' => $harga_main,
                    'satuan_sub' => $barang->satuan_sub,
                    'jumlah_sub' => $post['jumlah_sub'][$i],
                    'harga_sub' => $harga_sub,
                    'harga_t' => $post['harga_t'][$i],
                    'creator' => $user->username,
                ]);
    
                $success_ .= '-pembelian_barang created-';

                // Insert ke tabel goods_prices apabila harga_main tidak sama dengan harga_main terakhir
                $this->pembelianService->updateGoodsPrice($barang, $pembelian_barang, $user, $success_);
    
    
                // $key_main = ctype_upper($pembelian_barang->satuan_main) ? strtolower($pembelian_barang->satuan_main) : $pembelian_barang->satuan_main;
                $key_main = strtolower($pembelian_barang->satuan_main);
                $isiMap[$key_main] = ($isiMap[$key_main] ?? 0) + $pembelian_barang->jumlah_main;
    
                if ($pembelian_barang->satuan_sub) {
                    // $key_sub = ctype_upper($pembelian_barang->satuan_sub) ? strtolower($pembelian_barang->satuan_sub) : $pembelian_barang->satuan_sub;
                    $key_sub = strtolower($pembelian_barang->satuan_sub);
                    $isiMap[$key_sub] = ($isiMap[$key_sub] ?? 0) + $pembelian_barang->jumlah_sub;
                }
    
            }
            
            $isi = [];
            foreach ($isiMap as $satuan => $jumlah) {
                $isi[] = ['satuan' => $satuan, 'jumlah' => $jumlah];
            }

            $nomor_nota = "N-$pembelian_new->id";
            if ($post['nomor_nota'] != null) {
                $nomor_nota = $post['nomor_nota'];
            }

            $harga_total = round((float)$post['harga_total'],2);
            $pembelian_new->update([
                'nomor_nota' => $nomor_nota,
                'isi' => json_encode($isi),
                'harga_total' => $harga_total,
                'amount_due' => $harga_total,
                // 'status_bayar' => $status_bayar,
                // 'keterangan_bayar' => $keterangan_bayar,
                // 'tanggal_lunas' => $tanggal_lunas,
                // 'created_at' => $tanggal_lunas,
            ]);
            $success_ .= '-pembelian new created-';

            DB::commit();

            $feedback = [
                'success_' => $success_,
                'warnings_' => $warnings_,
            ];

            return back()->with($feedback);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal membuat pembelian baru: ' . $e->getMessage());
        }
    }

    function delete(Pembelian $pembelian) {
        // dump($pembelian->accountingInvoices()->get());
        // dd($pembelian->latestAccountingInvoice()->first());
        if ($pembelian->status_bayar === 'LUNAS' || $pembelian->status_bayar === 'SEBAGIAN') {
            return back()->withErrors('Tidak dapat menghapus pembelian yang sudah terjadi pembayaran. Accounting terkait pembelian ini harus dihapus terlebih dahulu.');
        }
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

        DB::beginTransaction();
        try {
            $jumlah_bayar_total = (float)$post['jumlah_bayar'] + (float)$pembelian->amount_paid;
            $new_amount_due = (float)$pembelian->harga_total - $jumlah_bayar_total;
            $keterangan_bayar = $pembelian->keterangan_bayar ?? null;
            $keterangan_bayar = $post['keterangan_bayar'] ?? $keterangan_bayar;
            $tanggal_lunas = date('Y-m-d', strtotime("$post[year]-$post[month]-$post[day]")) . " " . date('H:i:s');
            $status_bayar = Pembelian::new_status_bayar($pembelian, $jumlah_bayar_total);
            $tanggal_lunas = $status_bayar === 'BELUM_LUNAS' ? null : $tanggal_lunas;

            $pembelian->tanggal_lunas = $tanggal_lunas;
            $pembelian->amount_due = $new_amount_due;
            $pembelian->amount_paid = $jumlah_bayar_total;
            $pembelian->status_bayar = $status_bayar;
            $pembelian->keterangan_bayar = $keterangan_bayar;
            $pembelian->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal memperbarui data pembayaran/pelunasan: ' . $e->getMessage());
        }

        return back()->with('success_', '-data_pembayaran/pelunasan updated-');
    }

    function pembatalan_pelunasan(Pembelian $pembelian) {
        dd("Fitur ini sudah tidak digunakan");
        $pembelian->tanggal_lunas = null;
        $pembelian->status_bayar = 'BELUM_LUNAS';
        $pembelian->keterangan_bayar = null;
        $pembelian->save();

        return back()->with('warnings_', '-pelunasan dibatalkan-');
    }

    function edit(Pembelian $pembelian) {
        if ($pembelian->status_bayar == 'LUNAS' || $pembelian->status_bayar == 'SEBAGIAN') {
            return back()->withErrors('Tidak dapat mengedit pembelian yang sudah terjadi pembayaran. Accounting terkait pembelian ini harus dihapus terlebih dahulu.');
        }
        $pembelian_barangs = PembelianBarang::where('pembelian_id', $pembelian->id)->get();

        $labelSupplier = Supplier::select('id', 'nama as label', 'nama as value')->orderBy('nama')->get();
        $labelBarang = Barang::select('id', 'nama as label', 'nama as value', 'supplier_id', 'satuan_sub', 'satuan_main', 'satuan_sub', 'harga_main', 'jumlah_main', 'harga_total_main')->orderBy('nama')->get();

        $data = [
            'menus' => Menu::get(),
            'route_now' => 'pembelians.index',
            'parent_route' => 'pembelians.index',
            'profile_menus' => Menu::get_profile_menus(),
            'pembelian_menus' => Menu::get_pembelian_menus(),
            'pembelian' => $pembelian,
            'pembelian_barangs' => $pembelian_barangs,
            'labelSupplier' => $labelSupplier,
            'labelBarang' => $labelBarang,
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
            'barang_id' => 'required|array',
            'harga_t' => 'required|array',
            'jumlah_main' => 'required|array',
            'jumlah_sub' => 'nullable|array',
            'harga_main.*' => 'nullable|numeric|min:1',
            'jumlah_main.*' => 'nullable|numeric|min:1',
            'jumlah_sub.*' => 'nullable|numeric|min:1',
        ]);

        $supplier = Supplier::find($post['supplier_id']);
        $user = Auth::user();

        $nomor_nota = "N-$pembelian->id";
        if (isset($post['nomor_nota']) && $post['nomor_nota'] !== null) {
            $nomor_nota = $post['nomor_nota'];
        }
        
        $success_ = '';
        $warnings_ = '';

        $barangList = Barang::whereIn('id', $post['barang_id'])->get()->keyBy('id');
        // dd($barangList);
        foreach ($barangList as $barang) {
            if($barang->supplier_id != $supplier->id) {
                $request->validate(['error'=>'required'],['error.required'=>'Barang dan Supplier tidak sesuai']);
            }
        }

        DB::beginTransaction();
        try {
            $pembelian->update([
                'nomor_nota' => $nomor_nota,
                'supplier_id' => $supplier->id,
                'supplier_nama' => $supplier->nama,
                'updater' => $user->username,
                'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', "{$post['year']}-{$post['month']}-{$post['day']} " . now()->format('H:i:s')),
            ]);

            // Keterangan isi pembelian akan di lakukan setelah perubahan dilakukan
            
            // Pada saat update, perlu cek pembelian_barang_id yang sebelumnya apakah ada yang dihapus
            $existing_pembelian_barang_ids = PembelianBarang::where('pembelian_id', $pembelian->id)->pluck('id')->toArray();
            $pembelian_barang_ids_to_delete = array_diff($existing_pembelian_barang_ids, $post['pembelian_barang_id']);
            // dump($existing_pembelian_barang_ids);
            // dd($pembelian_barang_ids_to_delete);
            if (count($pembelian_barang_ids_to_delete) > 0) {
                PembelianBarang::whereIn('id', $pembelian_barang_ids_to_delete)->delete();
                $success_ .= '-deleted pembelian_barang ids: ' . implode(',', $pembelian_barang_ids_to_delete) . '-';
            }

            for ($i=0; $i < count($post['pembelian_barang_id']); $i++) {
                // if ($barang === null) { // kasus dimana barang memang sudah dihapus namun apa yang sudah tercantum pada nota pembelian, tidak terhapus, namun barang_id menjadi null
                // }
                $pembelian_barang = null;
                $barang = $barangList[$post['barang_id'][$i]] ?? null;
                if ($post['pembelian_barang_id'][$i] === 'new') {
                    // dd($barang);

                    $harga_main = (float)$post['harga_main'][$i];
                    $harga_sub = $harga_main * (int)$post['jumlah_main'][$i];

                    $pembelian_barang = PembelianBarang::create([
                        'pembelian_id' => $pembelian->id,
                        'barang_id' => $barang->id,
                        'barang_nama' => $barang->nama,
                        'satuan_main' => $barang->satuan_main,
                        'jumlah_main' => $post['jumlah_main'][$i],
                        'harga_main' => $harga_main,
                        'satuan_sub' => $barang->satuan_sub,
                        'jumlah_sub' => $post['jumlah_sub'][$i],
                        'harga_sub' => $harga_sub,
                        'harga_t' => $post['harga_t'][$i],
                        // 'status_bayar' => null,
                        // 'keterangan_bayar' => null,
                        // 'tanggal_lunas' => null,
                        // 'created_at' => $pembelian_barang->created_at, // sudah otomatis
                        // 'updated_at' => $pembelian_barang->updated_at,
                        'creator' => $user->username,
                        // 'updater' => $user->username,
                    ]);

                    $success_ .= '-new pembelian_barang created-';
                } else {
                    $pembelian_barang = PembelianBarang::find($post['pembelian_barang_id'][$i]);
                    // dd($pembelian_barang);
                    $harga_main = (float)$post['harga_main'][$i];
                    $harga_sub = $harga_main * (float)$post['jumlah_main'][$i];

                    $pembelian_barang->update([
                        'barang_id' => $pembelian_barang->barang_id,
                        'barang_nama' => $pembelian_barang->barang_nama,
                        'satuan_main' => $pembelian_barang->satuan_main,
                        'jumlah_main' => $post['jumlah_main'][$i],
                        'harga_main' => $harga_main,
                        'satuan_sub' => $pembelian_barang->satuan_sub,
                        'jumlah_sub' => $post['jumlah_sub'][$i],
                        'harga_sub' => $harga_sub,
                        'harga_t' => $post['harga_t'][$i],
                        // 'status_bayar' => null,
                        // 'keterangan_bayar' => null,
                        // 'tanggal_lunas' => null,
                        // 'created_at' => $pembelian_barang->created_at, // sudah otomatis
                        // 'updated_at' => $pembelian_barang->updated_at,
                        // 'creator' => $user->username,
                        'updater' => $user->username,
                    ]);
                    $success_ .= '-pembelian_barang updated-';
                }

                if ($pembelian_barang) {
                    $this->pembelianService->updateGoodsPrice($barang, $pembelian_barang, $user, $success_);
                }
            }

            $isiMap = [];
            foreach (PembelianBarang::where('pembelian_id', $pembelian->id)->get() as $pembelian_barang) {
                $key_main = strtolower($pembelian_barang->satuan_main);
                $isiMap[$key_main] = ($isiMap[$key_main] ?? 0) + $pembelian_barang->jumlah_main;

                if ($pembelian_barang->satuan_sub) {
                    $key_sub = strtolower($pembelian_barang->satuan_sub);
                    $isiMap[$key_sub] = ($isiMap[$key_sub] ?? 0) + $pembelian_barang->jumlah_sub;
                }
            }
            $isi = [];
            foreach ($isiMap as $satuan => $jumlah) {
                $isi[] = ['satuan' => $satuan, 'jumlah' => $jumlah];
            }

            $pembelian->update([
                'nomor_nota' => $nomor_nota,
                'isi' => json_encode($isi),
                'harga_total' => $post['harga_total'],
                // 'status_bayar' => $status_bayar,
                // 'keterangan_bayar' => $keterangan_bayar,
                // 'tanggal_lunas' => $tanggal_lunas,
                // 'created_at' => $tanggal_lunas,
            ]);
            $success_ .= '-pembelian updated-';
            DB::commit();

            $feedback = [
                'success_' => $success_,
            ];

            return back()->with($feedback);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal update pembelian: ' . $e->getMessage());
        }

        
    }

    function delete_pembelian_barang(Pembelian $pembelian, PembelianBarang $pembelian_barang) {
        // dump($pembelian);
        // dd($pembelian_barang);
        if ($pembelian->status_bayar == 'LUNAS' || $pembelian->status_bayar == 'SEBAGIAN') {
            dd('Tidak dapat mengedit pembelian yang sudah terjadi pembayaran. Accounting terkait pembelian ini harus dihapus terlebih dahulu.');
        }

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

    public function changePembelianBarang(PembelianBarang $pembelian_barang, Request $request) {
        $post = $request->validate([
            'barang_id' => 'required|numeric',
            'barang_nama' => 'required|string',
        ]);
        // dump($post);
        // dd($pembelian_barang);
        $success_ = '';
        $pembelian_barang->update([
            'barang_id' => $post['barang_id'],
            'barang_nama' => $post['barang_nama'],
        ]);
        $success_ .= 'pembelian_barang diupdate-';
        return back()->with('success_', $success_);
    }
}
