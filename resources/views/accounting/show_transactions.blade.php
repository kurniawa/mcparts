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
    <div class="mx-1 py-1 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-xl font-bold">Data Transaksi - <span class="text-slate-500">{{ $user->username }}</span></h1>

            <div class="border rounded p-1 inline-block">
                <table class="text-xs">
                    <tr><td>Tipe Instansi</td><td>:</td><td><div class="ml-2">{{ $user_instance->instance_type }}</div></td></tr>
                    <tr><td>Nama Instansi</td><td>:</td><td><div class="ml-2">{{ $user_instance->instance_name }}</div></td></tr>
                    <tr><td>Branch</td><td>:</td><td><div class="ml-2">{{ $user_instance->branch }}</div></td></tr>
                    <tr><td>Nomor Rek.</td><td>:</td><td><div class="ml-2">{{ $user_instance->account_number }}</div></td></tr>
                </table>
            </div>
            {{-- <div>
                <button type="submit" id="btn_new_kas" class="border font-semibold rounded text-violet-500 border-violet-300 px-1 ml-2" onclick="toggle_light(this.id, 'form_new_kas', [], ['bg-violet-200'], 'flex')">+ NEW KAS</button>
            </div> --}}
        </div>

        {{-- TRANSACTIONS --}}
        <div class="mt-2">
            <table class="text-xs w-full">
                <tr>
                    <th></th><th></th><th></th>
                    <th>
                        <div class="flex justify-between bg-yellow-300">
                            <span>Rp</span>
                            <span>{{ number_format($keluar_total,0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </th>
                    <th>
                        <div class="flex justify-between bg-yellow-300">
                            <span>Rp</span>
                            <span>{{ number_format($masuk_total,0,',','.') }}</span>
                            <span> ,-</span>
                        </div>
                    </th>
                    <th>
                        <div class="flex justify-between bg-emerald-300">
                            <span>Rp</span>
                            @if (count($accountings) !== 0)
                            <span>{{ number_format($accountings[count($accountings) - 1],0,',','.') }}</span>
                            @else
                            <span>0</span>
                            @endif
                            <span> ,-</span>
                        </div>
                    </th>
                </tr>
                <tr class="bg-blue-500 text-white"><th>TANGGAL</th><th>KODE</th><th>KETERANGAN</th><th>KELUAR</th><th>MASUK</th><th>SALDO</th></tr>
                @foreach ($accountings as $accounting)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($accounting->created_at)) }}</td>
                    <td>{{ $accounting->kode }}</td>
                    <td>{{ $accounting->transaction_name }}</td>
                    <td>
                        @if ($accounting->transaction_type === 'pengeluaran')
                        {{ number_format($accounting->jumlah,0,',','.') }}
                        @endif
                    </td>
                    <td>
                        @if ($accounting->transaction_type === 'pemasukan')
                        {{ number_format($accounting->jumlah,0,',','.') }}
                        @endif
                    </td>
                    <td>{{ number_format($accounting->saldo,0,',','.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        {{-- END - TRANSACTIONS --}}

        {{-- STORE NEW TRANSACTIONS --}}
        <div class="border rounded p-1 mt-3">
            <h2 class="font-bold">Tambah Transaksi :</h2>
            <form action="{{ route('accounting.store_transactions') }}" method="POST" class="mt-1 inline-block min-w-max">
                @csrf
                <table class="text-xs min-w-max" id="table_add_transactions">
                    <tr class="text-slate-600"><th>tanggal</th><th>kode</th><th>keterangan</th><th>keterangan tambahan</th><th>keluar</th><th>masuk</th><th>saldo</th><th>related_user</th><th>keterangan u. related_user</th></tr>
                    @for ($i = 0; $i < 7; $i++)
                    <tr>
                        <td><input type="text" name="created_at[]" id="created_at-{{ $i }}" class="border p-1 text-xs w-28"></td>
                        <td><input type="text" name="kode[]" id="kode-{{ $i }}" class="border p-1 text-xs w-16"></td>
                        <td><input type="text" name="transaction_name[]" id="transaction_name-{{ $i }}" class="border p-1 text-xs w-60"></td>
                        <td><input type="text" name="keterangan[]" id="keterangan-{{ $i }}" class="border p-1 text-xs w-full"></td>
                        <td><input type="text" name="keluar[]" id="keluar-{{ $i }}" class="border p-1 text-xs w-36"></td>
                        <td><input type="text" name="masuk[]" id="masuk-{{ $i }}" class="border p-1 text-xs w-36"></td>
                        <td><input type="text" name="saldo[]" id="saldo-{{ $i }}" class="border p-1 text-xs w-36"></td>
                        <td>
                            <select name="related_user_id[]" id="related_user_id-{{ $i }}" class="text-xs py-1">
                                <option value="">-</option>
                                @foreach ($related_users as $related_user)
                                <option value="{{ $related_user->id }}">{{ $related_user->username }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="transaction_name-related_user[]" id="transaction_name-related_user-{{ $i }}" class="border p-1 text-xs w-60"></td>
                    </tr>
                    @endfor
                    <tr id="tr_add_transaction">
                        <td>
                            <button type="button" class="rounded bg-emerald-200 text-emerald-600" onclick="add_transaction('tr_add_transaction','table_add_transactions')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </table>
                <div class="mt-3 text-xs border rounded p-1 inline-block border-yellow-500">
                    <p>*) Keterangan Tambahan akan tertulis dalam tanda kurung pada laporan.</p>
                </div>
                <div class="mt-3 text-center text-xs">
                    <button type="submit" class="border-2 font-semibold rounded text-emerald-500 border-emerald-300 bg-emerald-200 px-2">CONFIRM</button>
                </div>
            </form>
        </div>
        {{-- END - STORE NEW TRANSACTIONS --}}

    </div>
</main>

<script>
    const related_users = {!! json_encode($related_users, JSON_HEX_TAG) !!};

    let html_option_related_users = '<option value="">-</option>';
    related_users.forEach(related_user => {
        html_option_related_users += `<option value="${related_user.id}">${related_user.username}</option>`;
    });

    console.log(html_option_related_users);

    let transaction_index = 7;
    function add_transaction(tr_id, parent_id) {
        document.getElementById(tr_id).remove();
        let parent = document.getElementById(parent_id);
        parent.insertAdjacentHTML('beforeend',
        `
        <tr>
            <td><input type="text" name="created_at[]" id="created_at-${transaction_index}" class="border p-1 text-xs w-28"></td>
            <td><input type="text" name="kode[]" id="kode-${transaction_index}" class="border p-1 text-xs w-16"></td>
            <td><input type="text" name="transaction_name[]" id="transaction_name-${transaction_index}" class="border p-1 text-xs w-72"></td>
            <td><input type="text" name="keluar[]" id="keluar-${transaction_index}" class="border p-1 text-xs w-36"></td>
            <td><input type="text" name="masuk[]" id="masuk-${transaction_index}" class="border p-1 text-xs w-36"></td>
            <td><input type="text" name="saldo[]" id="saldo-${transaction_index}" class="border p-1 text-xs w-36"></td>
            <td>
                <select name="related_user_id[]" id="related_user_id-${transaction_index}" class="text-xs py-1">
                    ${html_option_related_users}
                </select>
            </td>
            <td><input type="text" name="transaction_name-related_user[]" id="transaction_name-related_user-${transaction_index}" class="border p-1 text-xs w-60"></td>
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
            transaction_index++;
        }, 100);
    }

    function table_to_excel(table_id) {
        $(`#${table_id}`).table2excel({
            filename:`${table_id}.xls`
        });
    }
</script>

@endsection
