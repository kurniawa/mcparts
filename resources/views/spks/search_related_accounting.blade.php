@extends('layouts.main')
@section('content')
  <main>
      <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <x-errors-any></x-errors-any>
        <x-validation-feedback></x-validation-feedback>
        <div>
            <h1>Search Related Accounting</h1>
        </div>
        <div class="border-t-4 pt-2">
            <div class="grid grid-cols-2">
                <table class="w-full">
                    <tr>
                        <td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $nota['no_nota'] }}</div></td>
                    </tr>
                    <tr>
                        <td>Tgl.</td><td>:</td>
                        <td>
                            <div class="w-fit">
                                <div class="flex">
                                    <div class="flex">
                                        @if ($nota['finished_at'] === null)
                                        <div>
                                            <div class="rounded p-1 bg-red-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($nota['created_at'])) }}</div>
                                                <div>{{ date('m-y',strtotime($nota['created_at'])) }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <div>
                                            <div class="rounded p-1 bg-yellow-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($nota['created_at'])) }}</div>
                                                <div>{{ date('m-y',strtotime($nota['created_at'])) }}</div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex ml-1 items-center">
                                        @if ($nota['finished_at'] !== null)
                                        <div>
                                            <div class="rounded p-1 bg-emerald-500 text-white font-bold text-center">
                                                <div>{{ date('d',strtotime($nota['finished_at'])) }}</div>
                                                <div>{{ date('m-y',strtotime($nota['finished_at'])) }}</div>
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
                </table>
                <table>
                    <tr>
                        <td class="align-top">Alamat</td><td class="align-top">:</td>
                        <td class="align-top">
                            @if ($nota['cust_long']!==null)
                            @foreach (json_decode($nota['cust_long'],true) as $long)
                            <div>{{ $long }}</div>
                            @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Kontak</td><td>:</td>
                        <td>
                            @if (isset($cust_kontaks) && $cust_kontaks[$key_nota]!==null)
                            {{ $cust_kontaks[$key_nota] }}
                            @else-@endif
                        </td>
                    </tr>
                </table>
            </div>


            {{-- Nota Items --}}
            <div class="border rounded px-1 py-2" id="nota-items">
                <table class="w-full text-xs">
                    <tr>
                        <th>Jml.</th>
                        <th>
                            <div class="flex items-center justify-center">
                                <span>Nama Barang</span>
                                <button type="button" id="spk_produk_nota_detail_button" class="ml-1 border rounded border-yellow-500 text-yellow-500 p-1" onclick="toggle_detail_classes(this.id,'spk_produk_nota_detail')">D</button>
                            </div>
                        </th>
                        <th>Hrg.</th><th>Hrg. t</th>
                    </tr>
                    <tr><td><div class="text-center">---</div></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td><td><div class="text-center">---</div></td></tr>
                    @if (isset($col_spk_produk_notas))
                    @foreach ($col_spk_produk_notas[$key_nota] as $key_spk_produk_nota => $spk_produk_nota)
                    <tr>
                        <td><div class="text-center">{{ $spk_produk_nota->jumlah }}</div></td>
                        <td>
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
                            <div class="text-center">{{ number_format($spk_produk_nota->harga,0,',','.') }}</div>
                        </td>
                        <td><div class="text-center">{{ number_format($spk_produk_nota->harga_t,0,',','.') }}</div></td>
                    </tr>
                </table>
                @endforeach
                @endif
            </div>
            {{-- END - Nota Items --}}
        </div>
    </div>
  </main>

<script>
    
</script>
@endsection
