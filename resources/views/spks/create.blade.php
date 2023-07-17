@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">+ New SPK</h1>
    </div>
  </header>
  <main class="mx-1 max-w-7xl py-1 sm:px-6 lg:px-8 text-xs">
    <div class="flex justify-center">
        <form action="{{ route('spks.store') }}" method="POST">
            @csrf
            <div class="border rounded p-2">
                <div class="border-b pb-3">
                    <table>
                        <tr>
                            <td>Tanggal</td><td><div class="mx-2">:</div></td>
                            <td class="py-1">
                                <div class="flex">
                                    <select name="day" id="day" class="rounded text-xs">
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
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Untuk</td><td><div class="mx-2">:</div></td>
                            <td class="py-1">
                                <input type="text" name="pelanggan_nama" id="pelanggan_nama" placeholder="nama pelanggan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                                <input type="hidden" name="reseller_id" id="reseller_id">
                            </td>
                        </tr>
                        <tr>
                            <td>Ket. (opt.)</td><td><div class="mx-2">:</div></td>
                            <td class="py-1"><input type="text" name="judul" placeholder="judul/keterangan..." class="text-xs rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></td>
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
                <button type="submit" class="border-2 border-emerald-300 bg-emerald-200 text-emerald-600 rounded-lg font-semibold py-1 px-3 hover:bg-emerald-300">Proses/Konfirmasi</button>
            </div>
        </form>
    </div>
    <x-errors-any></x-errors-any>
    <x-validation-feedback></x-validation-feedback>
  </main>
</div>

<script>
    const label_pelanggans = {!! json_encode($label_pelanggans, JSON_HEX_TAG) !!}
    // console.log('pelanggans', pelanggans);
    // // const pelanggan_resellers = {-!! json_encode($pelanggan_resellers, JSON_HEX_TAG) !!}

    // if (show_console) {
    //     console.log("label_pelanggans");
    //     console.log(label_pelanggans);
    //     // console.log("pelanggan_resellers");
    //     // console.log(pelanggan_resellers);
    // }

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
        `<tr>
            <td>
                <div class="flex items-center mt-1">
                    <button id="toggle_produk_keterangan-${index_spk_item}" type="button" class="border border-yellow-500 rounded text-yellow-500" onclick="toggleButton(this.id,'produk_keterangan-${index_spk_item}',['bg-yellow-300'],null)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                    <input type="text" name="produk_nama[]" id="produk_nama-${index_spk_item}" class="border-slate-300 rounded-lg text-xs p-1 ml-1 placeholder:text-slate-400" placeholder="nama item...">
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

    function toggleButton(button_id, content_id, classes_add, classes_remove) {
        // console.log(classes_add);
        $(`#${content_id}`).toggle(350);
        setTimeout(() => {
            // console.log($(`#${content_id}`).css('display'));
            // console.log(classes_add, classes_add.length);
            // console.log(classes_remove, classes_remove.length);
            let display = $(`#${content_id}`).css('display');
            let button = document.getElementById(button_id)
            if (display === 'inline-block' || display === 'block') {
                if (classes_remove !== null) {
                    if (classes_remove.length !== 0) {
                        classes_remove.forEach(item => {
                            button.classList.remove(item);
                        });
                    }
                }
                if (classes_add !== null) {
                    if (classes_add.length !== 0) {
                        classes_add.forEach(item => {
                            button.classList.add(item);
                        })
                    }
                }
            } else {
                if (classes_add !== null) {
                    if (classes_add.length !== 0) {
                        classes_add.forEach(item => {
                            button.classList.remove(item);
                        })
                    }
                }
                if (classes_remove !== null) {
                    if (classes_remove.length !== 0) {
                        classes_remove.forEach(item => {
                            button.classList.add(item);
                        });
                    }
                }
            }
        }, 500);
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
</script>
@endsection
