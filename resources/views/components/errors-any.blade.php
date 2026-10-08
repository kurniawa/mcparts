@if ($errors->any())
<div class="flex gap-1 fixed top-20 right-1/2 translate-x-1/2 z-50 align-top">
    <div class="">
    @foreach ($errors->all() as $error)
        <div class="px-3 py-2 rounded bg-red-100 text-red-600 opacity-80">{{ $error }}</div>
    @endforeach
    </div>
    <button type="button" class="p-1 rounded bg-red-100 text-red-600 opacity-80 hover:cursor-pointer" onclick="this.parentElement.remove()">X</button>
</div>
@endif
