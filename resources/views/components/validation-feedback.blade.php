<div class="m-2 text-sm">
<!-- He who is contented is rich. - Laozi -->
    @if (session()->has('success_') && session('success_')!=="")
    <div class="alert-success rounded">{{ session('success_') }}</div>
    @endif
    @if (session()->has('warnings_') && session('warnings_')!=="")
    <div class="alert-warning rounded">{{ session('warnings_') }}</div>
    @endif
    @if (session()->has('danger_') && session('danger_')!=="")
    <div class="alert-danger rounded">{{ session('danger_') }}</div>
    @endif
    @if (session()->has('errors_') && session('errors_')!=="")
    <div class="alert-danger rounded">{{ session('errors_') }}</div>
    @endif
</div>
