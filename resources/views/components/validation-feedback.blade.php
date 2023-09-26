<div class="m-2 text-sm">
<!-- He who is contented is rich. - Laozi -->
    @if (session()->has('success_') && session('success_')!=="")
    <div class="font-semibold px-3 py-2 rounded bg-emerald-200 text-emerald-600 opacity-70">{{ session('success_') }}</div>
    @endif
    @if (session()->has('warnings_') && session('warnings_')!=="")
    <div class="font-semibold px-3 py-2 rounded bg-yellow-200 text-yellow-600 opacity-70">{{ session('warnings_') }}</div>
    @endif
    @if (session()->has('danger_') && session('danger_')!=="")
    <div class="font-semibold px-3 py-2 rounded bg-red-200 text-red-600 opacity-70">{{ session('danger_') }}</div>
    @endif
    @if (session()->has('errors_') && session('errors_')!=="")
    <div class="font-semibold px-3 py-2 rounded bg-red-200 text-red-600 opacity-70">{{ session('errors_') }}</div>
    @endif
    @if (session()->has('failed_') && session('failed_')!=="")
    <div class="font-semibold px-3 py-2 rounded bg-red-200 text-red-600 opacity-70">{{ session('failed_') }}</div>
    @endif
</div>
