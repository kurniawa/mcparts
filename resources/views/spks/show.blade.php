@extends('layouts.main')
@section('content')
  <main>
      <x-errors-any></x-errors-any>
    <div class="mx-1 py-1 sm:px-6 lg:px-8 text-xs">
        <div class="grid grid-cols-3 gap-1">
            <div class="text-center bg-violet-200 rounded-t font-bold text-slate-700 py-1 text-xl">Surat Perintah Kerja</div>
            <div class="text-center bg-emerald-200 rounded-t font-bold text-slate-700 py-1 text-xl">Nota</div>
            <div class="text-center bg-orange-200 rounded-t font-bold text-slate-700 py-1 text-xl">Surat Jalan</div>
            {{-- SPK --}}
            <div>
                <table class="w-full">
                    <tr><td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $spk->no_spk }}</div></td></tr>
                    <tr>
                        <td>Tgl.</td><td>:</td>
                        <td>
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
                                            <div>{{ date('d',strtotime($spk->finished_at)) }}</div>
                                            <div>{{ date('m-y',strtotime($spk->finished_at)) }}</div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="font-bold">---</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr><td>Untuk</td><td>:</td><td><a href="" class="text-indigo-500 font-semibold text-lg">{{ $nama_pelanggan }}</a></td></tr>
                </table>

                {{-- SPK Items --}}
                <div class="border rounded px-1 mt-2 py-2" id="spk-items">
                    <table class="w-full text-xs">
                        <tr><th>Item Produksi</th><th>Jumlah</th></tr>
                        <tr><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                        @foreach ($spk_produks as $spk_produk)
                        <tr><td>{{ $spk_produk->nama_produk }}</td><td><div class="text-center">{{ $spk_produk->jumlah }}</div></td></tr>
                        @endforeach
                        <tr><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                        <tr><th>Total</th><th>{{ $spk->jumlah_total }}</th></tr>
                    </table>
                </div>
                {{-- END - SPK Items --}}
            </div>
            {{-- END - SPK --}}
            <div>
                @if (count($notas) === 0)
                <div class="flex border-t pt-1 justify-center">
                    <div>none</div>
                </div>
                @else
                @foreach ($notas as $key_nota => $nota)
                <div class="border-t-4 pt-2">
                    <div class="grid grid-cols-2">
                        <table class="w-full">
                            <tr>
                                <td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $nota->no_nota }}</div></td>
                            </tr>
                            <tr>
                                <td>Tgl.</td><td>:</td>
                                <td>
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
                                                <div class="rounded p-1 bg-pink-500 text-white font-bold text-center">
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
                                                    <div>{{ date('d',strtotime($nota->finished_at)) }}</div>
                                                    <div>{{ date('m-y',strtotime($nota->finished_at)) }}</div>
                                                </div>
                                            </div>
                                            @else
                                            <span class="font-bold">---</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td class="align-top">Alamat</td><td class="align-top">:</td>
                                <td class="align-top">
                                    @if ($nota->cust_long_ala!==null)
                                    @foreach (json_decode($nota->cust_long_ala,true) as $long)
                                    <div>{{ $long }}</div>
                                    @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Kontak</td><td>:</td>
                                <td>
                                    @if ($cust_kontaks[$key_nota]!==null)
                                    {{ $cust_kontaks[$key_nota] }}
                                    @else-@endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Nota Items --}}
                    <div class="border rounded px-1 py-2 my-2" id="nota-items-{{ $key_nota }}">
                        <table class="w-full text-xs">
                            <tr><th>Jml.</th><th>Nama Barang</th><th>Hrg.</th><th>Hrg. t</th></tr>
                            <tr><td><div class="text-center">---</div></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td><td><div class="text-center">---</div></td></tr>
                            @foreach ($col_spk_produk_notas[$key_nota] as $spk_produk_nota)
                            <tr>
                                <td><div class="text-center">{{ $spk_produk_nota->jumlah }}</div></td>
                                <td>{{ $spk_produk_nota->nama_nota }}</td>
                                <td><div class="text-center">{{ number_format($spk_produk_nota->harga,0,',','.') }}</div></td>
                                <td><div class="text-center">{{ number_format($spk_produk_nota->harga_t,0,',','.') }}</div></td>
                            </tr>
                            @endforeach
                            <tr><td></td><td><div class="text-center">-----</div></td><td></td><td><div class="text-center">---</div></td></tr>
                            <tr><th></th><th>Total</th><th></th><th>{{ number_format($nota->harga_total,0,',','.') }}</th></tr>
                        </table>
                    </div>
                    {{-- END - Nota Items --}}
                </div>
                @endforeach
                @endif
            </div>
            {{-- SJ --}}
            <div>
                @foreach ($notas as $key2 => $nota)
                @if (count($col_srjalans[$key2]) === 0)
                <div class="flex border-b pt-1 justify-center">
                    <div>none</div>
                </div>
                @else
                @foreach ($col_srjalans[$key2] as $key_srjalan => $srjalan)
                <div class="border-t-4 pt-2">
                    <div class="grid grid-cols-2">
                        <table class="w-full">
                            <tr>
                                <td>No.</td><td>:</td><td><div class="font-bold text-sm text-slate-500">{{ $srjalan->no_srjalan }}</div></td>
                            </tr>
                            <tr>
                                <td>Tgl.</td><td>:</td>
                                <td>
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
                                                <div class="rounded p-1 bg-pink-500 text-white font-bold text-center">
                                                    <div>{{ date('d',strtotime($srjalan->created_at)) }}</div>
                                                    <div>{{ date('m-y',strtotime($srjalan->created_at)) }}</div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex ml-1 items-center">
                                            @if ($srjalan->finished_at !== null)
                                            <div>
                                                <div class="rounded p-1 bg-blue-500 text-white font-bold text-center">
                                                    <div>{{ date('d',strtotime($srjalan->finished_at)) }}</div>
                                                    <div>{{ date('m-y',strtotime($srjalan->finished_at)) }}</div>
                                                </div>
                                            </div>
                                            @else
                                            <span class="font-bold">---</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="border rounded border-sky-400">
                            <div class="text-center"><h3>Data Ekspedisi</h3></div>
                            <table class="w-full">
                                <tr><td>Ekspedisi</td><td>:</td><td>{{ $srjalan->ekspedisi_nama }}</td></tr>
                                <tr>
                                    <td class="align-top">Alamat</td><td class="align-top">:</td>
                                    <td class="align-top">
                                        @if ($srjalan->eks_long_ala!==null)
                                        @foreach (json_decode($srjalan->eks_long_ala,true) as $long)
                                        <div>{{ $long }}</div>
                                        @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kontak</td><td>:</td>
                                    <td>
                                        @if ($col_ekspedisi_kontaks[$key2][$key_srjalan] !==null)
                                        {{ $col_ekspedisi_kontaks[$key2][$key_srjalan] }}
                                        @else-@endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Srjalan Items --}}
                    <div class="border rounded px-1 py-2 my-2" id="srjalan-items-{{ $key2 }}-{{ $key_srjalan }}">
                        <table class="w-full text-xs">
                            <tr><th>jml.</th><th>nama barang</th><th>jml.p</th></tr>
                            <tr><td><div class="text-center">---</div></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                            @foreach ($col_col_spk_produk_nota_srjalans[$key2][$key_srjalan] as $spk_produk_nota_srjalan)
                            <tr><td><div class="text-center">{{ $spk_produk_nota_srjalan->jumlah }}</div></td><td>{{ $spk_produk_nota_srjalan->spk_produk_nota->nama_nota }}</td><td><div class="text-center">{{ $spk_produk_nota_srjalan->jml_packing }} {{ $spk_produk_nota_srjalan->tipe_packing }}</div></td></tr>
                            @endforeach
                            <tr><td></td><td><div class="text-center">-----</div></td><td><div class="text-center">---</div></td></tr>
                            <tr>
                                <th></th><th>total</th>
                                <th>
                                    {{-- {{ dump(json_decode('["nama"=>"nama1"],["nama"=>"nama2"]', true)) }} --}}
                                    {{-- {{ dump(json_decode('[["tipe_packing"=>"colly","jumlah"=>2406,"jml_packing"=>16]]', true)) }} --}}
                                    {{-- {{ dump(json_decode('["tipe_packing"=>"colly","jumlah"=>2406,"jml_packing"=>16]', true)) }} --}}
                                    {{-- {{ dump(json_decode('["Semabung Baru No 50", "Pangkalpinang - Bangka"]', true)) }} --}}
                                    {{-- {{ dump(json_decode('{"tipe_packing":"colly","jumlah":2406,"jml_packing":16}', true)) }} --}}
                                    {{-- {{ dump($srjalan->jml_packing) }} --}}
                                    {{-- {{ dd(json_decode($srjalan->jml_packing)) }} --}}
                                    @foreach (json_decode($srjalan->jml_packing, true) as $jml_packing)
                                    {{ $jml_packing['jml_packing'] }} {{ $jml_packing['tipe_packing'] }}
                                    @endforeach
                                </th>
                            </tr>
                        </table>
                    </div>
                    {{-- END - Srjalan Items --}}
                </div>
                @endforeach
                @endif
                @endforeach
            </div>
            {{-- END - SJ --}}
        </div>
    </div>
  </main>
</div>
@endsection
{{-- <a href="https://www.flaticon.com/free-icons/fox" title="fox icons">Fox icons created by Freepik - Flaticon</a> --}}
{{-- cat --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Freepik - Flaticon</a> --}}
{{-- Honey Badger --}}
{{-- <a href="https://www.flaticon.com/free-icons/badger" title="badger icons">Badger icons created by Freepik - Flaticon</a> --}}
{{-- Panda --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Smashicons - Flaticon</a> --}}
