@extends('layouts.main')
@section('content')

<div class="no_print">
    <div class="flex mt-2 ml-2">
        {{-- <button id="btn-asli+copy" onclick="toggle_light_instant('nota-all', [], ['bg-slate-200'], 'block')" class="rounded border border-slate-300 text-slate-500 font-semibold px-3">asli+copy</button> --}}
        <button id="btn-asli" onclick="toggle_light_instant('nota-0', [], ['bg-slate-200'], 'block')" class="rounded border border-slate-300 text-slate-500 font-semibold px-3 ml-2">asli</button>
        <button id="btn-copy" onclick="toggle_light_instant('nota-1', [], ['bg-slate-200'], 'block')" class="rounded border border-slate-300 text-slate-500 font-semibold px-3 ml-2">copy</button>
    </div>
</div>

@for ($k = 0; $k < 2; $k++)
<div class="containerDetailNota nota-all nota-{{ $k }}" id="">
    <div class="border-t mt-1 mb-1 pt-2"></div>
    <div class="grid grid-cols-3 items-center">
        <div class=""><img class="logo-mc" src="{{ asset('images/logo-mc.jpg') }}" alt="" style="width: 10rem;"></div>
        <div class="">
            <div class="fw-bold" style="font-size:1.3rem">NOTA</div>
            <div class="fw-bold font-1_3" style="font-size: 0.8rem">CV. MC-Parts</div>
            <div style="font-size: 0.8rem">Jl. Raya Karanggan No. 96</div>
            <div style="font-size: 0.8rem">Kec. Gn. Putri/Kab. Bogor</div>
            {{-- <br>0812 9335 218<br>0812 8655 6500 --}}
        </div>
        <div class="">
            <div class="flex items-center">
                <table style="font-size: 0.8rem" class="w-full">
                    <tr>
                        <td><div class="font-bold">No.</div></td><th>:</th>
                        <td>
                            <div class="font-bold">
                                MCP/{{ date('m/y', strtotime($nota->created_at)) }}/...
                                {{-- {{ $nota->nomor_nota }} --}}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><div class="font-bold">Kepada</div></td><th>:</th>
                        <td>
                            @if ($nota->reseller_id)
                            <div class="font-bold">{{ $nota->reseller_nama }} - {{ $nota->pelanggan_nama }}</div>
                            @else
                            <div class="font-bold">{{ $nota->pelanggan_nama }}</div>
                            @endif
                        </td>
                    </tr>
                    <tr><td>Tanggal</td><td>:</td><td>{{ date('d-m-Y', strtotime($nota->created_at)) }}</td></tr>
                    <tr style="vertical-align: top"><td>Alamat</td><td>:</td>
                        <td>
                            @if ($nota->cust_short!==null)
                            {{-- @foreach (json_decode($cust_long_ala,true) as $long)
                            <div>{{ $long }}</div>
                            @endforeach --}}
                            <div>{{ $nota->cust_short }}</div>
                            @else
                                @if ($nota->cust_long !== null)
                                @foreach (json_decode($nota->cust_long, true) as $long)
                                <div>{{ $long }}</div>
                                @endforeach
                                @endif
                                @if ($cust_kontak)
                                    @if ($cust_kontak->kodearea)
                                    <span>({{ $cust_kontak->kodearea }}) </span>
                                    @endif
                                <span class="toFormatPhoneNumber">{{ $cust_kontak->nomor }}</span>
                                @else
                                @endif
                            @endif
                        </td>
                    </tr>
                </table>
                <div style="font-weight:bold;font-size: 1.5rem" class="w-full text-center">
                    @if ($k === 1)
                    Copy
                    @else
                    Asli
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="pr-2">
        <table style="width: 100%;" class="mt-3 tableItemNota">
            <tr class="tr-border-bottom tr-border-left-right font-1_3 fw-bold">
                <td class="text-center">Jumlah</td><td class="text-center">Nama Barang</td><td class="text-center">Hrg./pcs</td><td class="text-center">Harga</td>
            </tr>
            @for ($i = 0; $i < count($spk_produk_notas); $i++)
            <tr class='tr-border-left-right font-1_1' style="font-size: 0.9rem">
                <td class="toFormatNumber text-end pe-2">{{ $spk_produk_notas[$i]->jumlah }}</td>
                <td class="ps-2 pe-2">
                    <div>
                        {{ $spk_produk_notas[$i]->nama_nota }}
                    </div>
                    @if ($spk_produk_notas[$i]->keterangan)
                    <div class="text-xs text-slate-400 italic">
                        {{ $spk_produk_notas[$i]->keterangan }}
                    </div>
                    @endif
                </td>
                <td class="ps-2 pe-2">
                    <div class="flex justify-between">
                        <span>Rp.</span>
                        <div><span>{{ number_format($spk_produk_notas[$i]->harga,0,',','.') }}</span>,-</div>
                    </div>
                </td>
                <td class="ps-2 pe-2">
                    <div class="flex justify-between">
                        <span>Rp.</span>
                        <div><span>{{ number_format($spk_produk_notas[$i]->harga_t,0,',','.') }}</span>,-</div>
                    </div>
                </td>
            @endfor
            @for ($j = 0; $j < $rest_row; $j++)
            <tr class='tr-border-left-right' style='height:1rem'><td></td><td></td><td></td><td></td></tr>
            @endfor
            <tr class='tr-border-left-right tr-border-bottom'><td></td><td></td><td></td><td></td></tr>
            <tr>
                <td></td><td></td>
                <td class='blrb-total text-center font-1_3 font-bold'>Total Harga</td>
                <td class="blrb-total ps-2 pe-2">
                    <div class="flex justify-between font-1_2">
                        <span>Rp.</span>
                        <div><span>{{ number_format($nota->harga_total,0,',','.') }}</span>,-</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="text-end mt-3">
        <div class="text-center me-5" style="display: inline-block">
            <div class="">Hormat Kami,</div>
            <br><br>
            <div>(....................)</div>
        </div>
    </div>

    <div class="hr-line border-top border-2 mt-2"></div>
</div>
@endfor


<style>
    .containerDetailNota {
        font-family: 'Roboto';
        font-weight: normal;
        font-style: normal;
        /* font-size: 0.8em; */
    }

    .tableItemNota {
        border-collapse: collapse;
        border-top: 1px solid black;
    }

    .tr-border-bottom th {
        border-bottom: 1px solid black;
        padding-top: 1em;
        padding-bottom: 1em;
    }

    .tr-border-bottom td {
        border-bottom: 1px solid black;
    }

    .tr-border-left-right th,
    .tr-border-left-right td {
        border-left: 1px solid black;
        border-right: 1px solid black;
    }

    .height-1_5em td {
        height: 1.5em;
    }

    .blrb-total {
        border-left: 1px solid black;
        border-right: 1px solid black;
        border-bottom: 3px solid black;
        /* padding-top: 1em;
        padding-bottom: 1em; */
    }

    @media print {
        @page {
            size: A4;
            /* DIN A4 standard, Europe */
            margin: 3mm 5mm 0 5mm;
        }

        html,
        body {
            width: 210mm;
            height: 297mm;
            /* height: 282mm; */
            /* font-size: 11px; */
            background: #FFF;
            overflow: visible;
            padding-top: 0mm;
        }
        .no_print {
            display: none;
        }
    }
</style>

@endsection

