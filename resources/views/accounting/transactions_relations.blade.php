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
    <div class="relative rounded mt-9">
        <div class="flex absolute -top-6 left-1/2 -translate-x-1/2 z-20">
            @foreach ($accounting_menus as $key_accounting_menu => $accounting_menu)
            @if ($route_now === $accounting_menu['route'])
            @if ($key_accounting_menu !== 0)
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold ml-2">{{ $accounting_menu['name'] }}</div>
            @else
            <div class="border rounded-t-lg bg-white px-1 border-b-4 font-bold">{{ $accounting_menu['name'] }}</div>
            @endif
            @else
            @if ($key_accounting_menu !== 0)
            <a href="{{ route($accounting_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100 ml-2">{{ $accounting_menu['name'] }}</a>
            @else
            <a href="{{ route($accounting_menu['route']) }}" class="border rounded-t-lg bg-white px-1 hover:bg-slate-100">{{ $accounting_menu['name'] }}</a>
            @endif
            @endif
            @endforeach
        </div>
        <div class="relative bg-white border-t z-10">
            <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs mt-1">
                <div class="flex">
                    <button id="filter" class="border rounded border-yellow-500 text-yellow-500 px-3 py-1" onclick="toggle_light(this.id,'filter-content',[],['bg-yellow-200'], 'block')">Filter</button>
                    <button class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_relasi_transaksi" onclick="toggle_light(this.id, 'form_new_relasi_transaksi', [], ['bg-emerald-200'], 'block')">+ relasi_transaksi</button>
                    {{-- <button type="submit" class="border rounded border-indigo-300 text-indigo-500 font-semibold px-3 py-1 ml-1" id="btn_new_barang" onclick="toggle_light(this.id, 'form_new_barang', [], ['bg-indigo-200'], 'block')">+ Barang</button> --}}
                </div>
                {{-- SEARCH / FILTER --}}
                <div class="hidden" id="filter-content">
                    <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                        <form action="" method="GET">
                            <div class="flex">
                                <div>
                                    <label>User Instance:</label>
                                    <div class="flex mt-1">
                                        <input type="radio" name="user_instance_id" id="filter-user_instance_id-all" value="all" checked>
                                        <label for="filter-user_instance_id-all" class="ml-1">all</label>
                                    </div>
                                    @foreach ($user_instances_all as $key => $user_instance)
                                    <div class="flex mt-1">
                                        <input type="radio" name="user_instance_id" id="filter-user_instance_id-{{ $key }}" value="{{ $user_instance->id }}">
                                        <label for="filter-user_instance_id-{{ $key }}" class="ml-1">{{ $user_instance->instance_type }} - {{ $user_instance->instance_name }} - {{ $user_instance->branch }} - {{ $user_instance->account_number }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="ml-2">
                                    <div class="flex">
                                        <div class="ml-1">
                                            <label>Tipe:</label>
                                            <div>
                                                <select name="type" id="filter-type" class="rounded py-1 text-xs">
                                                    <option value="ALL">ALL</option>
                                                    <option value="UANG MASUK">UANG MASUK</option>
                                                    <option value="UANG KELUAR">UANG KELUAR</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ml-1">
                                            <label>Deskripsi:</label>
                                            <div>
                                                <input type="text" name="desc" id="filter-desc" class="border rounded p-1 text-xs w-60">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex mt-1">
                                        <div class="ml-1">
                                            <label>Kategori lvl.1:</label>
                                            <div>
                                                <input type="text" name="kategori_level_one" id="filter-kategori_level_one" class="border rounded p-1 text-xs">
                                            </div>
                                        </div>
                                        <div class="ml-1">
                                            <label>Kategori lvl.2:</label>
                                            <div>
                                                <input type="text" name="kategori_level_two" id="filter-kategori_level_two" class="border rounded p-1 text-xs">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex mt-2 justify-center">
                                <div>
                                    <button type="submit" class="ml-2 flex items-center bg-orange-500 text-white py-1 px-3 rounded hover:bg-orange-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                        <span class="ml-1">Filter</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- END - SEARCH / FILTER --}}

                {{-- FORM NEW RELASI TRANSAKSI --}}
                <div class="hidden" id="form_new_relasi_transaksi">
                    <div class="flex justify-center">
                        <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                            <form action="{{ route('accounting.store_transactions_relations') }}" method="POST">
                                @csrf
                                <div class="flex">
                                    <div>
                                        <label>User Instance:</label>
                                        @foreach ($user_instances as $key => $user_instance)
                                        @if (Auth::user()->id === $user_instance->user_id)
                                        <div class="flex mt-1">
                                            <input type="radio" name="user_instance_id" id="new_relasi_transaksi-user_instance_id-{{ $key }}" value="{{ $user_instance->id }}">
                                            <label for="new_relasi_transaksi-user_instance_id-{{ $key }}" class="ml-1">{{ $user_instance->instance_type }} - {{ $user_instance->instance_name }} - {{ $user_instance->branch }} - {{ $user_instance->account_number }}</label>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    <div class="ml-2">
                                        <div class="flex">
                                            <div class="ml-1">
                                                <label>Tipe:</label>
                                                <div>
                                                    <select name="type" id="new_relasi_transaksi-type" class="rounded py-1 text-xs">
                                                        <option value="UANG MASUK">UANG MASUK</option>
                                                        <option value="UANG KELUAR">UANG KELUAR</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ml-1">
                                                <label>Deskripsi:</label>
                                                <div>
                                                    <input type="text" name="desc" id="new_relasi_transaksi-desc" class="border rounded p-1 text-xs w-60">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex mt-1">
                                            <div class="ml-1">
                                                <label>Kategori lvl.1:</label>
                                                <div>
                                                    <input type="text" name="kategori_level_one" id="new_relasi_transaksi-kategori_level_one" class="border rounded p-1 text-xs">
                                                </div>
                                                {{-- <div id="new-pilihan_kategori_level_one"></div> --}}
                                            </div>
                                            <div class="ml-1">
                                                <label>Kategori lvl.2:</label>
                                                <div>
                                                    <input type="text" name="kategori_level_two" id="new_relasi_transaksi-kategori_level_two" class="border rounded p-1 text-xs">
                                                </div>
                                                {{-- <div id="new-pilihan_kategori_level_two"></div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex border rounded p-1 border-sky-300 mt-3">
                                    <div>
                                        <label>Related User Instance:</label>
                                        <div class="flex mt-1">
                                            <input type="radio" name="related_user_instance_id" id="new_relasi_transaksi-related_user_instance_id-none" value="" checked>
                                            <label for="new_relasi_transaksi-related_user_instance_id-none" class="ml-1">none</label>
                                        </div>
                                        @foreach ($user_instances as $key => $user_instance)
                                        <div class="flex mt-1">
                                            <input type="radio" name="related_user_instance_id" id="new_relasi_transaksi-related_user_instance_id-{{ $key }}" value="{{ $user_instance->id }}">
                                            <label for="new_relasi_transaksi-related_user_instance_id-{{ $key }}" class="ml-1">{{ $user_instance->instance_type }} - {{ $user_instance->instance_name }} - {{ $user_instance->branch }} - {{ $user_instance->account_number }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="ml-2">
                                        <div class="flex mt-1">
                                            <div>
                                                <label>related_user:</label>
                                                <div>
                                                    <select name="related_user_id" id="new_relasi_transaksi-related_user" class="text-xs rounded py-1">
                                                        <option value="">-</option>
                                                        @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ml-1">
                                                <label>supplier:</label>
                                                <div>
                                                    <input type="text" name="supplier_nama" id="new_relasi_transaksi-supplier" class="border rounded p-1 text-xs">
                                                    <input type="hidden" name="supplier_id" id="new_relasi_transaksi-supplier_id">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <label>pelanggan:</label>
                                            <div>
                                                <input type="text" name="pelanggan_nama" id="new_relasi_transaksi-pelanggan" class="border rounded p-1 text-xs">
                                                <input type="hidden" name="pelanggan_id" id="new_relasi_transaksi-pelanggan_id">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex mt-3 justify-center">
                                    <div>
                                        <button type="submit" class="ml-2 flex items-center bg-emerald-500 text-white py-1 px-3 rounded hover:bg-emerald-700">
                                            <span class="ml-1">+ Tambah Relasi Transaksi</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- END - FORM NEW RELASI TRANSAKSI --}}

                <table class="table-slim table-border min-w-content max-w-full mt-2">
                    <tr><th>Deskripsi/Keterangan</th><th>Kategori lvl.1</th><th>Kategori lvl.2</th><th>Info Lain</th></tr>
                    @foreach ($transaction_names as $key => $kategori_types)
                    <tr class="bg-violet-300">
                        <td colspan="4">
                            <div class="font-semibold">
                                {{ $user_instances[$key]->username }} - {{ $user_instances[$key]->instance_type }} - {{ $user_instances[$key]->instance_name }} - {{ $user_instances[$key]->branch }} - {{ $user_instances[$key]->account_number }} - {{ $user_instances[$key]->kode }}
                            </div>
                        </td>
                    </tr>
                    @foreach ($kategori_types as $key_kategori => $tr_names)
                    @if (count($tr_names) !== 0)
                    @if ($key_kategori === 0)
                    <tr>
                        <td colspan="2">
                            <div class="bg-pink-200 font-semibold p-1 rounded">
                                {{ $tr_names[0]->kategori_type }}
                            </div>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2">
                            <div class="bg-emerald-200 font-semibold p-1 rounded">
                                {{ $tr_names[0]->kategori_type }}
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endif
                    @foreach ($tr_names as $tr_name)
                    <tr>
                        {{-- <td>{{ $tr_name->username }}</td> --}}
                        <td>{{ $tr_name->desc }}</td>
                        {{-- <td>{{ $tr_name->kategori_type }}</td> --}}
                        <td>{{ $tr_name->kategori_level_one }}</td>
                        <td>{{ $tr_name->kategori_level_two }}</td>
                        <td>
                            @if ($tr_name->pelanggan_id !== null)
                            Pelanggan: {{ $tr_name->pelanggan_nama }}
                            @elseif ($tr_name->related_user_id !== null)
                            <div>Terkait dengan:</div>
                            <div>{{ $tr_name->related_username }} - {{ $tr_name->related_user_instance_type }} - {{ $tr_name->related_user_instance_name }} - {{ $tr_name->related_user_instance_branch }}</div>
                            <div>{{ $tr_name->related_desc }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                    @endforeach
                </table>

            </div>
        </div>
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
    const label_suppliers = {!! json_encode($label_suppliers, JSON_HEX_TAG) !!};
    const label_pelanggans = {!! json_encode($label_pelanggans, JSON_HEX_TAG) !!};
    const kategoris = {!! json_encode($kategoris, JSON_HEX_TAG) !!};
    const label_kategori_level_one = {!! json_encode($label_kategori_level_one, JSON_HEX_TAG) !!};

    $('#new_relasi_transaksi-pelanggan').autocomplete({
        source: label_pelanggans,
        select: function (event, ui) {
            // console.log(ui.item);
            document.getElementById('new_relasi_transaksi-pelanggan_id').value = ui.item.id;
            document.getElementById('new_relasi_transaksi-pelanggan').value = ui.item.value;
        }
    });

    $('#new_relasi_transaksi-supplier').autocomplete({
        source: label_suppliers,
        select: function (event, ui) {
            // console.log(ui.item);
            document.getElementById('new_relasi_transaksi-supplier_id').value = ui.item.id;
            document.getElementById('new_relasi_transaksi-supplier').value = ui.item.value;
        }
    });

    // function autocomplete_kategori_level_one(value) {
    //     console.log(value);
    //     var html_pilihan_kategori_level_one = ``;
    // }

    // const filterItems = (needle, heystack) => {
    //     let query = needle.toLowerCase();
    //     return heystack.filter(item => item.toLowerCase().indexOf(query) >= 0);
    // }

    console.log(kategoris);
    let label_kategori_level_two = new Array();

    $('#new_relasi_transaksi-kategori_level_one').autocomplete({
        source: label_kategori_level_one,
        select: function (event, ui) {
            console.log(ui.item);
            // document.getElementById('new_relasi_transaksi-kategori_level_one').value = ui.item.id;
            document.getElementById('new_relasi_transaksi-kategori_level_one').value = ui.item.value;
            let index_kategoris = new Array();
            for (let i = 0; i < kategoris.length; i++) {
                if (kategoris[i].kategori_level_one === ui.item.value) {
                    index_kategoris.push(i);
                }
            }
            console.log(index_kategoris);

            label_kategori_level_two = new Array();
            for (let j = 0; j < index_kategoris.length; j++) {
                if (kategoris[index_kategoris[j]].kategori_level_two !== null) {
                    label_kategori_level_two.push({
                        'label': kategoris[index_kategoris[j]].kategori_level_two,
                        'value': kategoris[index_kategoris[j]].kategori_level_two,
                    });
                }
            }
            set_autocomplete_kategori_level_two();
        }
    });

    function set_autocomplete_kategori_level_two() {
        console.log(label_kategori_level_two);
        $('#new_relasi_transaksi-kategori_level_two').autocomplete({
        source: label_kategori_level_two,
        select: function (event, ui) {
            console.log(ui.item);
            // document.getElementById('new_relasi_transaksi-kategori_level_one').value = ui.item.id;
            document.getElementById('new_relasi_transaksi-kategori_level_two').value = ui.item.value;
        }
    });
    }
</script>

@endsection
