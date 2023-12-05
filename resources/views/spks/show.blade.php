@extends('layouts.main')
@section('content')
  <main>
      <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <x-errors-any></x-errors-any>
        <x-validation-feedback></x-validation-feedback>
        <div class="grid grid-cols-3 gap-1">
            <div class="text-center bg-violet-200 rounded-t font-bold text-slate-700 py-1 text-xl">Surat Perintah Kerja</div>
            <div class="text-center bg-emerald-200 rounded-t font-bold text-slate-700 py-1 text-xl">Nota</div>
            <div class="text-center bg-orange-200 rounded-t font-bold text-slate-700 py-1 text-xl">Surat Jalan</div>
            {{-- SPK --}}
            <div>
                <table class="w-full">
                    <tr><td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $spk->no_spk }}</div></td></tr>
                    <tr>
                        <td>Tgl.</td><td>:</td>
                        <td>
                            <div class="w-fit">
                                <div class="flex items-center" onclick="toggle_element('form_edit_tanggal')">
                                    <div class="flex">
                                        @if ($spk->finished_at === null)
                                        <div>
                                            <div class="rounded p-1 bg-red-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($spk->created_at)) }}</div>
                                                <div>{{ date('m-y',strtotime($spk->created_at)) }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <div>
                                            <div class="rounded p-1 bg-yellow-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($spk->created_at)) }}</div>
                                                <div>{{ date('m-y',strtotime($spk->created_at)) }}</div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex ml-1">
                                        @if ($spk->finished_at !== null)
                                        <div>
                                            <div class="rounded p-1 bg-emerald-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($spk->finished_at)) }}</div>
                                                <div>{{ date('m-y',strtotime($spk->finished_at)) }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <span class="font-bold">---</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="hidden" id="form_edit_tanggal">
                        <td></td><td></td>
                        <td>
                            <form action="{{ route('spks.edit_tanggal', $spk->id) }}" method="POST" class="w-fit">
                                @csrf
                                <div>tgl. pembuatan:</div>
                                <div class="flex items-center">
                                    <div class="flex">
                                        <select name="created_day" id="created_day" class="rounded text-xs pl-0 pr-7">
                                            <option value="{{ date('d',strtotime($spk->created_at)) }}">{{ date('d',strtotime($spk->created_at)) }}</option>
                                            <option value="">-</option>
                                            @for ($i = 1; $i < 32; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select name="created_month" id="created_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                            <option value="{{ date('m',strtotime($spk->created_at)) }}">{{ date('m',strtotime($spk->created_at)) }}</option>
                                            <option value="">-</option>
                                            @for ($i = 1; $i < 13; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select name="created_year" id="created_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                            <option value="{{ date('Y',strtotime($spk->created_at)) }}">{{ date('Y',strtotime($spk->created_at)) }}</option>
                                            <option value="">-</option>
                                            @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-1">tgl. selesai:</div>
                                @if ($spk->finished_at !== null)
                                <div class="flex">
                                    <select name="finished_day" id="finished_day" class="rounded text-xs pl-0 pr-7">
                                        <option value="{{ date('d',strtotime($spk->finished_at)) }}">{{ date('d',strtotime($spk->finished_at)) }}</option>
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 32; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="finished_month" id="finished_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                        <option value="{{ date('m',strtotime($spk->finished_at)) }}">{{ date('m',strtotime($spk->finished_at)) }}</option>
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 13; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="finished_year" id="finished_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                        <option value="{{ date('Y',strtotime($spk->finished_at)) }}">{{ date('Y',strtotime($spk->finished_at)) }}</option>
                                        <option value="">-</option>
                                        @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @else
                                <div class="flex">
                                    <select name="finished_day" id="finished_day" class="rounded text-xs pl-0 pr-7">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 32; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="finished_month" id="finished_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                        <option value="">-</option>
                                        @for ($i = 1; $i < 13; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="finished_year" id="finished_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                        <option value="">-</option>
                                        @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @endif
                                <div class="text-end mt-1">
                                    <button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>Untuk</td><td>:</td>
                        <td>
                            <div class="flex items-center">
                                <a href="" class="text-indigo-500 font-semibold text-lg">{{ $nama_pelanggan }}</a>
                                <button type="button" class="border border-slate-300 text-slate-400 rounded ml-1" id="btn_edit_pelanggan" onclick="toggle_light(this.id, 'form_edit_pelanggan', ['border', 'border-slate-300'], ['bg-slate-200'], 'table-row')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr id="form_edit_pelanggan" class="hidden">
                        <td></td><td></td>
                        <td>
                            <form action="{{ route('spks.edit_pelanggan', $spk->id) }}" method="POST" onsubmit="return confirm('Data pelanggan pada Nota dan Srjalan(apabila sudah dibuat) akan berubah!')">
                                @csrf
                                @if ($spk->reseller_id !== null)
                                <input type="text" name="pelanggan_nama" id="pelanggan_nama" placeholder="nama pelanggan..." value="{{ $spk->reseller_nama }} - {{ $spk->pelanggan_nama }}" class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                @else
                                <input type="text" name="pelanggan_nama" id="pelanggan_nama" placeholder="nama pelanggan..." value="{{ $spk->pelanggan_nama }}" class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                @endif
                                <input type="hidden" name="pelanggan_id" id="pelanggan_id" value="{{ $spk->pelanggan_id }}">
                                <input type="hidden" name="reseller_id" id="reseller_id" value="{{ $spk->reseller_id }}">
                                <button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>Ket.</td><td>:</td>
                        <td>
                            <div class="inline-block" onclick="toggle_element('form_edit_keterangan')">
                                @if ($spk->keterangan === null)
                                -
                                @else
                                {{ $spk->keterangan }}
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr id="form_edit_keterangan" class="hidden">
                        <td></td><td></td>
                        <td>
                            <form action="{{ route('spks.edit_keterangan', $spk->id) }}" method="POST">
                                @csrf
                                <div class="flex items-center">
                                    <input type="text" name="keterangan" value="{{ $spk->keterangan }}" placeholder="keterangan..." class="text-xs rounded px-1 py-0">
                                    <button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1 ml-1">confirm</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                </table>

                {{-- SPK Items --}}
                <div class="border rounded px-1 mt-2 py-2" id="spk-items">
                    <table class="w-full text-xs">
                        <tr>
                            <th>
                                <div class="flex items-center justify-center">
                                    <span>Item Produksi</span>
                                    <button type="button" id="spk_produk_detail_button" class="ml-1 border rounded border-yellow-500 text-yellow-500 p-1" onclick="toggle_detail_classes(this.id,'spk_produk_detail')">D</button>

                                </div>
                            </th>
                            <th>Jumlah</th>
                        </tr>
                        <tr><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                        @foreach ($spk_produks as $key_spk_produk => $spk_produk)
                        <tr>
                            <td onclick="toggle_element('spk_produk_detail-{{ $key_spk_produk }}')">
                                <div>
                                    {{ $spk_produk->nama_produk }}
                                </div>
                                @if ($spk_produk->keterangan)
                                    <div class="border rounded text-slate-400 ml-1 italic">{{ $spk_produk->keterangan }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-center" onclick="toggle_element('jumlah_deviasi-{{ $key_spk_produk }}')">
                                    <span>{{ $spk_produk->jumlah }}</span>
                                    @if ($spk_produk->deviasi_jumlah > 0)
                                    <span class="text-emerald-500"> +{{ $spk_produk->deviasi_jumlah }}</span>
                                    @elseif ($spk_produk->deviasi_jumlah < 0)
                                    <span class="text-pink-500"> {{ $spk_produk->deviasi_jumlah }}</span>
                                    @endif
                                </div>
                                <div class="hidden" id="jumlah_deviasi-{{ $key_spk_produk }}">
                                    <form action="{{ route('spks.edit_jumlah_deviasi',[$spk->id, $spk_produk->id]) }}" method="POST" class="border rounded">
                                        @csrf
                                        <table>
                                            <tr><td>Jml.</td><td>:</td><td><input type="number" class="text-xs p-0 rounded w-12" name="jumlah" value="{{ $spk_produk->jumlah }}"></td></tr>
                                            <tr><td>+/-</td><td>:</td><td><input type="number" class="text-xs p-0 rounded w-12" name="deviasi" value="{{ $spk_produk->deviasi_jumlah }}"></td></tr>
                                        </table>
                                        <div class="text-center my-1">
                                            <button type="submit" class="px-1 bg-emerald-200 text-emerald-500 rounded">confirm</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="spk_produk_detail hidden" id="spk_produk_detail-{{ $key_spk_produk }}">
                            <td>
                                <div class="flex">
                                    <div>
                                        <div class="border rounded border-violet-500 p-1 text-violet-500" onclick="toggle_element('spk_produk_selesai-{{ $key_spk_produk }}')">S: {{ $spk_produk->jumlah_selesai }}</div>
                                        {{-- FORM TETAPKAN SPK ITEM SELESAI --}}
                                        <div class="mt-1 hidden" id="spk_produk_selesai-{{ $key_spk_produk }}">
                                            <form action="{{ route('spks.spk_item_tetapkan_selesai', $spk_produk->id) }}" method="POST" class="border rounded p-1">
                                                @csrf
                                                <table class="text-xs">
                                                    <tr>
                                                        <td>S</td><td>:</td>
                                                        <td>
                                                            <input type="hidden" name="spk_produk_id" value="{{ $spk_produk->id }}">
                                                            <input type="number" name="jumlah" id="" class="rounded text-xs p-1 w-14" value="{{ $spk_produk->jumlah_selesai }}">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div class="text-center mt-1">
                                                    <button type="submit" class="bg-violet-300 text-violet-700 rounded p-1">confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                        {{-- END - FORM TETAPKAN SPK ITEM SELESAI --}}
                                    </div>
                                    <div class="ml-1">
                                        <div class="border rounded border-emerald-500 p-1 text-emerald-500" onclick="toggle_element('spk_produk_nota-{{ $key_spk_produk }}')">
                                            @foreach ($data_spk_produks[$key_spk_produk]['data_nota'] as $data_nota)
                                            @if ($data_nota['jumlah'] !== 0)
                                            <div>N-{{ $data_nota['nota_id'] }}:{{ $data_nota['jumlah'] }}</div>
                                            @endif
                                            @endforeach
                                        </div>
                                        {{-- FORM INPUT SPK ITEM KE NOTA --}}
                                        <div class="mt-1 hidden" id="spk_produk_nota-{{ $key_spk_produk }}">
                                            <form action="{{ route('notas.create_or_edit_jumlah_spk_produk_nota', [$spk->id, $spk_produk->id]) }}" method="POST" class="border rounded p-1">
                                                @csrf
                                                <table>
                                                    @foreach ($data_spk_produks[$key_spk_produk]['data_nota'] as $data_nota)
                                                    <tr>
                                                        <td>N-{{ $data_nota['nota_id'] }}</td><td>:</td>
                                                        <td>
                                                            <input type="hidden" name="nota_id[]" value="{{ $data_nota['nota_id'] }}">
                                                            <input type="number" name="jumlah[]" class="rounded text-xs p-1 w-14" value="{{ $data_nota['jumlah'] }}">
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td>new</td><td>:</td>
                                                        <td>
                                                            <input type="hidden" name="nota_id[]" value="new">
                                                            <input type="number" name="jumlah[]" class="rounded text-xs p-1 w-14" step="1" min="0" value="0">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div class="text-center mt-1">
                                                    <button type="submit" class="bg-emerald-300 text-emerald-700 rounded p-1">confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                        {{-- END - FORM INPUT SPK ITEM KE NOTA --}}
                                    </div>
                                    <div class="ml-1">
                                        <div class="border rounded border-orange-400 p-1 text-orange-400">
                                            @foreach ($data_spk_produks[$key_spk_produk]['data_srjalan'] as $data_srjalan)
                                            <div>SJ-{{ $data_srjalan['srjalan_id'] }}:{{ $data_srjalan['jumlah'] }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <form action="{{ route('spks.delete_item', [$spk->id, $spk_produk->id]) }}" class="ml-1 flex" method="POST" onsubmit="return confirm('Menghapus spk_item akan merubah data Nota dan Srjalan!')">
                                        @csrf
                                        <button class="text-red-400" type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <form action="{{ route('spks.spk_produk_edit_keterangan', [$spk->id, $spk_produk->id]) }}" method="POST" class="mt-2">
                                    @csrf
                                    <h5>Edit Keterangan:</h5>
                                    <div class="inline-block">
                                        <textarea name="keterangan" cols="30" rows="3" class="border-slate-300 rounded-lg text-xs p-0 placeholder:text-slate-400" placeholder="keterangan item...">{{ $spk_produk->keterangan }}</textarea>
                                        <div class="text-end my-1">
                                            <button type="submit" class="px-1 bg-emerald-200 text-emerald-500 rounded">confirm</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        <tr><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                        <tr><th>Total</th><th>{{ $spk->jumlah_total }}</th></tr>
                        {{-- TR ADD_SPK_ITEMS --}}
                        <tr>
                            <td colspan="2">
                                <form action="{{ route('spks.add_item', $spk->id) }}" method="POST">
                                    @csrf
                                    <table class="w-full" id="table_new_spk_items">
                                        <tr id="tr_add_item">
                                            <td>
                                                <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="addSPKItem('tr_add_item','table_new_spk_items')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>
                                                </button>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                    <div class="text-center hidden" id="btn_confirm_add_spk_items">
                                        <button class="bg-emerald-200 text-emerald-500 rounded px-1" type="submit">confirm</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        {{-- END - TR ADD_SPK_ITEMS --}}
                    </table>
                </div>
                {{-- END - SPK Items --}}
                {{-- OPSI SPK --}}
                <div class="flex justify-end mt-1 items-center">
                    {{-- <form action="{{ route('spks.edit', $spk->id) }}" method="GET" class="ml-1 flex">
                        <button class="border border-slate-300 text-slate-400 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    </form> --}}
                    <a href="{{ route('spks.print_out', $spk->id) }}" class="rounded text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                        </svg>
                    </a>
                    <form action="{{ route('spks.selesai_all',$spk->id) }}" method="POST" class="ml-1 flex" onsubmit="return confirm('Yakin menetapkan semua item SPK menjadi SELESAI?')">
                        @csrf
                        <button type="submit" class="bg-violet-200 text-violet-500 rounded font-bold text-md px-1" name="spk_id" value="{{ $spk->id }}">S</button>
                    </form>
                    <button type="button" class="ml-1 border border-emerald-200 text-emerald-500 rounded font-bold text-md px-1" id="btn_pilihan_nota" value="{{ $spk->id }}" onclick="toggle_light(this.id,'pilihan_nota',[],['bg-emerald-200'], 'block')">N</button>
                    <form action="{{ route('spks.delete',$spk->id) }}" method="POST" class="ml-1 flex" onsubmit="return confirm('Warning: Hapus SPK akan menghapus Nota dan Surat Jalan terkait!')">
                        @csrf
                        <button type="submit" class="bg-red-200 text-red-500 rounded" name="spk_id" value="{{ $spk->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </form>
                </div>
                {{-- NOTA ALL -> PILIHAN NOTA --}}
                <div class="hidden" id="pilihan_nota">
                    <div class="mt-1 flex justify-end">
                        <form action="{{ route('notas.nota_all', $spk->id) }}" method="POST" class="border border-emerald-300 rounded p-1 text-emerald-500" onsubmit="return confirm('Yakin input semua item SPK ke Nota terpilih?')">
                            @csrf
                            @foreach ($notas as $key_pilih_nota => $nota)
                            <div class="flex items-center mt-1">
                                <input type="radio" name="nota_id" id="pilih_nota_id-{{ $key_pilih_nota }}" value="{{ $nota->id }}">
                                <label for="pilih_nota_id-{{ $key_pilih_nota }}" class="ml-1">{{ $nota->no_nota }}</label>
                            </div>
                            @endforeach
                            <div class="flex items-center mt-1">
                                <input type="radio" name="nota_id" id="pilih_nota_id-new" value="new">
                                <label for="pilih_nota_id-new" class="ml-1">new</label>
                            </div>
                            <div class="text-center mt-1">
                                <button type="submit" class="bg-emerald-300 text-emerald-700 rounded p-1">confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- END - NOTA ALL -> PILIHAN NOTA --}}
                {{-- END - OPSI SPK --}}
            </div>
            {{-- END - SPK --}}
            <div>
                @if (count($notas) === 0)
                {{-- <div class="flex border-t pt-1 justify-center">
                    <div>none</div>
                </div> --}}
                @else
                {{-- PILIHAN ALAMAT/KONTAK --}}
                <div class="text-right"><button id="btn_opsi_alamat_kontak" class="border rounded border-emerald-300 text-emerald-500" onclick="toggle_light(this.id, 'pilihan_alamat_kontak', [], ['bg-emerald-200'], 'block')">Opsi Alamat/Kontak</button></div>
                <div id="pilihan_alamat_kontak" class="hidden">
                    <div class="border rounded p-1 grid grid-cols-2 mt-1">
                        <form method="POST" action="{{ route('notas.edit_alamat', $spk->id) }}" onsubmit="return confirm('Akan mengubah data alamat Nota dan Srjalan terkait!')">
                            @csrf
                            <div class="font-bold">Pilihan Alamat:</div>
                            <div class="flex">
                                @foreach ($pilihan_alamat as $key_alamat => $alamat)
                                @if ($alamat->id === $alamat_id_terpilih)
                                <input type="radio" name="alamat_id" id="pilihan_alamat-{{ $key_alamat }}" value="{{ $alamat->id }}" checked>
                                @else
                                <input type="radio" name="alamat_id" id="pilihan_alamat-{{ $key_alamat }}" value="{{ $alamat->id }}">
                                @endif
                                <label for="pilihan_alamat-{{ $key_alamat }}" class="ml-1">
                                    @if ($alamat->long !== null)
                                    @foreach (json_decode($alamat->long, true) as $long)
                                    <div>{{ $long }}</div>
                                    @endforeach
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            <div class="font-semibold">Untuk:</div>
                            <div class="flex">
                                <div class="flex item-center">
                                    <input type="radio" name="nota_id" id="nota_id-semua" value="semua" checked>
                                    <label for="nota_id-semua" class="ml-1">semua</label>
                                </div>
                                @foreach ($notas as $key_nota => $nota)
                                <div class="flex item-center ml-2">
                                    <input type="radio" name="nota_id" id="nota_id-{{ $key_nota }}" value="{{ $nota->id }}">
                                    <label for="nota_id-{{ $key_nota }}" class="ml-1">{{ $nota->no_nota }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3"><button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button></div>
                        </form>
                        <form action="{{ route('notas.edit_kontak', $spk->id) }}" method="POST" onsubmit="return confirm('Akan mengubah data kontak Nota dan Srjalan terkait!')">
                            @csrf
                            <div class="font-bold">Pilihan Kontak:</div>
                            <div class="flex">
                                @foreach ($pilihan_kontak as $key_kontak => $kontak)
                                @if ($kontak->id === $kontak_id_terpilih)
                                <input type="radio" name="kontak_id" id="pilihan_kontak-{{ $key_kontak }}" value="{{ $kontak->id }}" checked>
                                @else
                                <input type="radio" name="kontak_id" id="pilihan_kontak-{{ $key_kontak }}" value="{{ $kontak->id }}">
                                @endif
                                <label for="pilihan_kontak-{{ $key_kontak }}" class="ml-1">
                                    @if ($kontak->tipe === 'seluler')
                                    {{ $kontak->nomor }}
                                    @elseif ($kontak->kodearea !== null)
                                    ({{ $kontak->kodearea }}) {{ $kontak->nomor }}
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            <div class="font-semibold">Untuk:</div>
                            <div class="flex">
                                <div class="flex item-center">
                                    <input type="radio" name="nota_id" id="nota_id_kontak-semua" value="semua" checked>
                                    <label for="nota_id_kontak-semua" class="ml-1">semua</label>
                                </div>
                                @foreach ($notas as $key_nota => $nota)
                                <div class="flex item-center ml-2">
                                    <input type="radio" name="nota_id" id="nota_id_kontak-{{ $key_nota }}" value="{{ $nota->id }}">
                                    <label for="nota_id_kontak-{{ $key_nota }}" class="ml-1">{{ $nota->no_nota }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3"><button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button></div>
                        </form>
                    </div>
                </div>
                {{-- END - PILIHAN ALAMAT/KONTAK --}}
                @foreach ($notas as $key_nota => $nota)
                <div class="border-t-4 pt-2">
                    <div class="grid grid-cols-2">
                        <table class="w-full">
                            <tr>
                                <td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $nota->no_nota }}</div></td>
                            </tr>
                            <tr>
                                <td>Tgl.</td><td>:</td>
                                <td>
                                    <div class="w-fit">
                                        <div class="flex" onclick="toggle_element('form_edit_tanggal_nota')">
                                            <div class="flex">
                                                @if ($nota->finished_at === null)
                                                <div>
                                                    <div class="rounded p-1 bg-red-500 text-white font-bold text-center">
                                                        <div>{{ date('d',strtotime($nota->created_at)) }}</div>
                                                        <div>{{ date('m-y',strtotime($nota->created_at)) }}</div>
                                                    </div>
                                                </div>
                                                @else
                                                <div>
                                                    <div class="rounded p-1 bg-yellow-500 text-white font-bold text-center">
                                                        <div>{{ date('d',strtotime($nota->created_at)) }}</div>
                                                        <div>{{ date('m-y',strtotime($nota->created_at)) }}</div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="flex ml-1 items-center">
                                                @if ($nota->finished_at !== null)
                                                <div>
                                                    <div class="rounded p-1 bg-emerald-500 text-white font-bold text-center">
                                                        <div>{{ date('d',strtotime($nota->finished_at)) }}</div>
                                                        <div>{{ date('m-y',strtotime($nota->finished_at)) }}</div>
                                                    </div>
                                                </div>
                                                @else
                                                <span class="font-bold">---</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hidden" id="form_edit_tanggal_nota">
                                <td colspan="3">
                                    <form action="{{ route('notas.edit_tanggal', $nota->id) }}" method="POST" class="w-fit">
                                        @csrf
                                        <div>tgl. pembuatan:</div>
                                        <div class="flex items-center">
                                            <div class="flex">
                                                <select name="created_day" id="created_day" class="rounded text-xs pl-0 pr-7">
                                                    <option value="{{ date('d',strtotime($nota->created_at)) }}">{{ date('d',strtotime($nota->created_at)) }}</option>
                                                    <option value="">-</option>
                                                    @for ($i = 1; $i < 32; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <select name="created_month" id="created_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                                    <option value="{{ date('m',strtotime($nota->created_at)) }}">{{ date('m',strtotime($nota->created_at)) }}</option>
                                                    <option value="">-</option>
                                                    @for ($i = 1; $i < 13; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <select name="created_year" id="created_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                                    <option value="{{ date('Y',strtotime($nota->created_at)) }}">{{ date('Y',strtotime($nota->created_at)) }}</option>
                                                    <option value="">-</option>
                                                    @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-1">tgl. selesai:</div>
                                        @if ($nota->finished_at !== null)
                                        <div class="flex">
                                            <select name="finished_day" id="finished_day" class="rounded text-xs pl-0 pr-7">
                                                <option value="{{ date('d',strtotime($nota->finished_at)) }}">{{ date('d',strtotime($nota->finished_at)) }}</option>
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 32; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_month" id="finished_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="{{ date('m',strtotime($nota->finished_at)) }}">{{ date('m',strtotime($nota->finished_at)) }}</option>
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 13; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_year" id="finished_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="{{ date('Y',strtotime($nota->finished_at)) }}">{{ date('Y',strtotime($nota->finished_at)) }}</option>
                                                <option value="">-</option>
                                                @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        @else
                                        <div class="flex">
                                            <select name="finished_day" id="finished_day" class="rounded text-xs pl-0 pr-7">
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 32; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_month" id="finished_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 13; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_year" id="finished_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="">-</option>
                                                @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        @endif
                                        <div class="text-end mt-1">
                                            <button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td class="align-top">Alamat</td><td class="align-top">:</td>
                                <td class="align-top">
                                    @if ($nota->cust_long!==null)
                                    @foreach (json_decode($nota->cust_long,true) as $long)
                                    <div>{{ $long }}</div>
                                    @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Kontak</td><td>:</td>
                                <td>
                                    @if ($cust_kontaks[$key_nota]!==null)
                                    {{ $cust_kontaks[$key_nota] }}
                                    @else-@endif
                                </td>
                            </tr>
                        </table>
                    </div>


                    {{-- Nota Items --}}
                    <div class="border rounded px-1 py-2" id="nota-items-{{ $key_nota }}">
                        <table class="w-full text-xs">
                            <tr>
                                <th>Jml.</th>
                                <th>
                                    <div class="flex items-center justify-center">
                                        <span>Nama Barang</span>
                                        <button type="button" id="spk_produk_nota_detail_button-{{ $key_nota }}" class="ml-1 border rounded border-yellow-500 text-yellow-500 p-1" onclick="toggle_detail_classes(this.id,'spk_produk_nota_detail-{{ $key_nota }}')">D</button>
                                    </div>
                                </th>
                                <th>Hrg.</th><th>Hrg. t</th>
                            </tr>
                            <tr><td><div class="text-center">---</div></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td><td><div class="text-center">---</div></td></tr>
                            @foreach ($col_spk_produk_notas[$key_nota] as $key_spk_produk_nota => $spk_produk_nota)
                            <tr>
                                <td><div class="text-center">{{ $spk_produk_nota->jumlah }}</div></td>
                                <td onclick="toggle_element('spk_produk_nota_detail-{{ $key_nota }}-{{ $key_spk_produk_nota }}')">
                                    <div>
                                        {{ $spk_produk_nota->nama_nota }}
                                    </div>
                                    @if ($spk_produk_nota->keterangan)
                                    <div class="border text-slate-400 italic rounded">
                                        {{ $spk_produk_nota->keterangan }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-center" onclick="toggle_element('edit_harga_item-{{ $key_nota }}-{{ $key_spk_produk_nota }}')">{{ number_format($spk_produk_nota->harga,0,',','.') }}</div>
                                    <div class="hidden text-center" id="edit_harga_item-{{ $key_nota }}-{{ $key_spk_produk_nota }}">
                                        <form action="{{ route('notas.edit_harga_item', [$spk->id, $nota->id, $spk_produk_nota->id]) }}" method="POST">
                                            @csrf
                                            <input type="text" class="rounded p-0 text-xs w-16" value="{{ number_format($spk_produk_nota->harga,0,',','.') }}" onchange="formatNumber(this, 'harga_nota_item-{{ $key_nota }}-{{ $key_spk_produk_nota }}')">
                                            <input type="hidden" id="harga_nota_item-{{ $key_nota }}-{{ $key_spk_produk_nota }}" name="harga" value="{{ $spk_produk_nota->harga }}">
                                            <div>harga khusus pelanggan ini?</div>
                                            <div class="flex items-center justify-center">
                                                <input type="checkbox" name="harga_khusus_pelanggan" value="yes" id="harga_khusus_pelanggan-{{ $key_nota }}-{{ $key_spk_produk_nota }}" class="rounded">
                                                <label for="harga_khusus_pelanggan-{{ $key_nota }}-{{ $key_spk_produk_nota }}" class="ml-1">yes</label>
                                            </div>
                                            <div class="mt-1">
                                                <button class="rounded px-1 bg-emerald-300 text-emerald-500 font-semibold">confirm</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td><div class="text-center">{{ number_format($spk_produk_nota->harga_t,0,',','.') }}</div></td>
                            </tr>
                            {{-- SPK_PRODUK_NOTA_DETAIL --}}
                            <tr class="spk_produk_nota_detail-{{ $key_nota }} hidden" id="spk_produk_nota_detail-{{ $key_nota }}-{{ $key_spk_produk_nota }}">
                                <td colspan="4">
                                    <div class="flex">
                                        <div>
                                            <div class="border rounded border-orange-400 p-1 text-orange-400" onclick="toggle_element('spk_produk_nota-{{ $key_nota }}-{{ $key_spk_produk_nota }}')">
                                                {{-- {{ dump($data_spk_produk_notas) }} --}}
                                                {{-- {{ dd($data_spk_produk_notas[$key_nota]) }} --}}
                                                @foreach ($data_spk_produk_notas[$key_nota][$key_spk_produk_nota] as $data_srjalan)
                                                {{-- {{ dd($data_srjalan) }} --}}
                                                @if ($data_srjalan['jumlah'] !== 0)
                                                <div>SJ-{{ $data_srjalan['srjalan_id'] }}:{{ $data_srjalan['jumlah'] }}</div>
                                                @endif
                                                @endforeach
                                            </div>
                                            {{-- FORM INPUT NOTA ITEM KE SJ --}}
                                            <div class="mt-1 hidden" id="spk_produk_nota-{{ $key_nota }}-{{ $key_spk_produk_nota }}">
                                                <form action="{{ route('sjs.create_or_edit_jumlah_spk_produk_nota_srjalan', [$spk->id, $nota->id, $spk_produk_nota->spk_produk_id, $spk_produk_nota->id]) }}" method="POST" class="border rounded p-1">
                                                    @csrf
                                                    <table class="text-xs">
                                                        @foreach ($data_spk_produk_notas[$key_nota][$key_spk_produk_nota] as $data_srjalan)
                                                        <tr>
                                                            <td>SJ-{{ $data_srjalan['srjalan_id'] }}</td><td>:</td>
                                                            <td>
                                                                <input type="hidden" name="srjalan_id[]" value="{{ $data_srjalan['srjalan_id'] }}">
                                                                <input type="number" name="jumlah[]" class="rounded text-xs p-1 w-14" value="{{ $data_srjalan['jumlah'] }}">
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td>new</td><td>:</td>
                                                            <td>
                                                                <input type="hidden" name="srjalan_id[]" value="new">
                                                                <input type="number" name="jumlah[]" class="rounded text-xs p-1 w-14" step="1" min="0" value="0">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div class="text-center mt-1">
                                                        <button type="submit" class="bg-orange-300 text-orange-700 rounded p-1">confirm</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <form action="{{ route('notas.delete_item', [$spk->id, $spk_produk_nota->id]) }}" class="ml-1 flex" method="POST" onsubmit="return confirm('Menghapus nota_item akan merubah data SPK dan Srjalan!')">
                                            @csrf
                                            <button class="text-red-400" type="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                        {{-- END - FORM INPUT NOTA ITEM KE SJ --}}
                                    </div>
                                    <div>
                                        <form action="{{ route('spks.spk_produk_nota_edit_keterangan', [$spk->id, $spk_produk_nota->id]) }}" method="POST" class="mt-2">
                                            @csrf
                                            <h5>Edit Keterangan:</h5>
                                            <div>
                                                <textarea name="keterangan" cols="30" rows="3" class="border-slate-300 rounded-lg text-xs p-0 placeholder:text-slate-400 w-full" placeholder="keterangan item...">{{ $spk_produk_nota->keterangan }}</textarea>
                                                <div class="text-end my-1">
                                                    <button type="submit" class="px-1 bg-emerald-200 text-emerald-500 rounded">confirm</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- END - SPK_PRODUK_NOTA_DETAIL --}}
                            @endforeach
                            <tr><td></td><td><div class="text-center">-----</div></td><td></td><td><div class="text-center">---</div></td></tr>
                            <tr><th></th><th>Total</th><th></th><th>{{ number_format($nota->harga_total,0,',','.') }}</th></tr>
                        </table>
                    </div>
                    {{-- END - Nota Items --}}
                    {{-- OPSI NOTA --}}
                    <div class="flex justify-end mt-1 mb-2 items-center">
                        <a href="{{ route('notas.print_out', $nota->id) }}" class="rounded text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                            </svg>
                        </a>
                        <button type="button" class="ml-1 border border-yellow-200 text-yellow-500 rounded font-bold text-md px-1" id="btn_pilihan_srjalan-{{ $key_nota }}" onclick="toggle_light(this.id,'pilihan_srjalan-{{ $key_nota }}',[],['bg-yellow-200'], 'block')">SJ</button>
                        <form action="{{ route('notas.delete',[$spk->id, $nota->id]) }}" method="POST" class="ml-1 flex" onsubmit="return confirm('Warning: Hapus Nota akan menghapus Surat Jalan terkait!')">
                            @csrf
                            <button type="submit" class="bg-red-200 text-red-500 rounded" name="nota_id" value="{{ $nota->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    {{-- END - OPSI NOTA --}}
                    {{-- SRJALAN_ALL -> PILIHAN SRJALAN --}}
                    <div class="hidden" id="pilihan_srjalan-{{ $key_nota }}">
                        <div class="mt-1 flex justify-end">
                            <form action="{{ route('sjs.srjalan_all', [$spk->id, $nota->id]) }}" method="POST" class="border border-yellow-300 rounded p-1 text-yellow-500" onsubmit="return confirm('Yakin input semua item Nota ke Srjalan terpilih?')">
                                @csrf
                                @foreach ($pilihan_srjalan as $key_pilih_srjalan => $srjalan)
                                <div class="flex items-center mt-1">
                                    <input type="radio" name="srjalan_id" id="pilih_srjalan_id-{{ $key_nota }}-{{ $key_pilih_srjalan }}" value="{{ $srjalan['id'] }}">
                                    <label for="pilih_srjalan_id-{{ $key_nota }}-{{ $key_pilih_srjalan }}" class="ml-1">SJ-{{ $srjalan['id'] }}</label>
                                </div>
                                @endforeach
                                <div class="flex items-center mt-1">
                                    <input type="radio" name="srjalan_id" id="pilih_srjalan_id-new" value="new">
                                    <label for="pilih_srjalan_id-new" class="ml-1">new</label>
                                </div>
                                <div class="text-center mt-1">
                                    <button type="submit" class="bg-emerald-300 text-emerald-700 rounded p-1">confirm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- END - SRJALAN_ALL -> PILIHAN SRJALAN --}}
                </div>
                @endforeach
                @endif
            </div>
            {{-- SJ --}}
            <div>
                <div class="flex justify-end">
                    <button id="btn_opsi_ekspedisi_transit" class="border rounded border-orange-300 text-orange-500 ml-1" onclick="toggle_light(this.id, 'pilihan_ekspedisi_transit', [], ['bg-orange-200'], 'block')">Opsi Ekspedisi/Transit</button>
                </div>

                {{-- PILIHAN EKSPEDISI/TRANSIT --}}
                <div id="pilihan_ekspedisi_transit" class="hidden">
                    <div class="border rounded p-1 grid grid-cols-2 mt-1">
                        <form method="POST" action="{{ route('sjs.edit_ekspedisi', $spk->id) }}">
                            @csrf
                            <div class="font-bold">Pilihan Ekspedisi:</div>
                            <div class="flex">
                                @foreach ($pilihan_ekspedisi as $key_ekspedisi => $p_ekspedisi)
                                @if ($p_ekspedisi['tipe'] === 'UTAMA')
                                <input type="radio" name="ekspedisi_id" id="pilihan_ekspedisi-{{ $key_ekspedisi }}" value="{{ $p_ekspedisi['id'] }}" checked>
                                @else
                                <input type="radio" name="ekspedisi_id" id="pilihan_ekspedisi-{{ $key_ekspedisi }}" value="{{ $p_ekspedisi['id'] }}">
                                @endif
                                <label for="pilihan_ekspedisi-{{ $key_ekspedisi }}" class="ml-1">
                                    <div class="font-semibold">{{ $p_ekspedisi['nama'] }}</div>
                                    @if ($p_ekspedisi['alamat']->long !== null)
                                    @foreach (json_decode($p_ekspedisi['alamat']->long, true) as $long)
                                    <div>{{ $long }}</div>
                                    @endforeach
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            <div class="font-semibold">Untuk:</div>
                            <div class="flex">
                                <div class="flex item-center">
                                    <input type="radio" name="srjalan_id" id="srjalan_id-semua" checked>
                                    <label for="srjalan_id-semua" class="ml-1">semua</label>
                                </div>
                                @foreach ($pilihan_srjalan as $key_p_srjalan => $p_srjalan)
                                <div class="flex item-center ml-2">
                                    <input type="radio" name="srjalan_id" id="srjalan_id-{{ $key_p_srjalan }}" value="{{ $p_srjalan['id'] }}">
                                    <label for="srjalan_id-{{ $key_p_srjalan }}" class="ml-1">SJ-{{ $p_srjalan['id'] }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3"><button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button></div>
                        </form>
                        <form action="{{ route('sjs.edit_transit', $spk->id) }}" method="POST">
                            @csrf
                            <div class="font-bold">Pilihan Transit:</div>
                            <div class="flex">
                                @foreach ($pilihan_transit as $key_transit => $transit)
                                @if ($transit['tipe'] === 'UTAMA')
                                <input type="radio" name="transit_id" id="pilihan_transit-{{ $key_transit }}" value="{{ $transit['id'] }}" checked>
                                @else
                                <input type="radio" name="transit_id" id="pilihan_transit-{{ $key_transit }}" value="{{ $transit['id'] }}">
                                @endif
                                <label for="pilihan_transit-{{ $key_transit }}" class="ml-1">
                                    <div class="font-semibold">{{ $transit['nama'] }}</div>
                                    @if ($transit['alamat']->long !== null)
                                    @foreach (json_decode($transit['alamat']->long, true) as $long)
                                    <div>{{ $long }}</div>
                                    @endforeach
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            <div class="font-semibold">Untuk:</div>
                            <div class="flex">
                                <div class="flex item-center">
                                    <input type="radio" name="srjalan_id" id="srjalan_id_transit-semua" checked>
                                    <label for="srjalan_id_transit-semua" class="ml-1">semua</label>
                                </div>
                                @foreach ($pilihan_srjalan as $key_p_srjalan => $p_srjalan)
                                <div class="flex item-center ml-2">
                                    <input type="radio" name="srjalan_id" id="srjalan_id_transit-{{ $key_p_srjalan }}" value="{{ $p_srjalan['id'] }}">
                                    <label for="srjalan_id_transit-{{ $key_p_srjalan }}" class="ml-1">SJ-{{ $p_srjalan['id'] }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3"><button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button></div>
                        </form>
                    </div>
                </div>
                {{-- END - PILIHAN EKSPEDISI/TRANSIT --}}
                @foreach ($notas as $key2 => $nota)
                @if (count($col_srjalans[$key2]) === 0)
                {{-- <div class="flex border-b pt-1 justify-center">
                    <div>none</div>
                </div> --}}
                @else
                @foreach ($col_srjalans[$key2] as $key_srjalan => $srjalan)
                <div class="border-t-4 pt-2">
                    <div class="grid grid-cols-2">
                        <table class="w-full">
                            <tr>
                                <td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $srjalan->no_srjalan }}</div></td>
                            </tr>
                            <tr>
                                <td>Tgl.</td><td>:</td>
                                <td>
                                    <div class="w-fit">
                                        <div class="flex" onclick="toggle_element('form_edit_tanggal_srjalan')">
                                            <div class="flex">
                                                @if ($srjalan->finished_at === null)
                                                <div>
                                                    <div class="rounded p-1 bg-red-500 text-white font-bold text-center">
                                                        <div>{{ date('d',strtotime($srjalan->created_at)) }}</div>
                                                        <div>{{ date('m-y',strtotime($srjalan->created_at)) }}</div>
                                                    </div>
                                                </div>
                                                @else
                                                <div>
                                                    <div class="rounded p-1 bg-yellow-500 text-white font-bold text-center">
                                                        <div>{{ date('d',strtotime($srjalan->created_at)) }}</div>
                                                        <div>{{ date('m-y',strtotime($srjalan->created_at)) }}</div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="flex ml-1 items-center">
                                                @if ($srjalan->finished_at !== null)
                                                <div>
                                                    <div class="rounded p-1 bg-emerald-500 text-white font-bold text-center">
                                                        <div>{{ date('d',strtotime($srjalan->finished_at)) }}</div>
                                                        <div>{{ date('m-y',strtotime($srjalan->finished_at)) }}</div>
                                                    </div>
                                                </div>
                                                @else
                                                <span class="font-bold">---</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hidden" id="form_edit_tanggal_srjalan">
                                <td colspan="3">
                                    <form action="{{ route('sjs.edit_tanggal', $srjalan->id) }}" method="POST" class="w-fit">
                                        @csrf
                                        <div>tgl. pembuatan:</div>
                                        <div class="flex items-center">
                                            <div class="flex">
                                                <select name="created_day" id="created_day" class="rounded text-xs pl-0 pr-7">
                                                    <option value="{{ date('d',strtotime($srjalan->created_at)) }}">{{ date('d',strtotime($srjalan->created_at)) }}</option>
                                                    <option value="">-</option>
                                                    @for ($i = 1; $i < 32; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <select name="created_month" id="created_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                                    <option value="{{ date('m',strtotime($srjalan->created_at)) }}">{{ date('m',strtotime($srjalan->created_at)) }}</option>
                                                    <option value="">-</option>
                                                    @for ($i = 1; $i < 13; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <select name="created_year" id="created_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                                    <option value="{{ date('Y',strtotime($srjalan->created_at)) }}">{{ date('Y',strtotime($srjalan->created_at)) }}</option>
                                                    <option value="">-</option>
                                                    @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-1">tgl. selesai:</div>
                                        @if ($srjalan->finished_at !== null)
                                        <div class="flex">
                                            <select name="finished_day" id="finished_day" class="rounded text-xs pl-0 pr-7">
                                                <option value="{{ date('d',strtotime($srjalan->finished_at)) }}">{{ date('d',strtotime($srjalan->finished_at)) }}</option>
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 32; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_month" id="finished_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="{{ date('m',strtotime($srjalan->finished_at)) }}">{{ date('m',strtotime($srjalan->finished_at)) }}</option>
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 13; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_year" id="finished_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="{{ date('Y',strtotime($srjalan->finished_at)) }}">{{ date('Y',strtotime($srjalan->finished_at)) }}</option>
                                                <option value="">-</option>
                                                @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        @else
                                        <div class="flex">
                                            <select name="finished_day" id="finished_day" class="rounded text-xs pl-0 pr-7">
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 32; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_month" id="finished_month" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="">-</option>
                                                @for ($i = 1; $i < 13; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="finished_year" id="finished_year" class="rounded text-xs pl-0 pr-7 ml-1">
                                                <option value="">-</option>
                                                @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        @endif
                                        <div class="text-end mt-1">
                                            <button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            {{-- EDIT NAMA TERTERA --}}
                            <tr>
                                <td>tertera</td><td>:</td>
                                @if ($srjalan->nama_tertera)
                                <td onclick="toggle_element('edit_nama_tertera')">{{ $srjalan->nama_tertera }}</td>
                                @else
                                <td onclick="toggle_element('edit_nama_tertera')">{{ $srjalan->pelanggan_nama }}</td>
                                @endif
                            </tr>
                            <tr class="hidden" id="edit_nama_tertera">
                                <td></td><td></td>
                                <td>
                                    <form method="POST" action="{{ route('sjs.edit_nama_tertera', $srjalan->id) }}">
                                        @csrf
                                        <input type="text" name="nama_tertera" value="{{ $srjalan->nama_tertera }}" class="border rounded text-xs p-1 w-full">
                                        <div class="text-end mt-1"><button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button></div>
                                    </form>
                                </td>
                            </tr>
                            {{-- END - EDIT NAMA TERTERA --}}
                        </table>
                        <div>
                            <div class="border rounded border-sky-400">
                                <div class="text-center"><h3 class="font-bold">Data Ekspedisi</h3></div>
                                <table class="w-full text-xs">
                                    <tr><td></td><td></td><td>{{ $srjalan->ekspedisi_nama }}</td></tr>
                                    <tr>
                                        <td class="align-top">Alamat</td><td class="align-top">:</td>
                                        <td class="align-top">
                                            @if ($srjalan->ekspedisi_long!==null)
                                            @foreach (json_decode($srjalan->ekspedisi_long,true) as $long)
                                            <div>{{ $long }}</div>
                                            @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kontak</td><td>:</td>
                                        <td>
                                            {{-- @if ($col_ekspedisi_kontaks[$key2][$key_srjalan] !==null)
                                            {{ $col_ekspedisi_kontaks[$key2][$key_srjalan] }}
                                            @else-@endif --}}
                                            @if ($srjalan->ekspedisi_kontak !==null)
                                            {{ json_decode($srjalan->ekspedisi_kontak, true)['nomor'] }}
                                            @else-@endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @if ($srjalan->transit_nama)
                            <div class="border rounded border-pink-400 mt-1">
                                <div class="text-center text-red-500"><h3>transit via:</h3></div>
                                <table class="w-full text-xs">
                                    <tr><td></td><td></td><td>{{ $srjalan->transit_nama }}</td></tr>
                                    <tr>
                                        <td class="align-top">Alamat</td><td class="align-top">:</td>
                                        <td class="align-top">
                                            @if ($srjalan->transit_long!==null)
                                            @foreach (json_decode($srjalan->transit_long,true) as $long)
                                            <div>{{ $long }}</div>
                                            @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kontak</td><td>:</td>
                                        <td>
                                            @if ($srjalan->transit_kontak !==null)
                                            @if (json_decode($srjalan->transit_kontak,true)['kodearea'] !== null)
                                            ({{ json_decode($srjalan->transit_kontak, true)['kodearea'] }}) {{ json_decode($srjalan->transit_kontak, true)['nomor'] }}
                                            @endif
                                            {{ json_decode($srjalan->transit_kontak, true)['nomor'] }}
                                            @else-@endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Srjalan Items --}}
                    <div class="border rounded px-1 py-2 mt-2" id="srjalan-items-{{ $key2 }}-{{ $key_srjalan }}">
                        <table class="w-full text-xs">
                            <tr>
                                <th>Jml.</th>
                                <th>Nama Barang</th>
                                <th>Jml. P</th>
                            </tr>
                            <tr><td><div class="text-center">---</div></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                            @foreach ($col_col_spk_produk_nota_srjalans[$key2][$key_srjalan] as $key_spk_produk_nota_srjalan => $spk_produk_nota_srjalan)
                            <tr>
                                <td>
                                    <div class="text-center">{{ $spk_produk_nota_srjalan->jumlah }}</div>
                                </td>
                                <td onclick="toggle_element('spk_produk_nota_srjalan_detail-{{ $key2 }}-{{ $key_srjalan }}-{{ $key_spk_produk_nota_srjalan }}')">
                                    {{ $spk_produk_nota_srjalan->spk_produk_nota->nama_nota }}
                                </td>
                                <td>
                                    <div class="text-center" onclick="toggle_element('spk_produk_nota_srjalan_colly-{{ $key_srjalan }}-{{ $key_spk_produk_nota_srjalan }}')">{{ $spk_produk_nota_srjalan->jumlah_packing }} {{ $spk_produk_nota_srjalan->tipe_packing }}</div>
                                    {{-- SPK_PRODUK_NOTA_SRJALAN_DETAIL --}}
                                    <form action="{{ route('sjs.edit_jumlah_packing', [$srjalan->id, $spk_produk_nota_srjalan->id]) }}" method="POST" id="spk_produk_nota_srjalan_colly-{{ $key_srjalan }}-{{ $key_spk_produk_nota_srjalan }}" class="border border-orange-400 rounded hidden">
                                        @csrf
                                        <table>
                                            <tr>
                                                <td><input type="number" name="jumlah_packing" value="{{ $spk_produk_nota_srjalan->jumlah_packing }}" class="rounded p-0 text-xs w-9"></td>
                                            </tr>
                                            <tr><td><button type="submit" class="bg-emerald-300 text-emerald-500 rounded px-1">confirm</button></td></tr>
                                        </table>
                                    </form>
                                    {{-- END - SPK_PRODUK_NOTA_SRJALAN_DETAIL --}}
                                </td>
                            </tr>
                            {{-- SPK_PRODUK_NOTA_DETAIL --}}
                            <tr class="spk_produk_nota_srjalan_detail-{{ $key2 }}-{{ $key_srjalan }} hidden" id="spk_produk_nota_srjalan_detail-{{ $key2 }}-{{ $key_srjalan }}-{{ $key_spk_produk_nota_srjalan }}">
                                <td colspan="3">
                                    <div class="flex">
                                        <form action="{{ route('sjs.delete_item', [$spk->id, $srjalan->id, $spk_produk_nota_srjalan->id]) }}" class="ml-1 flex" method="POST" onsubmit="return confirm('Menghapus nota_item akan merubah data SPK dan Nota!')">
                                            @csrf
                                            <button class="text-red-400" type="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                        {{-- END - FORM INPUT NOTA ITEM KE SJ --}}
                                    </div>
                                </td>
                            </tr>
                            {{-- END - SPK_PRODUK_NOTA_DETAIL --}}
                            @endforeach
                            <tr><td></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                            <tr>
                                <th></th><th>Total</th>
                                <td>
                                    @if ($srjalan->jumlah_packing !== null)
                                    <div onclick="toggle_element('total_jumlah_packing-{{ $key2 }}-{{ $key_srjalan }}')" class="text-center font-bold">
                                        @foreach (json_decode($srjalan->jumlah_packing, true) as $jumlah_packing)
                                        <div>{{ $jumlah_packing['jumlah_packing'] }} {{ $jumlah_packing['tipe_packing'] }}</div>
                                        @endforeach
                                    </div>
                                    @endif
                                    <form id="total_jumlah_packing-{{ $key2 }}-{{ $key_srjalan }}" action="{{ route('sjs.update_packing', $srjalan->id) }}" method="POST" class="border rounded mt-1 hidden">
                                        @csrf
                                        <table class="text-xs">
                                        @foreach ($data_packings[$key_srjalan] as $data_packing)
                                        <tr>
                                            <td>{{ $data_packing['tipe_packing'] }}</td><td>:</td>
                                            <td>
                                                <input type="number" name="jumlah_packing[]" value="{{ $data_packing['jumlah_packing'] }}" class="p-0 rounded text-xs w-9">
                                                <input type="hidden" name="jumlah[]" value="{{ $data_packing['jumlah'] }}">
                                                <input type="hidden" name="tipe_packing[]" value="{{ $data_packing['tipe_packing'] }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                        </table>
                                        <div class="text-center mt-1 pb-1">
                                            <button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1">confirm</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>
                    {{-- END - Srjalan Items --}}
                </div>
                {{-- OPSI SRJALAN --}}
                <div class="flex justify-end mt-1 mb-2 items-center">
                    <a href="{{ route('sjs.print_out', $srjalan->id) }}" class="rounded text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                        </svg>
                    </a>
                    <form action="{{ route('sjs.delete',[$spk->id, $srjalan->id]) }}" method="POST" class="ml-1 flex" onsubmit="return confirm('Warning: Hapus Srjalan akan reset Item Nota(jumlah_sudah_srjalan) terkait!')">
                        @csrf
                        <button type="submit" class="bg-red-200 text-red-500 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </form>
                    {{-- <form action="{{ route('sjs.update_packing',$srjalan->id) }}" method="POST" class="ml-1 flex">
                        @csrf
                        <button type="submit" class="px-1 bg-orange-200 text-orange-500 rounded">update colly</button>
                    </form> --}}
                </div>
                {{-- END - OPSI SRJALAN --}}
                @endforeach
                @endif
                @endforeach
            </div>
            {{-- END - SJ --}}
        </div>
    </div>
  </main>
</div>

<script>
    function toggle_detail_classes(btn_id, class_name) {
        $(`.${class_name}`).toggle(300);
        setTimeout(() => {
            let display = $(`.${class_name}`).css('display');
            let detail_button = document.getElementById(btn_id)
            if (display === 'table-row') {
                detail_button.classList.remove('text-yellow-500');
                detail_button.classList.add('text-yellow-700');
                detail_button.classList.add('bg-yellow-500');
            } else {
                detail_button.classList.remove('text-yellow-700');
                detail_button.classList.remove('bg-yellow-500');
                detail_button.classList.add('text-yellow-500');
            }
        }, 500);
    }

    const label_pelanggans = {!! json_encode($label_pelanggans, JSON_HEX_TAG) !!}
    $("#pelanggan_nama").autocomplete({
        source: label_pelanggans,
        select: function(event, ui) {
            // console.log(ui);
            $("#pelanggan_id").val(ui.item.id);
            $("#reseller_id").val(ui.item.reseller_id);
            // console.log(event);
            // alert(ui.item.name);
        }
    });

    let index_spk_item = 0;
    function addSPKItem(tr_id, parent_id) {
        document.getElementById(tr_id).remove();
        let parent = document.getElementById(parent_id);
        parent.insertAdjacentHTML('beforeend',
        `<tr class="tr_add_items">
            <td style="width:75%">
                <div class="flex items-center mt-1">
                    <button id="toggle_produk_keterangan-${index_spk_item}" type="button" class="border border-yellow-500 rounded text-yellow-500" onclick="toggle_light(this.id,'produk_keterangan-${index_spk_item}',[],['bg-yellow-300'],'block')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                    <input type="text" name="produk_nama[]" id="produk_nama-${index_spk_item}" class="border-slate-300 rounded-lg text-xs p-1 ml-1 placeholder:text-slate-400 w-full" placeholder="nama item...">
                    <input type="hidden" name="produk_id[]" id="produk_id-${index_spk_item}">
                </div>
                <div class="mt-1 hidden" id="produk_keterangan-${index_spk_item}">
                    <textarea name="produk_keterangan[]" cols="30" rows="3" class="border-slate-300 rounded-lg text-xs p-0 placeholder:text-slate-400" placeholder="keterangan item..."></textarea>
                </div>
            </td>
            <td><div class="text-center"><input type="number" name="produk_jumlah[]" id="produk_jumlah" min="1" step="1" class="border-slate-300 rounded-lg text-xs p-1 w-1/2"></div></td>
        </tr>
        <tr id="tr_add_item">
            <td>
                <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="addSPKItem('tr_add_item', 'table_new_spk_items')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </td>
        </tr>
        `);
        setTimeout(() => {
            setAutocompleteSPKItem(`produk_nama-${index_spk_item}`, `produk_nama-${index_spk_item}`, `produk_id-${index_spk_item}`);
            index_spk_item++;
            toggle_btn_confirm_add_spk_items();
        }, 100);
    }

    const label_produks = {!! json_encode($label_produks, JSON_HEX_TAG) !!};

    function setAutocompleteSPKItem(input_id, label_id, value_id) {
        $(`#${input_id}`).autocomplete({
            source: label_produks,
            select: function (event, ui) {
                // console.log(ui.item);
                document.getElementById(label_id).value = ui.item.value;
                document.getElementById(value_id).value = ui.item.id;
            }
        });
    }

    function toggle_btn_confirm_add_spk_items() {
        const tr_add_items = document.querySelectorAll('.tr_add_items');
        if (tr_add_items.length > 0) {
            $('#btn_confirm_add_spk_items').show(300);
        } else {
            $('#btn_confirm_add_spk_items').hide(300);
        }
    }
</script>
@endsection
{{-- <a href="https://www.flaticon.com/free-icons/fox" title="fox icons">Fox icons created by Freepik - Flaticon</a> --}}
{{-- cat --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Freepik - Flaticon</a> --}}
{{-- Honey Badger --}}
{{-- <a href="https://www.flaticon.com/free-icons/badger" title="badger icons">Badger icons created by Freepik - Flaticon</a> --}}
{{-- Panda --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Smashicons - Flaticon</a> --}}
