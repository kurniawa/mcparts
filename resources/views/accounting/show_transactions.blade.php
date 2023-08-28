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
            <form action="{{ route('accounting.store_transactions', $user_instance->id) }}" method="POST" class="mt-1 inline-block min-w-max">
                @csrf
                <table class="text-xs min-w-max" id="table_add_transactions">
                    <tr class="text-slate-600"><th>tanggal</th><th>kode</th><th>deskripsi/keterangan</th><th>keterangan tambahan</th><th>keluar</th><th>masuk</th><th>saldo</th></tr>
                    @for ($i = 0; $i < 7; $i++)
                    <tr>
                        <td><input type="text" name="created_at[]" id="created_at-{{ $i }}" class="border p-1 text-xs mt-2 w-28"></td>
                        <td><input type="text" name="kode[]" id="kode-{{ $i }}" class="border p-1 text-xs mt-2 w-16"></td>
                        <td><input type="text" name="transaction_desc[]" id="transaction_desc-{{ $i }}" class="border p-1 text-xs mt-2 w-60"></td>
                        <td><input type="text" name="keterangan[]" id="keterangan-{{ $i }}" class="border p-1 text-xs mt-2 w-full"></td>
                        <td><input type="text" name="keluar[]" id="keluar-{{ $i }}" class="border p-1 text-xs mt-2 w-36"></td>
                        <td><input type="text" name="masuk[]" id="masuk-{{ $i }}" class="border p-1 text-xs mt-2 w-36"></td>
                        <td>
                            <input type="text" name="saldo[]" id="saldo-{{ $i }}" class="border p-1 text-xs mt-2 w-36">
                            <input type="hidden" name="kategori_type[]" id="new_transaction-kategori_type-{{ $i }}">
                            <input type="hidden" name="kategori_level_one[]" id="new_transaction-kategori_level_one-{{ $i }}">
                            <input type="hidden" name="kategori_level_two[]" id="new_transaction-kategori_level_two-{{ $i }}">
                            <input type="hidden" name="related_user_id[]" id="new_transaction-related_user_id-{{ $i }}">
                            <input type="hidden" name="pelanggan_nama[]" id="new_transaction-pelanggan_nama-{{ $i }}">
                            <input type="hidden" name="pelanggan_id[]" id="new_transaction-pelanggan_id-{{ $i }}">
                            <input type="hidden" name="related_desc[]" id="new_transaction-related_desc-{{ $i }}">
                            <input type="hidden" name="related_user_instance_type[]" id="new_transaction-related_user_instance_type-{{ $i }}">
                            <input type="hidden" name="related_user_instance_id[]" id="new_transaction-related_user_instance_id-{{ $i }}">
                            <input type="hidden" name="related_user_instance_name[]" id="new_transaction-related_user_instance_name-{{ $i }}">
                            <input type="hidden" name="related_user_instance_branch[]" id="new_transaction-related_user_instance_branch-{{ $i }}">
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
                <div class="mt-3 text-xs border rounded p-1 inline-block border-yellow-500">
                    <p>*) Keterangan Tambahan akan tertulis dalam tanda kurung pada ringkasan/laporan.</p>
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
    const label_deskripsi = {!! json_encode($label_deskripsi, JSON_HEX_TAG) !!};

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
            <td><input type="text" name="created_at[]" id="created_at-${transaction_index}" class="border p-1 text-xs mt-2 w-28"></td>
            <td><input type="text" name="kode[]" id="kode-${transaction_index}" class="border p-1 text-xs mt-2 w-16"></td>
            <td><input type="text" name="transaction_desc[]" id="transaction_desc-${transaction_index}" class="border p-1 text-xs mt-2 w-60"></td>
            <td><input type="text" name="keterangan[]" id="keterangan-${transaction_index}" class="border p-1 text-xs mt-2 w-full"></td>
            <td><input type="text" name="keluar[]" id="keluar-${transaction_index}" class="border p-1 text-xs mt-2 w-36"></td>
            <td><input type="text" name="masuk[]" id="masuk-${transaction_index}" class="border p-1 text-xs mt-2 w-36"></td>
            <td>
                <input type="text" name="saldo[]" id="saldo-${transaction_index}" class="border p-1 text-xs mt-2 w-36">
                <input type="hidden" name="kategori_type[]" id="new_transaction-kategori_type-${transaction_index}">
                <input type="hidden" name="kategori_level_one[]" id="new_transaction-kategori_level_one-${transaction_index}">
                <input type="hidden" name="kategori_level_two[]" id="new_transaction-kategori_level_two-${transaction_index}">
                <input type="hidden" name="related_user_id[]" id="new_transaction-related_user_id-${transaction_index}">
                <input type="hidden" name="pelanggan_nama[]" id="new_transaction-pelanggan_nama-${transaction_index}">
                <input type="hidden" name="pelanggan_id[]" id="new_transaction-pelanggan_id-${transaction_index}">
                <input type="hidden" name="related_desc[]" id="new_transaction-related_desc-${transaction_index}">
                <input type="hidden" name="related_user_instance_type[]" id="new_transaction-related_user_instance_type-${transaction_index}">
                <input type="hidden" name="related_user_instance_id[]" id="new_transaction-related_user_instance_id-${transaction_index}">
                <input type="hidden" name="related_user_instance_name[]" id="new_transaction-related_user_instance_name-${transaction_index}">
                <input type="hidden" name="related_user_instance_branch[]" id="new_transaction-related_user_instance_branch-${transaction_index}">
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
                autofill_transaction(index, ui.item.value);
            }
        });
    }

    const transaction_names = {!! json_encode($transaction_names, JSON_HEX_TAG) !!};

    function autofill_transaction(index, desc) {
        // console.log(index, desc);
        let res = transaction_names.find(o => o.desc === desc);
        console.log(res);
        let related_user_instance_id = document.getElementById(`new_transaction-related_user_instance_id-${index}`);
        document.getElementById(`new_transaction-kategori_type-${index}`).value = res.kategori_type;
        document.getElementById(`new_transaction-kategori_level_one-${index}`).value = res.kategori_level_one;
        document.getElementById(`new_transaction-kategori_level_two-${index}`).value = res.kategori_level_two;
        document.getElementById(`new_transaction-related_desc-${index}`).value = res.related_desc;
        document.getElementById(`new_transaction-related_user_id-${index}`).value = res.related_user_id;
        document.getElementById(`new_transaction-pelanggan_id-${index}`).value = res.pelanggan_id;
        document.getElementById(`new_transaction-pelanggan_nama-${index}`).value = res.pelanggan_nama;
        related_user_instance_id.value = res.related_user_instance_id;
        document.getElementById(`new_transaction-related_user_instance_type-${index}`).value = res.related_user_instance_type;
        document.getElementById(`new_transaction-related_user_instance_name-${index}`).value = res.related_user_instance_name;
        document.getElementById(`new_transaction-related_user_instance_branch-${index}`).value = res.related_user_instance_branch;
        // console.log(related_user_instance_id)
        // console.log(related_user_instance_id.value)
    }

    function table_to_excel(table_id) {
        $(`#${table_id}`).table2excel({
            filename:`${table_id}.xls`
        });
    }
</script>

@endsection
