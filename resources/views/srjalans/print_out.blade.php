@extends('layouts.main')
@section('content')

@for ($i_copy_sj = 0; $i_copy_sj < 2; $i_copy_sj++)
<div class="containerDetailsj mr-3">
    {{-- SJ PELANGGAN TANPA RESELLER --}}
    <div class="border-t-2 mt-1 mb-1"></div>
    <div class="grid grid-cols-3 items-center" style="font-size: 0.8rem">
        @if ($srjalan->reseller_id)
        <div class="text-center">
            <div class="font-bold text-base">Pengirim:</div>
            <span class="font-bold text-base">{{ $srjalan->reseller_nama }}</span>
            <div style="font-size: 0.8rem">
                @if ($srjalan->reseller_long)
                @foreach (json_decode($srjalan->reseller_long) as $long)
                <div>{{ $long }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="text-center font-bold">
            <span class="judul-sj">SURAT JALAN /</span><br><span class="judul-sj">TANDA TERIMA BARANG</span>
        </div>
        <div class="text-center">
            @if ($i_copy_sj===0)
            ( Asli )
            @else
            ( Copy )
            @endif
        </div>
        @else
        <div class="">
            <img class="logo-mc" src="{{ asset('images/logo-mc.jpg') }}" alt="">
        </div>
        <div class="">
            <div class="font-bold font-1_3">CV. MC-Parts</div>
            <div>Jl. Raya Karanggan No. 96</div><div>Kec. Gn. Putri/Kab. Bogor</div>
        </div>
        <div class="items-center font-bold relative">
            <div class="text-center">
                <span class="judul-sj">SURAT JALAN /</span><br><span class="judul-sj">TANDA TERIMA BARANG</span>
            </div>
            <div class="absolute right-3 top-1/4">
                @if ($i_copy_sj===0)
                ( Asli )
                @else
                ( Copy )
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="hr-line border-top border-2 mt-1 mb-1"></div>
    <div class="grid {{ ($srjalan->transit_nama) ? "grid-cols-4" : "grid-cols-3" }} items-center">
        <div class="text-center">
            <div class="font-bold">Untuk:</div>
            @if ($srjalan->nama_tertera)
            <div class="font-bold">{{ $srjalan->nama_tertera }}</div>
            @else
            <div class="font-bold">{{ $srjalan->pelanggan_nama }}</div>
            @endif
        </div>
        <div class="" style="font-size: 0.8rem">
            <div class="font-bold font-big">Alamat:</div>
            <div class="font-big">
                @if ($srjalan->cust_long !== null)
                @foreach (json_decode($srjalan->cust_long, true) as $long)
                <div>{{ $long }}</div>
                @endforeach
                @endif
                @if ($srjalan->cust_kontak !== null)
                <div>
                    @if ($srjalan->cust_kontak->kodearea !== null)
                    <span>{{ $srjalan->cust_kontak->kodearea }} </span>
                    @endif
                    <span>{{ $srjalan->cust_kontak->nomor }}</span>
                </div>
                @endif
            </div>
        </div>
        <div class="font-1_2">
            <table style="font-size: 0.8rem">
                {{-- <tr class="font-bold" style="font-size: 0.8rem"><td>No</td><td>:</td><td id="no_sj">{{ $srjalan['no_srjalan'] }}</td></tr> --}}
                <tr style="font-size: 0.8rem"><td>Tanggal</td><td>:</td><td>{{ date("d-m-Y", strtotime($srjalan['created_at'])) }}</td></tr>
                <tr style="vertical-align: top;font-size:0.8rem"><td>Ekspedisi</td><td>:</td>
                    <td>
                        <div class="d-flex">
                            <div>
                                <span class="font-bold">{{ $srjalan->ekspedisi_nama }}</span>
                                @if ($srjalan->ekspedisi_long !== null)
                                @foreach (json_decode($srjalan->ekspedisi_long, true) as $long)
                                <div>{{ $long }}</div>
                                @endforeach
                                @endif
                                @if ($srjalan->ekspedisi_kontak !== null)
                                <div>
                                    @if ($srjalan->ekspedisi_kontak->kodearea !== null)
                                    <span>{{ $srjalan->ekspedisi_kontak->kodearea }} </span>
                                    @endif
                                    <span>{{ $srjalan->ekspedisi_kontak->nomor }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>

                </tr>
            </table>
        </div>
        @if ($srjalan->transit_nama !== null)
        <div class="text-xs">
            <div class="ms-3">
                <div style="color: red" class="font-bold">Via Ekspedisi:</div>
                <span class="font-bold">{{ $srjalan->transit_nama }}</span>
                @if ($srjalan->transit_long !== null)
                @foreach (json_decode($srjalan->transit_long, true) as $long)
                <div>{{ $long }}</div>
                @endforeach
                @endif
                @if ($srjalan->transit_kontak !== null)
                <div>
                    @if ($srjalan->transit_kontak->kodearea !== null)
                    <span>{{ $srjalan->transit_kontak->kodearea }} </span>
                    @endif
                    <span>{{ $srjalan->transit_kontak->nomor }}</span>
                </div>
                @endif

            </div>
        </div>
        @endif
    </div>

    <table class="tableItemsj">
        <tr>
            <th class="thTableItemsj " style="width: 50%;text-align: center;">Nama / Jenis Barang</th>
            <th class="thTableItemsj " style="text-align: center;">Jumlah</th>
        </tr>
        <tr>
            <td class="tdTableItemsj" style="position: relative">
                @if ($i_copy_sj===0)
                <div class="font-bold font-3xl" style="font-size: 1.2rem" onclick="toggle_element('form_edit_jenis_barang')">{{ $srjalan['jenis_barang'] }}</div>
                <div id="form_edit_jenis_barang" class="form-edit-jenis-barang hidden">
                    <form action="{{ route('sjs.edit_jenis_barang', $srjalan->id) }}" method="POST">
                        @csrf
                        <input type="text" name="jenis_barang" value="{{ $srjalan->jenis_barang }}" class="rounded p-0">
                        <input type="hidden" name="srjalan_id" value="{{ $srjalan->id }}">
                        <button type="submit" class="bg-emerald-200 text-emerald-500 rounded px-1 text-xs">confirm</button>
                    </form>
                </div>
                @else
                <div class="font-bold font-3xl" style="font-size: 1.2rem">{{ $srjalan['jenis_barang'] }}</div>
                @endif
            </td>
            <td class="tdTableItemsj font-bold" style="font-size: 2rem;">
                <div class="grid-2-auto grid-column-gap-0_5em">
                    <div id="divJmlKoli" class="justify-self-right">
                        @if ($srjalan->jumlah_packing !== null)
                        @foreach (json_decode($srjalan->jumlah_packing, true) as $data_packing)
                        @if ($data_packing['tipe_packing'] === 'colly')
                        <div class="flex items-center justify-center">
                            <span id="jmlKoli">{{ $data_packing['jumlah_packing'] }}</span>
                            <img class="w-9 h-9" src="{{ asset('images/koli.svg') }}" alt="">
                        </div>
                        @else
                        <div>
                            {{ $data_packing['jumlah_packing'] }} {{ $data_packing['tipe_packing'] }}
                        </div>
                        @endif
                        @endforeach
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <span style="font-style: italic;font-size:0.8rem" class="font-big">*Barang sudah diterima dengan baik dan sesuai, oleh:</span>

    <br><br>

    <div class="grid grid-cols-2">
        <div class="text-center">
            <div class="">Penerima,</div>
            <br><br>
            <div>(....................)</div>
        </div>
        <div class="text-center">
            <div class="">Hormat Kami,</div>
            <br><br>
            <div>(....................)</div>
        </div>
    </div>
    <div class="hr-line border-top border-2 mt-2"></div>
</div>
@endfor


<style>
    .logo-mc {
        width: 10em;
    }

    .containerDetailsj {
        font-family: 'Roboto';
        font-weight: normal;
        font-style: normal;
    }

    .tableItemsj {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
    }

    .thTableItemsj,
    .tdTableItemsj {
        border-left: 1px solid black;
        border-right: 1px solid black;
        border-bottom: 1px solid black;
    }

    .tdTableItemsj {
        height: 8rem;
        text-align: center;
    }
    .icon-edit-jenis-barang:hover{
        cursor: pointer;
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
        .icon-edit-jenis-barang,.form-edit-jenis-barang,.navbar{
            display:none;
        }

        .judul-sj{
            font-size: 1.2rem;
        }
        .no_print {
            display: none;
        }
    }
</style>

@endsection

