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
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">Data Accounting - <span class="text-slate-500">{{ $user->username }}</span> -</h1>
                    <div>
                        <button type="submit" id="btn_new_kas" class="border font-semibold rounded text-violet-500 border-violet-300 px-1 ml-2" onclick="toggle_light(this.id, 'form_new_kas', [], ['bg-violet-200'], 'block')">+ NEW KAS</button>
                    </div>
                </div>
                {{-- FORM NEW_KAS --}}
                <div id="form_new_kas" class="hidden">
                    <div class="flex mt-2 justify-center">
                        <form action="{{ route('accounting.create_kas') }}" method="POST" class="p-1 border rounded border-violet-300">
                            @csrf
                            <table class="text-xs">
                                {{-- <tr><td>Nama Table</td><td>:</td><td><input type="text" name="table_name" class="border rounded text-xs p-1"></td></tr> --}}
                                <tr>
                                    <td>Tipe Kas/Instansi</td><td>:</td>
                                    <td>
                                        {{-- <input type="text" name="instance_type" class="border rounded text-xs p-1"> --}}
                                        <select name="instance_type" id="" class="py-1 rounded text-xs">
                                            @foreach ($instance_types as $instance_type)
                                            <option value="{{ $instance_type }}">{{ $instance_type }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nama Kas/Instansi</td><td>:</td>
                                    <td>
                                        {{-- <input type="text" name="instance_name" class="border rounded text-xs p-1"> --}}
                                        <select name="instance_name" id="" class="py-1 rounded text-xs">
                                            <option value=""></option>
                                            @foreach ($instance_names as $instance_name)
                                            <option value="{{ $instance_name }}">{{ $instance_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr><td>Nama Branch</td><td>:</td><td><input type="text" name="branch" class="border rounded text-xs p-1"></td></tr>
                                <tr><td>Nomor Rek.</td><td>:</td><td><input type="text" name="account_number" class="border rounded text-xs p-1"></td></tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="mt-3 text-center">
                                            <button type="submit" class="border-2 font-semibold rounded text-violet-500 border-violet-300 bg-violet-200 px-1">+ CREATE</button>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
                {{-- FORM NEW_KAS --}}

                <div class="border border-sky-300 rounded text-xs p-2 mt-2">
                    @if (count($user_instance_this) === 0)
                    Anda belum memiliki instansi.
                    @else
                    <table>
                        @foreach ($user_instance_this as $key_user_instance_this => $user_ins)
                        <tr>
                            <td>{{ $key_user_instance_this + 1 }}.</td>
                            <td>
                                <div class="ml-2">
                                    <a href="{{ route('accounting.show_transactions', $user_ins->id) }}" target="_blank" class="text-sky-500 font-bold">
                                        <button>{{ $user_ins->instance_type }} - {{ $user_ins->instance_name }} - {{ $user_ins->branch }} >></button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    @endif
                </div>

                <div class="border border-sky-300 rounded text-xs p-2 mt-2">
                    <div class="border rounded shadow drop-shadow bg-white p-1 inline-block">
                        <h3 class="font-bold">Daftar Instansi</h3>
                    </div>
                    <table class="table-slim mt-1">
                        <tr><th>No.</th><th>Pengelola</th><th>Tipe Instansi</th><th>Nama Instansi</th><th>Cabang</th><th></th></tr>
                        @foreach ($user_instances as $key_user_instance => $user_instance)
                        <tr>
                            <td>{{ $key_user_instance + 1 }}.</td>
                            <td>{{ $user_instance->username }}</td>
                            <td>{{ $user_instance->instance_type }}</td>
                            <td>{{ $user_instance->instance_name }}</td>
                            <td>{{ $user_instance->branch }}</td>
                            <td>
                                <div class="ml-2">
                                    <a href="{{ route('accounting.show_transactions', $user_instance->id) }}" target="_blank" class="text-sky-500 font-bold">
                                        <button>>></button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    <div class="mt-2">
                        <a href="{{ route('accounting.jurnal') }}" target="_blank">
                            <button class="bg-orange-400 text-white font-semibold p-1 rounded">Lihat Jurnal >></button>
                        </a>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('accounting.ringkasan') }}" target="_blank">
                            <button class="bg-violet-400 text-white font-semibold p-1 rounded">Lihat Ringkasan >></button>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>

</script>

@endsection
