@extends('layouts.main')
@section('content')
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-bold tracking-tight text-gray-900">Artisan Command Center</h1>
    </div>
  </header>
  <x-errors-any></x-errors-any>
  <x-validation-feedback></x-validation-feedback>
  <main class="text-xs m-5 p-3 bg-white shadow rounded">
    <form action="{{ route('artisan.accounting_update_data_rupiah') }}" method="POST">
        @csrf
        <button type="submit" class="rounded bg-emerald-200 text-emerald-500 font-bold p-1">accounting_update_data_rupiah</button>
    </form>
  </main>
</div>
@endsection
