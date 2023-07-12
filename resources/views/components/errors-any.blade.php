@if ($errors->any())
<div class="mt-2 text-pink-600">
@foreach ($errors->all() as $error)
{{ $error }}
@endforeach
</div>
@endif
