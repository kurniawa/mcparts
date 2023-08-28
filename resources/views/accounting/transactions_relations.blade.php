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
                    <button type="submit" class="border rounded border-emerald-300 text-emerald-500 font-semibold px-3 py-1 ml-1" id="btn_new_pembelian" onclick="toggle_light(this.id, 'form_new_pembelian', [], ['bg-emerald-200'], 'block')">+ Pembelian</button>
                    <button type="submit" class="border rounded border-indigo-300 text-indigo-500 font-semibold px-3 py-1 ml-1" id="btn_new_barang" onclick="toggle_light(this.id, 'form_new_barang', [], ['bg-indigo-200'], 'block')">+ Barang</button>
                </div>
                {{-- SEARCH / FILTER --}}
                <div class="hidden" id="filter-content">
                    <div class="rounded p-2 bg-white shadow drop-shadow inline-block mt-1">
                        <form action="" method="GET">
                            <div class="flex">
                                <div>
                                    <label>Username:</label>
                                    <div>
                                        <select name="user_id" id="filter-user_id" class="rounded py-1 text-xs">
                                            @foreach ($users as $user_ava)
                                            <option value="{{ $user_ava->id }}">{{ $user_ava->username }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="ml-1">
                                    <label>Tipe:</label>
                                    <div>
                                        <select name="type" id="filter-type" class="rounded py-1 text-xs">
                                            <option value="UANG MASUK">UANG MASUK</option>
                                            <option value="UANG KELUAR">UANG KELUAR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="ml-1">
                                    <label>Kategori ke-1:</label>
                                    <div>
                                        <input type="text" name="kategori_level_one" id="filter-kategori_level_one" class="border rounded p-1 text-xs">
                                    </div>
                                </div>
                                <div class="ml-1">
                                    <label>Kategori ke-2:</label>
                                    <div>
                                        <input type="text" name="kategori_level_two" id="filter-kategori_level_two" class="border rounded p-1 text-xs">
                                    </div>
                                </div>
                            </div>
                            <div class="flex mt-1 justify-between items-end">
                                <div>
                                    <label>Deskripsi:</label>
                                    <div>
                                        <input type="text" name="desc" id="filter-desc" class="border rounded p-1 text-xs w-60">
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="ml-2 flex items-center bg-orange-500 text-white py-1 px-3 rounded hover:bg-orange-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                        <span class="ml-1">Search</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- END - SEARCH / FILTER --}}

                <table class="table-slim w-full mt-2">
                    <tr><th>User</th><th>Deskripsi/Keterangan</th><th>Tipe</th><th>Kategori ke-1</th><th>Kategori ke-2</th><th>Info Lain</th></tr>
                    @foreach ($transaction_names as $transaction_name)
                    <tr>
                        <td>{{ $transaction_name->username }}</td>
                        <td>{{ $transaction_name->desc }}</td>
                        <td>{{ $transaction_name->kategori_type }}</td>
                        <td>{{ $transaction_name->kategori_level_one }}</td>
                        <td>{{ $transaction_name->kategori_level_two }}</td>
                        <td>
                            @if ($transaction_name->pelanggan_id !== null)
                            Pelanggan: {{ $transaction_name->pelanggan_nama }}
                            @elseif ($transaction_name->related_user_id !== null)
                            <div>Terkait dengan:</div>
                            <div>{{ $transaction_name->related_username }} - {{ $transaction_name->related_user_instance_type }} - {{ $transaction_name->related_user_instance_name }} - {{ $transaction_name->related_user_instance_branch }}</div>
                            <div>{{ $transaction_name->related_desc }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>

            </div>
        </div>
    </div>
</main>

<script>

</script>

@endsection
