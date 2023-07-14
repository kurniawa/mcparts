@extends('layouts.main')
@section('content')
{{-- <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">App</h1>
    </div>
  </header> --}}
  <main>
    {{-- SEARCH / FILTER --}}
    <x-errors-any></x-errors-any>
    <div class="mx-1 max-w-7xl py-1 sm:px-6 lg:px-8 text-xs">
        <div><button id="filter" class="border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggleFilter(this.id,'filter-content')">Filter</button></div>
        <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1" id="filter-content">
            <form action="" method="GET">
                <div class="flex items-center">
                    <div><input type="radio" name="tipe_filter" value="spk" id="radio_spk" checked><label for="radio_spk" class="ml-1">SPK</label></div>
                    <div class="ml-3"><input type="radio" name="tipe_filter" value="nota" id="radio_nota"><label for="radio_nota" class="ml-1">Nota</label></div>
                    <div class="ml-3"><input type="radio" name="tipe_filter" value="sj" id="radio_sj"><label for="radio_sj" class="ml-1">SJ</label></div>
                </div>
                <div class="ml-1 mt-2 flex">
                    <div>
                        <label>Customer:</label>
                        <div class="flex mt-1">
                            <input type="text" class="input" name="nama_pelanggan" placeholder="Nama Customer...">
                        </div>
                    </div>
                    <div class="flex items-center ml-2">
                        <div><input type="radio" name="timerange" value="today" id="now" onclick="setTimeRange('now')"><label for="now" class="ml-1">now</label></div>
                        <div class="ml-3"><input type="radio" name="timerange" value="7d" id="7d" onclick="setTimeRange('7d')"><label for="7d" class="ml-1">7d</label></div>
                        <div class="ml-3"><input type="radio" name="timerange" value="30d" id="30d" onclick="setTimeRange('30d')"><label for="30d" class="ml-1">30d</label></div>
                        <div class="ml-3"><input type="radio" name="timerange" value="this_year" id="tahun_ini" onclick="setTimeRange('tahun_ini')"><label for="tahun_ini" class="ml-1">tahun ini</label></div>
                        <div class="ml-3"><input type="radio" name="timerange" value="last_year" id="tahun_lalu" onclick="setTimeRange('tahun_lalu')"><label for="tahun_lalu" class="ml-1">tahun lalu</label></div>
                    </div>
                </div>
                <div class="flex mt-2">
                    <div class="ml-1 flex">
                        <div>
                            <label>Dari:</label>
                            <div class="flex">
                                <select name="from_day" id="from_day" class="rounded">
                                    <option value="">-</option>
                                    @for ($i = 1; $i < 32; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <select name="from_month" id="from_month" class="rounded ml-1">
                                    <option value="">-</option>
                                    @for ($i = 1; $i < 13; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <select name="from_year" id="from_year" class="rounded ml-1">
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
                                <select name="to_day" id="to_day" class="rounded">
                                    <option value="">-</option>
                                    @for ($i = 1; $i < 32; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <select name="to_month" id="to_month" class="rounded ml-1">
                                    <option value="">-</option>
                                    @for ($i = 1; $i < 13; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <select name="to_year" id="to_year" class="rounded ml-1">
                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                    <option value="">-</option>
                                    @for ($i = ((int)date("Y") - 30); $i < ((int)date("Y") + 30); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <button type="submit" class="ml-2 flex items-center bg-orange-500 text-white py-1 px-3 rounded hover:bg-orange-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <span class="ml-1">Search</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- END - SEARCH / FILTER --}}
    <div class="mx-1 max-w-7xl py-1 sm:px-6 lg:px-8 text-xs">
        <div class="grid grid-cols-3 gap-1">
            <div class="text-center bg-violet-200 rounded-t font-bold text-slate-700 py-1">SPK</div>
            <div class="text-center bg-emerald-200 rounded-t font-bold text-slate-700 py-1">Nota</div>
            <div class="text-center bg-orange-200 rounded-t font-bold text-slate-700 py-1">SJ</div>
            @foreach ($spks as $key => $spk)
            {{-- SPK --}}
            <div>
                <div class="grid grid-cols-2 border-t pt-1">
                    <div>
                        <a class="font-bold text-indigo-500" href="">{{ $spk->no_spk }}</a>
                        <div>{{ $spk->pelanggan_nama }}</div>
                        <div>
                            <button id="toggle-spk-items-{{ $key }}" class="rounded bg-white shadow drop-shadow" onclick="showDropdown(this.id, 'spk-items-{{ $key }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex">
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
                                    <div>{{ date('d',strtotime($spk->created_at)) }}</div>
                                    <div>{{ date('m-y',strtotime($spk->created_at)) }}</div>
                                </div>
                            </div>
                            @else
                            <span class="font-bold">--</span>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- SPK Items --}}
                <div class="border rounded hidden px-1" id="spk-items-{{ $key }}">
                    <table class="w-full text-xs">
                        @foreach ($col_spk_produks[$key] as $spk_produk)
                        <tr><td>{{ $spk_produk->nama_produk }}</td><td>{{ $spk_produk->jumlah }}</td></tr>
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
                                    <div class="rounded p-1 bg-pink-400 text-white font-bold text-center">
                                        <div>{{ date('d',strtotime($nota->created_at)) }}</div>
                                        <div>{{ date('m-y',strtotime($nota->created_at)) }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="flex ml-1 items-center">
                                @if ($nota->finished_at !== null)
                                <div>
                                    <div class="rounded p-1 bg-blue-500 text-white font-bold text-center">
                                        <div>{{ date('d',strtotime($nota->created_at)) }}</div>
                                        <div>{{ date('m-y',strtotime($nota->created_at)) }}</div>
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
                            <a class="font-bold text-sky-400" href="">{{ $srjalan->no_srjalan }}</a>
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
                                    <div class="rounded p-1 bg-violet-400 text-white font-bold text-center">
                                        <div>{{ date('d',strtotime($srjalan->created_at)) }}</div>
                                        <div>{{ date('m-y',strtotime($srjalan->created_at)) }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="flex ml-1 items-center">
                                @if ($srjalan->finished_at !== null)
                                <div>
                                    <div class="rounded p-1 bg-sky-500 text-white font-bold text-center">
                                        <div>{{ date('d',strtotime($srjalan->created_at)) }}</div>
                                        <div>{{ date('m-y',strtotime($srjalan->created_at)) }}</div>
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
                            <tr><td>{{ $spk_produk_nota_srjalan->spk_produk_nota->nama_nota }}</td><td>{{ $spk_produk_nota_srjalan->jml_packing }}</td><td>{{ $spk_produk_nota_srjalan->tipe_packing }}</td></tr>
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

<script>
    $('#filter-content').hide();
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

    function setTimeRange(timerange) {
        console.log(timerange);
        let from_day, from_month, from_year;
        let to_day, to_month, to_year;
        if (timerange === 'now') {
            const date = new Date();
            from_day = date.getDate();
            from_month = date.getMonth()+1;
            from_year = date.getFullYear();
            to_day = from_day;
            to_month = from_month;
            to_year = from_year;
        } else if (timerange === '7d') {
            const to_date = new Date();
            const from_date = new Date(new Date().setDate(to_date.getDate() - 7));
            from_day = from_date.getDate();
            from_month = from_date.getMonth()+1;
            from_year = from_date.getFullYear();
            to_day = to_date.getDate();
            to_month = to_date.getMonth()+1;
            to_year = to_date.getFullYear();
        } else if (timerange === '30d') {
            const to_date = new Date();
            const from_date = new Date(new Date().setDate(to_date.getDate() - 30));
            from_day = from_date.getDate();
            from_month = from_date.getMonth()+1;
            from_year = from_date.getFullYear();
            to_day = to_date.getDate();
            to_month = to_date.getMonth()+1;
            to_year = to_date.getFullYear();
        } else if (timerange === 'tahun_ini') {
            const date = new Date();
            from_day = 1;
            from_month = 1;
            from_year = date.getFullYear();
            to_day = date.getDate();
            to_month = date.getMonth()+1;
            to_year = date.getFullYear();
        } else if (timerange === 'tahun_lalu') {
            const date = new Date();
            from_day = 1;
            from_month = 1;
            from_year = date.getFullYear() - 1;
            to_day = 31;
            to_month = 12;
            to_year = from_year;
        }

        document.getElementById('from_day').value = from_day;
        document.getElementById('from_month').value = from_month;
        document.getElementById('from_year').value = from_year;
        document.getElementById('to_day').value = to_day;
        document.getElementById('to_month').value = to_month;
        document.getElementById('to_year').value = to_year;
    }
</script>
@endsection
