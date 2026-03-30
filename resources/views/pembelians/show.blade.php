@extends('layouts.main')
@section('content')
  
<main class="p-2">
    <div>
        <h1 class="font-bold text-xl">Pembelian</h1>
        <p>Nota: <span class="font-bold">{{ $pembelian->nomor_nota }}</span></p>
        <p>Supplier: <span class="font-bold">{{ $pembelian->supplier_nama }}</span></p>
        <div class="flex items-center gap-2">
            <span>Tanggal:</span>
            <div class="flex items-center gap-2">
                <div class="flex">
                    @if ($pembelian->tanggal_lunas === null)
                    <div>
                        <div class="rounded p-1 bg-pink-200 text-pink-500 font-bold text-center">
                            <div class="min-w-max">{{ date('d',strtotime($pembelian->created_at)) }}</div>
                            <div class="min-w-max">{{ date('m-y',strtotime($pembelian->created_at)) }}</div>
                        </div>
                    </div>
                    @else
                    <div>
                        <div class="rounded p-1 bg-sky-200 text-sky-500 font-bold text-center">
                            <div class="min-w-max">{{ date('d',strtotime($pembelian->created_at)) }}</div>
                            <div class="min-w-max">{{ date('m-y',strtotime($pembelian->created_at)) }}</div>
                        </div>
                    </div>
                    @endif
                </div>
                <p>-</p>
                <div class="flex items-center">
                    @if ($pembelian->tanggal_lunas !== null)
                    <div>
                        <div class="rounded p-1 bg-emerald-200 text-emerald-500 font-bold text-center">
                            <div class="min-w-max">{{ date('d',strtotime($pembelian->tanggal_lunas)) }}</div>
                            <div class="min-w-max">{{ date('m-y',strtotime($pembelian->tanggal_lunas)) }}</div>
                        </div>
                    </div>
                    @else
                    <span class="font-bold">--</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <hr class="mt-2 border-b-4 border-slate-400">
    <hr class="mt-1 border-slate-400">

    <x-validation-feedback></x-validation-feedback>

    <div class="grid grid-cols-2 gap-2">
        <table class="table-nice text-xs">
            <thead>
                <tr><th>No.</th><th>Nama Barang</th><th>Jumlah</th><th>Harga/Satuan</th><th>Harga Total</th></tr>
            </thead>
            <tbody>
                @foreach ($pembelian->pembelianBarangs as $key => $pembelian_barang)
                <tr>
                    <td>{{ $key + 1 }}.</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span>{{ $pembelian_barang->barang_nama }}</span>
                            {{-- <button type="button" class="button-toggle-change-pembelian-barang text-slate-400" value="{{ $key }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </button> --}}
                        </div>
                    </td>
                    <td>{{ $pembelian_barang->jumlah_sub }} {{ $pembelian_barang->satuan_sub }}&#64;{{ $pembelian_barang->jumlah_main /100 }} {{ $pembelian_barang->satuan_main }}</td>
                    <td>{{ number_format($pembelian_barang->harga_main, 2, ',', '.') }}/{{ $pembelian_barang->satuan_main }}</td>
                    <td>{{ number_format($pembelian_barang->harga_t, 2, ',', '.') }}</td>
                </tr>
                {{-- <tr>
                    <td colspan="5" class="hidden" id="tr-change-pembelian-barang-{{ $key }}">
                        <form action="{{ route('pembelians.changePembelianBarang', $pembelian_barang->id) }}" method="post" id="form-change-pembelian-barang">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="barang_nama" id="input-change-pembelian-barang-nama-{{ $key }}" class="input-change-pembelian-barang rounded border-slate-300" required>
                                <input type="hidden" name="barang_id" id="input-change-pembelian-barang-id-{{ $key }}" required>
                                <button type="submit" class="bg-emerald-200 text-emerald-500 py-1 px-2 rounded">confirm</button>
                            </div>
                        </form>
                    </td>
                </tr> --}}
                @endforeach
                <tr><th></th><th></th><th></th><th>Grand Total</th><th>{{ number_format($pembelian->harga_total, 2, ',', '.') }}</th></tr>
            </tbody>
        </table>
        <x-history-pembayaran :nota="$pembelian" :key_nota="$pembelian->id"></x-history-pembayaran>
    </div>
    {{-- <div class="text-xs flex justify-center italic mt-2">Fitur sementara: mengganti barang yang sudah tercantum pada pembelian</div> --}}
</main>      

<script>
    const label_barang = {!! json_encode($label_barang, JSON_HEX_TAG) !!}

    document.querySelectorAll('.input-change-pembelian-barang').forEach((element, index) => {
        $(`#${element.id}`).autocomplete({
            source: label_barang,
            select: function (event, ui) {
                // $(`#input-change-pembelian-barang-nama-${index}`).value = ui.item.label;
                $(`#input-change-pembelian-barang-id-${index}`).val(ui.item.id);
                // console.log($(`#input-change-pembelian-barang-id-${index}`));
                // console.log($(`#input-change-pembelian-barang-id-${index}`).val());
            }
        });
    });

    document.getElementById('form-change-pembelian-barang').addEventListener('submit', function (e) {
        e.preventDefault();
        let confirmation = confirm('Hati-hati! Hanya akan mengganti nama barang saja, tidak mengganti jumlah dan harga atau data lainnya. Ini hanya fitur sementara! Lanjutkan?');
        if (confirmation) {
            e.target.submit();
        }
    });

    document.querySelectorAll('.button-toggle-change-pembelian-barang').forEach(element => {
        element.addEventListener('click', (e) => {
            $(`#tr-change-pembelian-barang-${element.value}`).toggle(300);
        })
    });
</script>
@endsection
{{-- <a href="https://www.flaticon.com/free-icons/fox" title="fox icons">Fox icons created by Freepik - Flaticon</a> --}}
{{-- cat --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Freepik - Flaticon</a> --}}
{{-- Honey Badger --}}
{{-- <a href="https://www.flaticon.com/free-icons/badger" title="badger icons">Badger icons created by Freepik - Flaticon</a> --}}
{{-- Panda --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Smashicons - Flaticon</a> --}}
