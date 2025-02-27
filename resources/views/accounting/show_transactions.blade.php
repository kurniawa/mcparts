@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
<main>
    <div class="m-2 text-xs">
        <x-errors-any></x-errors-any>
        <x-validation-feedback></x-validation-feedback>
    </div>
    <div class="mx-1 py-1 sm:px-6 lg:px-8 relative">
        <h1 class="text-xl font-bold">Data Transaksi - <span class="text-slate-500">{{ $user->username }}</span></h1>
        <div class="flex">
            <div id="filter-content">
                <div class="rounded p-2 bg-white shadow drop-shadow">
                    <form action="" method="GET" class="text-xs">
                        <div class="flex items-end">
                            <div>
                                <label>Desc:</label>
                                <div class="flex mt-1">
                                    <input type="text" class="border rounded text-xs p-1" name="desc" placeholder="Deskripsi/Keterangan" id="filter-desc">
                                </div>
                            </div>
                            <div class="flex items-center ml-2 gap-2">
                                <div><input type="radio" name="timerange" value="triwulan" id="triwulan" onclick="set_time_range('triwulan')"><label for="triwulan" class="ml-1">triwulan</label></div>
                                <div><input type="radio" name="timerange" value="triwulan_lalu" id="triwulan_lalu" onclick="set_time_range('triwulan_lalu')"><label for="triwulan_lalu" class="ml-1">triwulan lalu</label></div>
                                <div><input type="radio" name="timerange" value="7d" id="7d" onclick="set_time_range('7d')"><label for="7d" class="ml-1">7d</label></div>
                                {{-- <div><input type="radio" name="timerange" value="30d" id="30d" onclick="set_time_range('30d')"><label for="30d" class="ml-1">30d</label></div> --}}
                                <div><input type="radio" name="timerange" value="bulan_ini" id="bulan_ini" onclick="set_time_range('bulan_ini')"><label for="bulan_ini" class="ml-1">bulan ini</label></div>
                                <div><input type="radio" name="timerange" value="bulan_lalu" id="bulan_lalu" onclick="set_time_range('bulan_lalu')"><label for="bulan_lalu" class="ml-1">bulan lalu</label></div>
                                <div><input type="radio" name="timerange" value="this_year" id="tahun_ini" onclick="set_time_range('tahun_ini')"><label for="tahun_ini" class="ml-1">tahun ini</label></div>
                                <div><input type="radio" name="timerange" value="last_year" id="tahun_lalu" onclick="set_time_range('tahun_lalu')"><label for="tahun_lalu" class="ml-1">tahun lalu</label></div>
                            </div>
                        </div>
                        <div class="mt-2 flex items-end">
                            <div class="flex">
                                <div>
                                    <label>Dari:</label>
                                    <div class="flex">
                                        <select name="from_day" id="from_day" class="rounded text-xs py-1">
                                            <option value="">-</option>
                                            @for ($i = 1; $i < 32; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select name="from_month" id="from_month" class="rounded text-xs py-1 ml-1">
                                            <option value="">-</option>
                                            @for ($i = 1; $i < 13; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select name="from_year" id="from_year" class="rounded text-xs py-1 ml-1">
                                            <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                            <option value="">-</option>
                                            @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <label>Sampai:</label>
                                    <div class="flex items-center">
                                        <select name="to_day" id="to_day" class="rounded text-xs py-1">
                                            <option value="">-</option>
                                            @for ($i = 1; $i < 32; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select name="to_month" id="to_month" class="rounded text-xs py-1 ml-1">
                                            <option value="">-</option>
                                            @for ($i = 1; $i < 13; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select name="to_year" id="to_year" class="rounded text-xs py-1 ml-1">
                                            <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                            <option value="">-</option>
                                            @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs">
                                <button type="submit" class="ml-2 flex items-center bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <span class="ml-1">filter</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div>
                <div class="border rounded p-1 ml-2">
                    <table class="text-xs">
                        <tr><td>Tipe Instansi</td><td>:</td><td><div class="ml-2">{{ $user_instance->instance_type }}</div></td></tr>
                        <tr><td>Nama Instansi</td><td>:</td><td><div class="ml-2">{{ $user_instance->instance_name }}</div></td></tr>
                        <tr><td>Branch</td><td>:</td><td><div class="ml-2">{{ $user_instance->instance_branch }}</div></td></tr>
                        @if ($user_instance->account_number)
                        <tr><td>Nomor Rek.</td><td>:</td><td><div class="ml-2">{{ $user_instance->account_number }}</div></td></tr>
                        @endif
                    </table>
                </div>
            </div>
            {{-- <div>
                <button type="submit" id="btn_new_kas" class="border font-semibold rounded text-violet-500 border-violet-300 px-1 ml-2" onclick="toggle_light(this.id, 'form_new_kas', [], ['bg-violet-200'], 'flex')">+ NEW KAS</button>
            </div> --}}
        </div>

        {{-- TRANSACTIONS --}}
        <div class="mt-2">
            <table class="text-xs table-border w-3/4 max-w-full">
                <tr>
                    <th>
                        <div class="flex">
                            <button class="rounded bg-emerald-400 text-white p-1" onclick="table_to_excel('table-transactions')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3v-6m1.06-4.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                            </button>
                        </div>
                    </th>
                    <th></th><th></th>
                    <th>
                        <div class="flex justify-between bg-pink-300">
                            <span>Rp</span>
                            <span>{{ number_format($keluar_total / 100,2,',','.') }}</span>
                        </div>
                    </th>
                    <th>
                        <div class="flex justify-between bg-emerald-300">
                            <span>Rp</span>
                            <span>{{ number_format($masuk_total / 100,2,',','.') }}</span>
                        </div>
                    </th>
                    <th>
                        <div class="flex justify-between bg-yellow-300">
                            <span>Rp</span>
                            @if ($from)
                            @if (count($accountings) !== 0)
                            <span>{{ number_format($accountings[count($accountings) - 1]->saldo / 100,2,',','.') }}</span>
                            @else
                            <span>0</span>
                            @endif
                            @else
                            <span>?</span>
                            @endif
                        </div>
                    </th>
                </tr>
                <tr class="bg-blue-500 text-white"><th>TANGGAL</th><th>KODE</th><th>KETERANGAN</th><th>KELUAR</th><th>MASUK</th><th>SALDO</th></tr>

                <tr>
                    <td></td>
                    <td></td>
                    <td><span class="font-bold text-orange-400">SALDO AWAL</span></td>
                    <td></td><td></td>
                    <td>
                        <div class="flex justify-between">
                            <span>Rp.</span>
                            <span>{{ number_format($saldo_awal / 100,2,',','.') }}</span>
                        </div>
                    </td>
                </tr>
                @foreach ($accountings as $key_accounting => $accounting)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($accounting->created_at)) }}</td>
                    <td>{{ $accounting->kode }}</td>
                    @if ($accounting->keterangan !== null)
                    <td>{{ $accounting->transaction_desc }} <span class="font-bold text-blue-300">- {{ $accounting->keterangan }} -</span></td>
                    @else
                    <td>{{ $accounting->transaction_desc }}</td>
                    @endif
                    <td>
                        @if ($accounting->transaction_type === 'pengeluaran')
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->jumlah / 100,2,',','.') }}</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        @if ($accounting->transaction_type === 'pemasukan')
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->jumlah / 100,2,',','.') }}</span>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="flex justify-between">
                            <span>Rp</span>
                            <span>{{ number_format($accounting->saldo / 100,2,',','.') }}</span>
                        </div>
                    </td>
                    @if ((int)$user_instance->user_id === $user->id)
                    <td class="border_none">
                        <button id="btn_edit_transaction-{{ $key_accounting }}" class="rounded bg-white shadow drop-shadow" onclick="showDropdown(this.id, 'tr_edit_transaction-{{ $key_accounting }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </td>
                    @endif
                    @if ((int)$user_instance->user_id === $user->id)
                    @if ($fitur_up_down_transaction)
                    <td>
                        <div>
                            <form action="{{ route('accounting.up_down_transaction', [$user_instance->id, $accounting->id]) }}" class="flex" method="POST" onsubmit="return confirm('up sekali?')">
                                @csrf
                                <div class="flex">
                                    <button class="rounded bg-indigo-300" type="submit" name="up_down_transaction" value="up">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div>
                            <form action="{{ route('accounting.up_down_transaction', [$user_instance->id, $accounting->id]) }}" class="flex" method="POST" onsubmit="return confirm('down sekali?')">
                                @csrf
                                <div>
                                    <button class="rounded bg-orange-300" type="submit" name="up_down_transaction" value="down">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </td>
                    @endif
                    @endif
                </tr>

                @if ((int)$user_instance->user_id === $user->id)
                <tr class="hidden" id="tr_edit_transaction-{{ $key_accounting }}">
                    <td colspan="6">
                        <div class="text-center">
                            <form action="{{ route('accounting.edit_entry', [$user_instance->id, $accounting->id]) }}" method="POST" class="inline-block p-1 rounded bg-white shadow drop-shadow">
                                @csrf
                                <h3 class="text-lg font-bold text-slate-500">Edit Transaksi</h3>
                                <div class="flex">
                                    <div>
                                        <label for="" class="block">tanggal:</label>
                                        <input type="text" name="created_at" id="edit-{{ $key_accounting }}-created_at" class="border p-1 text-xs mt-1 w-28" placeholder="dd-mm-yyyy" value="{{ date('d-m-Y', strtotime($accounting->created_at)) }}">
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">kode:</label>
                                        <input type="text" name="kode" id="edit-{{ $key_accounting }}-kode" class="border p-1 text-xs mt-1 w-20" value="{{ $user_instance->kode }}" value="{{ $accounting->kode }}">
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">deskripsi/keterangan:</label>
                                        <input type="text" name="transaction_desc" id="edit-{{ $key_accounting }}-transaction_desc" class="border p-1 text-xs mt-1 w-60" value="{{ $accounting->transaction_desc }}">
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">keterangan tambahan:</label>
                                        <input type="text" name="keterangan" id="edit-{{ $key_accounting }}-keterangan" class="border p-1 text-xs mt-1 w-60" value="{{ $accounting->keterangan }}">
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">keluar:</label>
                                        @if ($accounting->transaction_type === 'pengeluaran')
                                        <input type="text" id="edit-{{ $key_accounting }}-keluar" class="border p-1 text-xs mt-1 w-36" value="{{ number_format($accounting->jumlah / 100,0,',','.') }}" onchange="formatNumber(this, 'edit-{{ $key_accounting }}-keluar-unformatted')">
                                        <input type="hidden" name="keluar" id="edit-{{ $key_accounting }}-keluar-unformatted" value="{{ $accounting->jumlah / 100 }}">
                                        @else
                                        <input type="text" id="edit-{{ $key_accounting }}-keluar" class="border p-1 text-xs mt-1 w-36">
                                        <input type="hidden" name="keluar" id="edit-{{ $key_accounting }}-keluar-unformatted">
                                        @endif
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">masuk:</label>
                                        @if ($accounting->transaction_type === 'pemasukan')
                                        <input type="text" id="edit-{{ $key_accounting }}-masuk" class="border p-1 text-xs mt-1 w-36" value="{{ number_format($accounting->jumlah / 100,0,',','.') }}" onchange="formatNumber(this, 'edit-{{ $key_accounting }}-masuk-unformatted')">
                                        <input type="hidden" name="masuk" id="edit-{{ $key_accounting }}-masuk-unformatted" value="{{ $accounting->jumlah / 100 }}">
                                        @else
                                        <input type="text" id="edit-{{ $key_accounting }}-masuk" class="border p-1 text-xs mt-1 w-36">
                                        <input type="hidden" name="masuk" id="edit-{{ $key_accounting }}-masuk-unformatted">
                                        @endif
                                    </div>
                                    {{-- <input type="hidden" name="transaction_id" id="edit-{{ $key_accounting }}-transaction_id" value="{{ $accounting }}"> --}}
                                </div>
                                {{-- <div class="flex mt-2">
                                    <div>
                                        <label for="" class="block">pelanggan:</label>
                                        <input type="text" name="pelanggan_nama" id="edit-{{ $key_accounting }}-pelanggan_nama" class="border p-1 text-xs mt-1" value="{{ $accounting->pelanggan_nama }}">
                                        <input type="hidden" name="pelanggan_id" id="edit-{{ $key_accounting }}-pelanggan_id" value="{{ $accounting->pelanggan_id }}">
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">supplier:</label>
                                        <input type="text" name="supplier_nama" id="edit-{{ $key_accounting }}-supplier_nama" class="border p-1 text-xs mt-1" value="{{ $accounting->supplier_nama }}">
                                        <input type="hidden" name="supplier_id" id="edit-{{ $key_accounting }}-supplier_id" value="{{ $accounting->supplier_id }}">
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">related_user:</label>
                                        <input type="text" name="related_username" id="edit-{{ $key_accounting }}-related_username" class="border p-1 text-xs mt-1" value="{{ $accounting->related_username }}">
                                        <input type="hidden" name="related_user_id" id="edit-{{ $key_accounting }}-related_user_id" value="{{ $accounting->related_user_id }}">
                                    </div>
                                    <div class="ml-1">
                                        <label for="" class="block">related_desc:</label>
                                        <input type="text" name="related_desc" id="edit-{{ $key_accounting }}-related_desc" class="border p-1 text-xs mt-1 w-56" value="{{ $accounting->related_desc }}">
                                    </div>
                                </div> --}}
                                {{-- <table class="table-slim mt-1">
                                    <tr>
                                        <td>Pelanggan</td><td>:</td><td></td>
                                    </tr>
                                </table> --}}
                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="border-2 font-semibold rounded text-emerald-500 border-emerald-300 bg-emerald-200 px-2">confirm edit</button>
                                </div>
                            </form>
                            <form action="{{ route('accounting.delete_entry', [$user_instance->id, $accounting->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus transaksi ini?')" class="mt-1">
                                @csrf
                                <div class="flex justify-center">
                                    <button type="submit" class="border-2 font-semibold rounded text-pink-500 border-pink-300 bg-pink-200 px-2">hapus transaksi</button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </table>
        </div>
        {{-- END - TRANSACTIONS --}}

        {{-- PRINT OUT TRANSACTIONS --}}
        <div class="hidden">
            <table id="table-transactions">
                <tr>
                    <th></th><th></th><th></th>
                    <th>{{ $keluar_total / 100 }}</th>
                    <th>{{ $masuk_total / 100 }}</th>
                    <th>
                        @if ($from)
                        @if (count($accountings) !== 0)
                        <span>{{ $accountings[count($accountings) - 1]->saldo / 100 }}</span>
                        @else
                        <span>0</span>
                        @endif
                        @else
                        <span>?</span>
                        @endif
                    </th>
                </tr>
                <tr><th>TANGGAL</th><th>KODE</th><th>KETERANGAN</th><th>KELUAR</th><th>MASUK</th><th>SALDO</th></tr>

                <tr>
                    <td></td>
                    <td></td>
                    <td><span>SALDO AWAL</span></td>
                    <td></td><td></td>
                    <td>{{ $saldo_awal }}</td>
                </tr>
                @foreach ($accountings as $key_accounting => $accounting)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($accounting->created_at)) }}</td>
                    <td>{{ $accounting->kode }}</td>
                    @if ($accounting->keterangan !== null)
                    <td>{{ $accounting->transaction_desc }} ({{ $accounting->keterangan }})</td>
                    @else
                    <td>{{ $accounting->transaction_desc }}</td>
                    @endif
                    <td>
                        @if ($accounting->transaction_type === 'pengeluaran')
                        {{ $accounting->jumlah / 100 }}
                        @endif
                    </td>
                    <td>
                        @if ($accounting->transaction_type === 'pemasukan')
                        {{ $accounting->jumlah / 100 }}
                        @endif
                    </td>
                    <td>
                        {{ $accounting->saldo / 100 }}
                    </td>

                </tr>
                @endforeach
            </table>
        </div>
        {{-- END - PRINT OUT TRANSACTIONS --}}

        {{-- STORE NEW TRANSACTIONS --}}
        @if ((int)$user_instance->user_id === $user->id)
        <div class="flex flex-col lg:flex-row lg:gap-2 mt-3">
            <div class="border rounded p-1">
                {{-- LOADING ANIMATION --}}
                <div id="spinner" class="flex justify-center items-center">
                    <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
                </div>
                {{-- END - LOADING ANIMATION --}}
                <div id="loading_to_hide">
                    <h2 class="font-bold text-slate-500">Tambah Transaksi :</h2>
                    <form action="{{ route('accounting.store_transactions', $user_instance->id) }}" onsubmit="return set_scroll_here()" method="POST" class="mt-1 inline-block min-w-max">
                        @csrf
                        <table class="text-xs min-w-max" id="table_add_transactions">
                            <tr class="text-slate-600">
                                <th>tanggal</th><th>kode</th><th>deskripsi/keterangan</th><th>keterangan tambahan</th><th>keluar</th><th>masuk</th>
                                {{-- <th>saldo</th> --}}
                            </tr>
                            @for ($i = 0; $i < 7; $i++)
                            <tr>
                                <td>
                                    {{-- <input type="text" name="created_at[]" id="created_at-{{ $i }}" class="border p-1 text-xs w-28" placeholder="dd-mm-yyyy" value="{{ old('created_at.' . $i) }}"> --}}
                                    <div class="flex items-center">
                                        <input type="text" name="day[]" id="day-{{ $i }}" class="border p-1 text-xs w-8" placeholder="dd" value="{{ old('day.' . $i) ? old('day.' . $i) : date('d') }}">
                                        <span>-</span>
                                        <input type="text" name="month[]" id="month-{{ $i }}" class="border p-1 text-xs w-8" placeholder="mm" value="{{ old('month.' . $i) ? old('month.' . $i) : date('m') }}">
                                        <span>-</span>
                                        <input type="text" name="year[]" id="year-{{ $i }}" class="border p-1 text-xs w-10" placeholder="yyyy" value="{{ old('year.' . $i) ? old('year.' . $i) : date('Y') }}">
                                    </div>
                                </td>
                                <td><input type="text" name="kode[]" id="kode-{{ $i }}" class="border p-1 text-xs w-20" value="{{ old('kode.' . $i) ? old('kode.' . $i) : $user_instance->kode }}"></td>
                                <td><input type="text" name="transaction_desc[]" id="transaction_desc-{{ $i }}" class="border p-1 text-xs w-60" value="{{ old('transaction_desc.' . $i) }}"></td>
                                <td><input type="text" name="keterangan[]" id="keterangan-{{ $i }}" class="border p-1 text-xs w-full" value="{{ old('keterangan.' . $i) }}"></td>
                                <td>
                                    <input type="text" id="keluar-{{ $i }}" class="border p-1 text-xs w-36" onchange="formatNumber(this, 'keluar-{{ $i }}-unformatted')" value="{{ old('keluar.' . $i) ? number_format((int)old('keluar.' . $i),0,',','.') : "" }}">
                                    <input type="hidden" name="keluar[]" id="keluar-{{ $i }}-unformatted" value="{{ old('keluar.' . $i) }}">
                                </td>
                                <td>
                                    <input type="text" id="masuk-{{ $i }}" class="border p-1 text-xs w-36" onchange="formatNumber(this, 'masuk-{{ $i }}-unformatted')" value="{{ old('masuk.' . $i) ? number_format((int)old('masuk.' . $i),0,',','.') : "" }}">
                                    <input type="hidden" name="masuk[]" id="masuk-{{ $i }}-unformatted" value="{{ old('masuk.' . $i) }}">
                                    <input type="hidden" name="transaction_id[]" id="transaction_id-{{ $i }}" value="{{ old('transaction_id.' . $i) }}">
                                </td>
                                <td>
                                    {{-- <input type="text" name="saldo[]" id="saldo-{{ $i }}" class="border p-1 text-xs w-36"> --}}
                                    {{-- <input type="hidden" name="kategori_type[]" id="new_transaction-kategori_type-{{ $i }}">
                                    <input type="hidden" name="kategori_level_one[]" id="new_transaction-kategori_level_one-{{ $i }}">
                                    <input type="hidden" name="kategori_level_two[]" id="new_transaction-kategori_level_two-{{ $i }}">
                                    <input type="hidden" name="related_user_id[]" id="new_transaction-related_user_id-{{ $i }}">
                                    <input type="hidden" name="pelanggan_nama[]" id="new_transaction-pelanggan_nama-{{ $i }}">
                                    <input type="hidden" name="pelanggan_id[]" id="new_transaction-pelanggan_id-{{ $i }}">
                                    <input type="hidden" name="related_desc[]" id="new_transaction-related_desc-{{ $i }}">
                                    <input type="hidden" name="related_user_instance_type[]" id="new_transaction-related_user_instance_type-{{ $i }}">
                                    <input type="hidden" name="related_user_instance_id[]" id="new_transaction-related_user_instance_id-{{ $i }}">
                                    <input type="hidden" name="related_user_instance_name[]" id="new_transaction-related_user_instance_name-{{ $i }}">
                                    <input type="hidden" name="related_user_instance_branch[]" id="new_transaction-related_user_instance_branch-{{ $i }}"> --}}
                                </td>
                                {{-- <td>
                                    <div>
                                        <button type="button" id="toggle-opsi_relasi-{{ $i }}" class="rounded bg-white shadow drop-shadow" onclick="showDropdown(this.id, 'opsi_relasi-{{ $i }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </div>
                                </td> --}}
                            </tr>
                            {{-- <tr class="hidden" id="opsi_relasi-{{ $i }}">
                                <td colspan="2">
                                    <div class="text-slate-400">Related User:</div>
                                    <select name="related_user_id[]" id="new_transaction-related_user_id-{{ $i }}" class="text-xs py-1 w-full">
                                        <option value="">related user</option>
                                        @foreach ($related_users as $related_user)
                                        <option value="{{ $related_user->id }}">{{ $related_user->username }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="text-slate-400">Related Desc:</div>
                                    <input type="text" name="related_desc[]" id="new_transaction-related_desc-{{ $i }}" class="border p-1 text-xs w-60" placeholder="keterangan u. related user">
                                </td>
                                <td>
                                    <div class="text-slate-400">Pelanggan Terkait:</div>
                                    <input type="text" name="pelanggan_nama[]" id="new_transaction-pelanggan_nama-{{ $i }}" class="border p-1 text-xs w-60" placeholder="nama pelanggan terkait">
                                    <input type="hidden" name="pelanggan_id[]" id="new_transaction-pelanggan_id-{{ $i }}">
                                </td>
                                <td>
                                    <div class="text-slate-400">(related user)Instance Type:</div>
                                    <input type="text" name="related_user_instance_type[]" id="new_transaction-related_user_instance_type-{{ $i }}" class="border p-1 text-xs" placeholder="tipe instansi ...">
                                    <input type="hidden" name="related_user_instance_id[]" id="new_transaction-related_user_instance_id-{{ $i }}">
                                </td>
                                <td>
                                    <div class="text-slate-400">(related user)Instance Name:</div>
                                    <input type="text" name="related_user_instance_name[]" id="new_transaction-related_user_instance_name-{{ $i }}" class="border p-1 text-xs" placeholder="nama instansi ...">
                                </td>
                                <td>
                                    <div class="text-slate-400">(related user)Instance Branch:</div>
                                    <input type="text" name="related_user_instance_branch[]" id="new_transaction-related_user_instance_branch-{{ $i }}" class="border p-1 text-xs" placeholder="cabang ...">
                                </td>
                            </tr> --}}
                            @endfor
                            <tr id="tr_add_transaction">
                                <td>
                                    <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="add_transaction('tr_add_transaction','table_add_transactions')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                    {{-- <input type="hidden" name="user_instance_id" value="{{ $user_instance->id }}"> --}}
                                </td>
                            </tr>
                        </table>
                        {{-- <div class="mt-3 text-xs border rounded p-1 inline-block border-yellow-500">
                            <p>*) Keterangan Tambahan akan tertulis dalam tanda kurung pada ringkasan/laporan.</p>
                        </div> --}}
                        <div class="mt-3 text-center text-xs">
                            <input id="loading_to_disable" type="submit" class="border-2 font-semibold rounded text-emerald-500 border-emerald-300 bg-emerald-200 px-2 hover:cursor-pointer" value="confirm" />
                        </div>
                    </form>
                </div>
            </div>
            {{-- NOTIFIKASI --}}
            {{-- <x-notification :user-instance=$user_instance :notifications=$notifications /> --}}
            {{-- END - NOTIFIKASI --}}
        </div>
        @endif
        {{-- END - STORE NEW TRANSACTIONS --}}
    </div>
</main>

<style>
    .table-border td {
        border-bottom: 1px solid lightgrey;
        border-collapse: collapse;
        padding: 5px;
    }

</style>

<script>
    const related_users = {!! json_encode($related_users, JSON_HEX_TAG) !!};
    const label_deskripsi = {!! json_encode($label_deskripsi, JSON_HEX_TAG) !!};
    const user_instance = {!! json_encode($user_instance, JSON_HEX_TAG) !!};
    const count_accountings = {!! json_encode(count($accountings), JSON_HEX_TAG) !!};

    // console.log(count_accountings);

    $(`#filter-desc`).autocomplete({
        source: label_deskripsi,
        select: function (event, ui) {
            document.getElementById(`filter-desc`).value = ui.item.value;
        }
    });

    let html_option_related_users = '<option value="">-</option>';
    related_users.forEach(related_user => {
        html_option_related_users += `<option value="${related_user.id}">${related_user.username}</option>`;
    });

    // console.log(html_option_related_users);

    let transaction_index = 7;
    function add_transaction(tr_id, parent_id) {
        document.getElementById(tr_id).remove();
        let parent = document.getElementById(parent_id);
        parent.insertAdjacentHTML('beforeend',
        `
        <tr>
            <td>
                <div class="flex items-center">
                    <input type="text" name="day[]" id="day-${transaction_index}" class="border p-1 text-xs w-8" placeholder="dd" value="{{ old('day.' . $i) ? old('day.' . $i) : date('d') }}">
                    <span>-</span>
                    <input type="text" name="month[]" id="month-${transaction_index}" class="border p-1 text-xs w-8" placeholder="mm" value="{{ old('month.' . $i) ? old('month.' . $i) : date('m') }}">
                    <span>-</span>
                    <input type="text" name="year[]" id="year-${transaction_index}" class="border p-1 text-xs w-10" placeholder="yyyy" value="{{ old('year.' . $i) ? old('year.' . $i) : date('Y') }}">
                </div>
            </td>
            <td><input type="text" name="kode[]" id="kode-${transaction_index}" class="border p-1 text-xs w-20" value="${user_instance.kode}"></td>
            <td><input type="text" name="transaction_desc[]" id="transaction_desc-${transaction_index}" class="border p-1 text-xs w-60"></td>
            <td><input type="text" name="keterangan[]" id="keterangan-${transaction_index}" class="border p-1 text-xs w-full"></td>
            <td>
                <input type="text" id="keluar-${transaction_index}" class="border p-1 text-xs w-36" onchange="formatNumber(this, 'keluar-${transaction_index}-unformatted')">
                <input type="hidden" name="keluar[]" id="keluar-${transaction_index}-unformatted">
            </td>
            <td>
                <input type="text" id="masuk-${transaction_index}" class="border p-1 text-xs w-36" onchange="formatNumber(this, 'masuk-${transaction_index}-unformatted')">
                <input type="hidden" name="masuk[]" id="masuk-${transaction_index}-unformatted">
                <input type="hidden" name="transaction_id[]" id="transaction_id-${transaction_index}">
            </td>
        </tr>
        <tr id="tr_add_transaction">
            <td>
                <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="add_transaction('tr_add_transaction','table_add_transactions')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </td>
        </tr>
        `);
        setTimeout(() => {
            // setAutocompleteSPKItem(`produk_nama-${transaction_index}`, `produk_nama-${transaction_index}`, `produk_id-${transaction_index}`);
            autocomplete_deskripsi(transaction_index);
            transaction_index++;
        }, 100);
    }

    for (let i = 0; i < transaction_index; i++) {
        autocomplete_deskripsi(i);
    }

    function autocomplete_deskripsi(index) {
        $(`#transaction_desc-${index}`).autocomplete({
            source: label_deskripsi,
            select: function (event, ui) {
                // console.log(ui.item);
                // document.getElementById(`transaction_desc-${index}`).value = ui.item.id;
                document.getElementById(`transaction_desc-${index}`).value = ui.item.value;
                document.getElementById(`transaction_id-${index}`).value = ui.item.id;
                // autofill_transaction(index, ui.item.value);
            }
        });
    }

    setTimeout(() => {
        for (let j = 0; j < count_accountings; j++) {
            autocomplete_edit_deskripsi(j);
        }
    }, 1000);

    function autocomplete_edit_deskripsi(index) {
        // console.log(index);
        $(`#edit-${index}-transaction_desc`).autocomplete({
            source: label_deskripsi,
            select: function (event, ui) {
                // console.log(ui.item);
                // document.getElementById(`edit-${index}-transaction_desc`).value = ui.item.id;
                document.getElementById(`edit-${index}-transaction_desc`).value = ui.item.value;
                // document.getElementById(`edit-${index}-transaction_id`).value = ui.item.id;
                // autofill_transaction(index, ui.item.value);
            }
        });
    }

    // const transaction_names = {-!! json_encode($transaction_names, JSON_HEX_TAG) !!};

    // function autofill_transaction(index, desc) {
    //     // console.log(index, desc);
    //     let res = transaction_names.find(o => o.desc === desc);
    //     console.log(res);
    //     let related_user_instance_id = document.getElementById(`new_transaction-related_user_instance_id-${index}`);
    //     document.getElementById(`new_transaction-kategori_type-${index}`).value = res.kategori_type;
    //     document.getElementById(`new_transaction-kategori_level_one-${index}`).value = res.kategori_level_one;
    //     document.getElementById(`new_transaction-kategori_level_two-${index}`).value = res.kategori_level_two;
    //     document.getElementById(`new_transaction-related_desc-${index}`).value = res.related_desc;
    //     document.getElementById(`new_transaction-related_user_id-${index}`).value = res.related_user_id;
    //     document.getElementById(`new_transaction-pelanggan_id-${index}`).value = res.pelanggan_id;
    //     document.getElementById(`new_transaction-pelanggan_nama-${index}`).value = res.pelanggan_nama;
    //     related_user_instance_id.value = res.related_user_instance_id;
    //     document.getElementById(`new_transaction-related_user_instance_type-${index}`).value = res.related_user_instance_type;
    //     document.getElementById(`new_transaction-related_user_instance_name-${index}`).value = res.related_user_instance_name;
    //     document.getElementById(`new_transaction-related_user_instance_branch-${index}`).value = res.related_user_instance_branch;
    //     // console.log(related_user_instance_id)
    //     // console.log(related_user_instance_id.value)
    // }

    function table_to_excel(table_id) {
        $(`#${table_id}`).table2excel({
            filename:`${table_id}.xls`
        });
    }

    setTimeout(() => {
        get_scroll();
        $spinner = $('#spinner');
        $spinner.hide();
    }, 1000);

    const loading_animation = () => {
        $loading_to_disable = $('#loading_to_disable')
        $loading_to_disable.prop('disabled', true);
        $loading_to_hide = $('#loading_to_hide');
        $loading_to_hide.hide()
        // console.log('loading_animation');
        $spinner = $('#spinner');
        $spinner.show();
    }

    const get_scroll = () => {
        const scrollValue = localStorage.getItem("scrollValue")
        if (scrollValue !== null) {
            window.scrollTo(0, scrollValue ? scrollValue : 0)
        }
        // console.log(scrollValue);
    }

    const set_scroll_here = () => {
        // console.log('test scroll');
        loading_animation();
        localStorage.setItem('scrollValue', window.scrollY)
        return true;
    }

</script>

@endsection
