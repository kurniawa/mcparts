@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
  <main>
      <x-errors-any></x-errors-any>
      <x-validation-feedback></x-validation-feedback>
    <div class="flex justify-center">
        @foreach ($spk_menus as $key_spk_menu => $spk_menu)
        @if ($route_now === $spk_menu['route'])
        @if ($key_spk_menu !== 0)
        <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold ml-2">{{ $spk_menu['name'] }}</div>
        @else
        <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold">{{ $spk_menu['name'] }}</div>
        @endif
        @else
        @if ($key_spk_menu !== 0)
        <a href="{{ route($spk_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100 ml-2">{{ $spk_menu['name'] }}</a>
        @else
        <a href="{{ route($spk_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100">{{ $spk_menu['name'] }}</a>
        @endif
        @endif
        @endforeach
    </div>
      <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
            {{-- <div class="flex">
                <button id="btn_filter" class="border rounded border-yellow-300 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content', [], ['bg-yellow-200'], 'inline-block')">Filter</button>
                <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_spk" onclick="toggle_light(this.id, 'form_new_spk', [], ['bg-emerald-200'], 'block')">+ SPK</button>
            </div> --}}
        <div class="flex mt-1">
            {{-- SEARCH / FILTER --}}
            <div>
                <div class="rounded p-2 bg-white shadow drop-shadow inline-block" id="filter-content">
                    <form action="" method="GET">
                        {{-- <div class="flex items-center">
                            <div><input type="radio" name="tipe_filter" value="spk" id="radio_spk" checked><label for="radio_spk" class="ml-1">SPK</label></div>
                            <div class="ml-3"><input type="radio" name="tipe_filter" value="nota" id="radio_nota"><label for="radio_nota" class="ml-1">Nota</label></div>
                            <div class="ml-3"><input type="radio" name="tipe_filter" value="sj" id="radio_sj"><label for="radio_sj" class="ml-1">SJ</label></div>
                        </div> --}}
                        <input type="hidden" name="tipe_filter" value="spk">
                        <div class="ml-1 mt-2 flex">
                            <div>
                                <label>Customer:</label>
                                <div class="flex mt-1">
                                    <input type="text" class="border rounded text-xs p-1" name="nama_pelanggan" placeholder="Nama Customer..." id="nama_pelanggan">
                                    <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                                </div>
                            </div>
                            <div class="flex items-center ml-2">
                                <div class="flex"><input type="radio" name="timerange" value="today" id="now" onclick="set_time_range('now')"><label for="now" class="ml-1 whitespace-nowrap">now</label></div>
                                <div class="ml-3 flex"><input type="radio" name="timerange" value="7d" id="7d" onclick="set_time_range('7d')"><label for="7d" class="ml-1 whitespace-nowrap">7d</label></div>
                                {{-- <div class="ml-3"><input type="radio" name="timerange" value="30d" id="30d" onclick="set_time_range('30d')"><label for="30d" class="ml-1 whitespace-nowrap">30d</label></div> --}}
                                <div class="ml-3 flex"><input type="radio" name="timerange" value="bulan_ini" id="bulan_ini" onclick="set_time_range('bulan_ini')"><label for="bulan_ini" class="ml-1 whitespace-nowrap">bulan ini</label></div>
                                <div class="ml-3 flex"><input type="radio" name="timerange" value="bulan_lalu" id="bulan_lalu" onclick="set_time_range('bulan_lalu')"><label for="bulan_lalu" class="ml-1 whitespace-nowrap">bulan lalu</label></div>
                                <div class="ml-3 flex"><input type="radio" name="timerange" value="this_year" id="tahun_ini" onclick="set_time_range('tahun_ini')"><label for="tahun_ini" class="ml-1 whitespace-nowrap">tahun ini</label></div>
                                <div class="ml-3 flex"><input type="radio" name="timerange" value="last_year" id="tahun_lalu" onclick="set_time_range('tahun_lalu')"><label for="tahun_lalu" class="ml-1 whitespace-nowrap">tahun lalu</label></div>
                            </div>
                        </div>
                        <div class="flex mt-2">
                            <div class="ml-1 flex">
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
                                        <button type="submit" class="ml-2 flex items-center bg-orange-500 text-white py-1 px-3 rounded hover:bg-orange-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                            </svg>
                                            <span class="ml-1">filter/search</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END - SEARCH / FILTER --}}

            {{-- FORM_NEW_SPK --}}
            <div id="form_new_spk" class="ml-2 w-full">
                <div class="flex">
                    <form action="{{ route('spks.store') }}" method="POST" class="w-full">
                        @csrf
                        <div class="border rounded p-2">
                            <div class="border-b pb-3">
                                <table>
                                    <tr>
                                        <td>Tanggal</td><td><div class="mx-2">:</div></td>
                                        <td class="py-1">
                                            <div class="flex">
                                                {{-- <select name="day" id="day" class="rounded text-xs">
                                                    <option value="{{ date('d') }}">{{ date('d') }}</option>
                                                    @for ($i = 1; $i < 32; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <select name="month" id="month" class="rounded text-xs ml-1">
                                                    <option value="{{ date('m') }}">{{ date('m') }}</option>
                                                    @for ($i = 1; $i < 13; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <select name="year" id="year" class="rounded text-xs ml-1">
                                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                                    <option value="">-</option>
                                                    @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select> --}}
                                                <input type="text" name="day" id="day" class="border rounded text-xs p-1 w-8" placeholder="dd" value="{{ date('d') }}">
                                                <input type="text" name="month" id="month" class="border rounded text-xs p-1 w-8 ml-1" placeholder="mm" value="{{ date('m') }}">
                                                <input type="text" name="year" id="year" class="border rounded text-xs p-1 w-11 ml-1" placeholder="yyyy" value="{{ date('Y') }}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Untuk</td><td><div class="mx-2">:</div></td>
                                        <td class="py-1">
                                            <input type="text" name="pelanggan_nama" id="new_spk-pelanggan_nama" placeholder="nama pelanggan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                            <input type="hidden" name="pelanggan_id" id="new_spk-pelanggan_id">
                                            <input type="hidden" name="reseller_id" id="new_spk-reseller_id">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                                        <td class="py-1"><input type="text" name="keterangan" placeholder="judul/keterangan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mt-2">
                                <table id="table_spk_items" class="text-slate-500 w-full">
                                    <tr><th>Nama Item</th><th>Jumlah</th></tr>
                                    <tr id="tr_add_item">
                                        <td>
                                            <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="addSPKItem('tr_add_item','table_spk_items')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td>
                                            <div class="flex items-center">
                                                <button id="toggle_produk_keterangan" type="button" class="border border-yellow-500 rounded text-yellow-500" onclick="toggleButton(this.id,'produk_keterangan',['bg-yellow-300'],null)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>
                                                </button>
                                                <input type="text" name="produk_nama[]" id="produk_nama" class="border-slate-300 rounded-lg text-xs p-1 ml-1 placeholder:text-slate-400" placeholder="nama item...">
                                            </div>
                                            <div class="mt-1" id="produk_keterangan">
                                                <textarea name="produk_keterangan[]" id="produk_keterangan" cols="30" rows="3" class="border-slate-300 rounded-lg text-xs p-0 placeholder:text-slate-400" placeholder="keterangan item..."></textarea>
                                            </div>
                                        </td>
                                        <td><div class="text-center"><input type="number" name="produk_jumlah[]" id="produk_jumlah" class="border-slate-300 rounded-lg text-xs p-1 w-1/2"></div></td>
                                    </tr> --}}
                                </table>
                            </div>
                        </div>
                        <div class="flex justify-center mt-3">
                            <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold py-1 px-3 hover:bg-emerald-300">proses new SPK</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END - FORM_NEW_SPK --}}
        </div>
    </div>
    <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <div class="grid grid-cols-3 gap-1">
            <div class="text-center bg-violet-200 rounded-t font-bold text-slate-700 py-1">SPK</div>
            <div class="text-center bg-emerald-200 rounded-t font-bold text-slate-700 py-1">Nota</div>
            <div class="text-center bg-orange-200 rounded-t font-bold text-slate-700 py-1">SJ</div>
            @foreach ($spks as $key => $spk)
            {{-- SPK --}}
            <div>
                <div class="grid grid-cols-3 border-t pt-1">
                    <div class="grow">
                        <a href="{{ route('spks.show', $spk->id) }}" class="font-bold text-indigo-500" href="">{{ $spk->no_spk }}</a>
                        <div><a href="" class="text-indigo-800">{{ $nama_pelanggans[$key] }}</a></div>
                        <div>
                            <button id="toggle-spk-items-{{ $key }}" class="rounded bg-white shadow drop-shadow" onclick="showDropdown(this.id, 'spk-items-{{ $key }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center">
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
                            <span class="font-bold">--</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex">
                        @if ($spk::user($spk->created_by)->profile_picture)
                            <div class="w-8 h-8 rounded-full overflow-hidden">
                                <img class="w-full" src="{{ asset("storage/" . $spk::user($spk->created_by)->profile_picture) }}" alt="">
                            </div>
                        @endif
                    </div>
                </div>
                {{-- SPK Items --}}
                <div class="border rounded hidden px-1" id="spk-items-{{ $key }}">
                    <table class="w-full text-xs">
                        @foreach ($col_spk_produks[$key] as $spk_produk)
                        <tr>
                            <td>{{ $spk_produk->nama_produk }}</td>
                            <td>
                                {{ $spk_produk->jumlah }}
                                @if ($spk_produk->deviasi_jml > 0)
                                <span class="text-indigo-500"> +{{ $spk_produk->deviasi_jml }}</span>
                                @elseif ($spk_produk->deviasi_jml < 0)
                                <span class="text-pink-500"> -{{ $spk_produk->deviasi_jml }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                {{-- END - SPK Items --}}
            </div>
            {{-- END - SPK --}}
            <div>
                @if (count($col_notas[$key]) === 0)
                <div class="flex border-t pt-1 justify-center">
                    <div>none</div>
                </div>
                @else
                @foreach ($col_notas[$key] as $key_nota => $nota)
                <div>
                    <div class="grid grid-cols-2 border-t pt-1">
                        <div>
                            <a class="font-bold text-emerald-400" href="">{{ $nota->no_nota }}</a>
                            <div>
                                <button id="toggle-nota-items-{{ $key }}" class="rounded bg-white shadow drop-shadow" onclick="showDropdown(this.id, 'nota-items-{{ $key }}-{{ $key_nota }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex">
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
                                <span class="font-bold">--</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Nota Items --}}
                    <div class="border rounded px-1 hidden" id="nota-items-{{ $key }}-{{ $key_nota }}">
                        <table class="w-full text-xs">
                            @foreach ($col_spk_produk_notas[$key][$key_nota] as $spk_produk_nota)
                            <tr><td>{{ $spk_produk_nota->nama_nota }}</td><td>{{ $spk_produk_nota->jumlah }}</td></tr>
                            @endforeach
                        </table>
                    </div>
                    {{-- END - Nota Items --}}
                </div>
                @endforeach
                @endif
            </div>
            <div>
                @foreach ($col_notas[$key] as $key2 => $nota)
                @if (count($col_srjalans[$key][$key2]) === 0)
                <div class="flex border-t pt-1 justify-center">
                    <div>none</div>
                </div>
                @else
                @foreach ($col_srjalans[$key][$key2] as $key_srjalan=>$srjalan)
                <div>
                    <div class="grid grid-cols-2 border-t pt-1">
                        <div>
                            <a class="font-bold text-sky-400" href="#">{{ $srjalan->no_srjalan }}</a>
                            <span>ekspedisi: </span><a href="#" class="text-sky-700">{{ $srjalan->ekspedisi_nama }}</a>
                            @if ($srjalan->transit_nama)
                            <span> - transit: </span><a href="#" class="text-sky-700">{{ $srjalan->transit_nama }}</a>
                            @endif
                            <div>
                                <button id="toggle-srjalan-items-{{ $key }}-{{ $key2 }}-{{ $key_srjalan }}" class="rounded bg-white shadow drop-shadow" onclick="showDropdown(this.id, 'srjalan-items-{{ $key }}-{{ $key2 }}-{{ $key_srjalan }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex">
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
                                <span class="font-bold">--</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Srjalan Items --}}
                    <div class="border rounded px-1 hidden" id="srjalan-items-{{ $key }}-{{ $key2 }}-{{ $key_srjalan }}">
                        <table class="w-full text-xs">
                            @foreach ($col_spk_produk_nota_srjalans[$key][$key2] as $spk_produk_nota_srjalan)
                            <tr><td>{{ $spk_produk_nota_srjalan->spk_produk_nota->nama_nota }}</td><td>{{ $spk_produk_nota_srjalan->jumlah_packing }}</td><td>{{ $spk_produk_nota_srjalan->tipe_packing }}</td></tr>
                            @endforeach
                        </table>
                    </div>
                    {{-- END - Srjalan Items --}}
                </div>
                @endforeach
                @endif
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
  </main>
</div>
{{-- <div class="bg-yellow-200">test</div> --}}
<script>
    // $('#filter-content').hide();
    function toggleFilter(filter_button_id, filter_content_id) {
        $(`#${filter_content_id}`).toggle(350);
        setTimeout(() => {
            // console.log($(`#${filter_content_id}`).css('display'));
            let display = $(`#${filter_content_id}`).css('display');
            let filter_button = document.getElementById(filter_button_id)
            if (display === 'inline-block') {
                filter_button.classList.remove('text-yellow-500');
                filter_button.classList.add('text-yellow-700');
                filter_button.classList.add('bg-yellow-500');
            } else {
                filter_button.classList.remove('text-yellow-700');
                filter_button.classList.remove('bg-yellow-500');
                filter_button.classList.add('text-yellow-500');
            }
        }, 500);
    }

    // SET AUTOCOMPLETE PELANGGAN
    const label_pelanggans = {!! json_encode($label_pelanggans, JSON_HEX_TAG) !!}
    $('#nama_pelanggan').autocomplete({
        source: label_pelanggans,
        select: function (event, ui) {
            // console.log(ui.item);
            document.getElementById('pelanggan_id').value = ui.item.id;
            document.getElementById('nama_pelanggan').value = ui.item.value;
        }
    });
    // END - SET AUTOCOMPLETE PELANGGAN

    // FUNGSI SPK
    $('#new_spk-pelanggan_nama').autocomplete({
        source: label_pelanggans,
        select: function (event, ui) {
            // console.log(ui.item);
            document.getElementById('new_spk-pelanggan_id').value = ui.item.id;
            document.getElementById('new_spk-pelanggan_nama').value = ui.item.value;
            $("#new_spk-reseller_id").val(ui.item.reseller_id);
            // console.log(reseller_id);
        }
    });

    let index_spk_item = 0;
    function addSPKItem(tr_id, parent_id) {
        document.getElementById(tr_id).remove();
        let parent = document.getElementById(parent_id);
        parent.insertAdjacentHTML('beforeend',
        `<tr>
            <td>
                <div class="flex items-center mt-1">
                    <button id="toggle_produk_keterangan-${index_spk_item}" type="button" class="border border-yellow-500 rounded text-yellow-500" onclick="toggleButton(this.id,'produk_keterangan-${index_spk_item}',['bg-yellow-300'],null)">
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
                <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="addSPKItem('tr_add_item', 'table_spk_items')">
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
    // END - FUNGSI SPK
</script>
@endsection
{{-- <a href="https://www.flaticon.com/free-icons/fox" title="fox icons">Fox icons created by Freepik - Flaticon</a> --}}
{{-- cat --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Freepik - Flaticon</a> --}}
{{-- Honey Badger --}}
{{-- <a href="https://www.flaticon.com/free-icons/badger" title="badger icons">Badger icons created by Freepik - Flaticon</a> --}}
{{-- Panda --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Smashicons - Flaticon</a> --}}
