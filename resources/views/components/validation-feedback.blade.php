@if (session()->has('success_') || session()->has('warnings_') || session()->has('danger_') || session()->has('errors_') || session()->has('failed_'))
<div class="flex gap-1 align-top fixed top-20 right-1/2 translate-x-1/2 z-50">
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
    <button type="button" class="p-1 rounded bg-red-100 text-red-600 opacity-80 hover:cursor-pointer" onclick="this.parentElement.remove()">X</button>
</div>
@endif
