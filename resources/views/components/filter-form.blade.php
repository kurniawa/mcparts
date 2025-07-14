<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
    {{-- SEARCH / FILTER --}}
    <form action="" method="GET" class="rounded p-2 bg-white shadow drop-shadow inline-block">
        <div class="ml-1 mt-2 flex">
            @if ($showCustomer)
            <div>
                <label>Customer:</label>
                <div class="flex mt-1">
                    <input type="text" class="border rounded text-xs p-1" name="pelanggan_nama" placeholder="Nama Customer..." id="pelanggan_nama">
                    <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                </div>
            </div>
            @endif
            <div class="flex items-center ml-2">
                <div><input type="radio" name="timerange" value="today" id="now" onclick="set_time_range('now')"><label for="now" class="ml-1">now</label></div>
                <div class="ml-3"><input type="radio" name="timerange" value="7d" id="7d" onclick="set_time_range('7d')"><label for="7d" class="ml-1">7d</label></div>
                <div class="ml-3"><input type="radio" name="timerange" value="triwulan" id="triwulan" onclick="set_time_range('triwulan')"><label for="triwulan" class="ml-1">triwulan</label></div>
                <div class="ml-3"><input type="radio" name="timerange" value="triwulan_lalu" id="triwulan_lalu" onclick="set_time_range('triwulan_lalu')"><label for="triwulan_lalu" class="ml-1">triwulan lalu</label></div>
                {{-- <div class="ml-3"><input type="radio" name="timerange" value="30d" id="30d" onclick="set_time_range('30d')"><label for="30d" class="ml-1">30d</label></div> --}}
                <div class="ml-3"><input type="radio" name="timerange" value="bulan_ini" id="bulan_ini" onclick="set_time_range('bulan_ini')"><label for="bulan_ini" class="ml-1">bulan ini</label></div>
                <div class="ml-3"><input type="radio" name="timerange" value="bulan_lalu" id="bulan_lalu" onclick="set_time_range('bulan_lalu')"><label for="bulan_lalu" class="ml-1">bulan lalu</label></div>
                <div class="ml-3"><input type="radio" name="timerange" value="this_year" id="tahun_ini" onclick="set_time_range('tahun_ini')"><label for="tahun_ini" class="ml-1">tahun ini</label></div>
                <div class="ml-3"><input type="radio" name="timerange" value="last_year" id="tahun_lalu" onclick="set_time_range('tahun_lalu')"><label for="tahun_lalu" class="ml-1">tahun lalu</label></div>
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
                            <span class="ml-1">Search</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- END - SEARCH / FILTER --}}
</div>