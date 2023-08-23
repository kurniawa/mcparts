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
        <div class="flex">
            <h1 class="text-xl font-bold">Data Accounting - <span class="text-slate-500">{{ $user->username }}</span> -</h1>
            <div>
                <button type="submit" id="btn_new_kas" class="border font-semibold rounded text-violet-500 border-violet-300 px-1 ml-2" onclick="toggle_light(this.id, 'form_new_kas', [], ['bg-violet-200'], 'flex')">+ NEW KAS</button>
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
                                {{-- <input type="text" name="instances_type" class="border rounded text-xs p-1"> --}}
                                <select name="instances_type" id="" class="py-1 rounded text-xs">
                                    @foreach ($instances_types as $instances_type)
                                    <option value="{{ $instances_type }}">{{ $instances_type }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Kas/Instansi</td><td>:</td>
                            <td>
                                {{-- <input type="text" name="instances_name" class="border rounded text-xs p-1"> --}}
                                <select name="instances_name" id="" class="py-1 rounded text-xs">
                                    <option value=""></option>
                                    @foreach ($instances_names as $instances_name)
                                    <option value="{{ $instances_name }}">{{ $instances_name }}</option>
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
            @if (count($user_instances) === 0)
            Anda belum memiliki database kas.
            @else
            <table>
                @foreach ($user_instances as $key_user_instance => $user_instance)
                <tr>
                    <td>{{ $key_user_instance + 1 }}.</td>
                    <td>
                        <div class="ml-2">
                            <a href="{{ route('accounting.show_transactions', $user_instance->id) }}" target="_blank" class="text-sky-500 font-bold">
                                <button>{{ $user_instance->instance_type }} - {{ $user_instance->instance_name }} - {{ $user_instance->branch }} >></button>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </table>
            @endif
        </div>
    </div>
</main>

<script>

</script>

@endsection
