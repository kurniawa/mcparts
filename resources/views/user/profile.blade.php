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

    <div class="mx-1 max-w-7xl py-1 sm:px-6 lg:px-8 text-xs">

        <div class="text-center">
            <span class="text-sm text-slate-500 mt-5 block">"If you look to others for fulfillment, you will never truly be fulfilled."</span>
        </div>

        <div class="flex flex-col md:flex-row md:justify-center mt-5 gap-5">
            {{-- Container Profile Picture --}}
            <div class="flex justify-center">
                @if ($user->profile_picture)
                <div class="md:w-96 md:h-96 relative">
                    <div class="rounded-full overflow-hidden bg-orange-100">
                        <img class="w-full" src="{{ asset("storage/$user->profile_picture") }}">
                    </div>
                    <label class="absolute right-0 bottom-0 hover:cursor-pointer" onclick="showModalEditProfilePicture()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-500 ml-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </label>
                </div>
                @else
                <div class="w-72 h-72 md:w-96 md:h-96 relative">
                    <div class="rounded-full overflow-hidden bg-orange-100 flex">
                        <a href="https://www.flaticon.com/free-icons/superhero" title="superhero icons" target="_blank" rel="noopener noreferrer">
                            <img class="object-cover" src="{{ asset('images/badger.png') }}" alt="Profile Picture">
                        </a>
                    </div>
                    <label class="absolute right-0 bottom-0 hover:cursor-pointer" onclick="showModalEditProfilePicture()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-500 ml-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </label>
                </div>
                @endif
                {{-- <div class="text-xs text-center">
                    Superhero icons created by Freepik - Flaticon
                </div> --}}
            </div>

            <div>
                {{-- Container Nama --}}
                <form action="{{ route('user.profile.update_nama', $user->id) }}" method="POST" class="sm:w-full p-5 border rounded border-slate-300 bg-white shadow-xl max-w-sm">
                    @csrf
                    <h3 class="text-slate-600">Nama</h3>
                    <div class="flex items-center">
                        <div class="w-full">
                            <input type="text" name="nama" id="nama" class="border border-slate-400 text-slate-700 shadow rounded w-full px-3 py-2 block placeholder:text-slate-400 focus:outline-none focus:border-none focus:ring-1 focus:ring-blue-500 invalid:text-pink-700 invalid:focus:ring-pink-700;" value="{{ $user->nama }}">
                            @error('user.nama')
                            <div class="w-full px-2 py-1 rounded text-pink-600">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-500 ml-1 hover:cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg> --}}
                    </div>
                    <div class="mt-2">
                        @if (session()->has('nama_updated'))
                        <div class="font-semibold w-full px-3 py-2 rounded bg-emerald-200 text-emerald-600 opacity-70">
                            {{ session('nama_updated') }}
                        </div>
                        @endif
                    </div>
                    <div class="text-right mt-5">
                        <button
                        class="rounded px-3 py-2 text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 focus:ring focus:ring-blue-300">
                            Confirm
                        </button>
                    </div>

                </form>

                {{-- Container Username --}}
                <form action="{{ route('user.profile.update_username', $user->id) }}" class="mt-5 sm:w-full p-5 border rounded border-slate-300 bg-white shadow-xl max-w-sm" method="POST">
                    @csrf
                    <h3 class="text-slate-600">Username</h3>
                    <div class="flex items-center">
                        <input type="text" name="username" id="username" class="border border-slate-400 text-slate-700 shadow rounded w-full px-3 py-2 block placeholder:text-slate-400 focus:outline-none focus:border-none focus:ring-1 focus:ring-blue-500 invalid:text-pink-700 invalid:focus:ring-pink-700;" value="{{ $user->username }}">
                        {{-- <svg x-on:click="open=!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-500 ml-1 hover:cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg> --}}
                    </div>
                    <div class="mt-2">
                        @if (session()->has('username_updated'))
                        <div class="font-semibold w-full px-3 py-2 rounded bg-emerald-200 text-emerald-600 opacity-70">
                            {{ session('username_updated') }}
                        </div>
                        @endif
                    </div>
                    <div class="text-right mt-5">
                        <button
                        class="rounded px-3 py-2 text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 focus:ring focus:ring-blue-300">
                            Confirm
                        </button>
                    </div>
                </form>

                {{-- Container Password --}}
                <form action="{{ route('user.profile.update_password', $user->id) }}" method="POST" class="mt-5 sm:w-full p-5 border rounded border-slate-300 bg-white shadow-xl max-w-sm">
                    @csrf
                    <h3 class="text-slate-600">Password</h3>
                    <label for="current_password" class="text-slate-600">Current Password :</label>
                    <input type="password" name="current_password" id="current_password" placeholder="Current Password..."
                    class="border border-slate-400 text-slate-700 shadow rounded w-full px-3 py-2 block placeholder:text-slate-400 focus:outline-none focus:border-none focus:ring-1 focus:ring-blue-500 invalid:text-pink-700 invalid:focus:ring-pink-700;"
                    >
                    @error('current_password')
                    <div class="w-full px-2 py-1 rounded text-pink-600">{{ $message }}</div>
                    @enderror
                    <label for="new_password" class="text-slate-600 block mt-2">New Password :</label>
                    <input type="password" name="new_password" id="new_password" placeholder="New Password..."
                    class="border border-slate-400 text-slate-700 shadow rounded w-full px-3 py-2 block placeholder:text-slate-400 focus:outline-none focus:border-none focus:ring-1 focus:ring-blue-500 invalid:text-pink-700 invalid:focus:ring-pink-700;">
                    @error('new_password')
                    <div class="w-full px-2 py-1 rounded text-pink-600">{{ $message }}</div>
                    @enderror
                    <label for="confirm_new_password" class="text-slate-600 block mt-2">Confirm New Password :</label>
                    <input type="password" name="confirm_new_password" id="confirm_new_password" placeholder="Confirm New Password..."
                    class="border border-slate-400 text-slate-700 shadow rounded w-full px-3 py-2 block placeholder:text-slate-400 focus:outline-none focus:border-none focus:ring-1 focus:ring-blue-500 invalid:text-pink-700 invalid:focus:ring-pink-700;">
                    @error('confirm_new_password')
                    <div class="w-full px-2 py-1 rounded text-pink-600">{{ $message }}</div>
                    @enderror

                    <div class="mt-2">
                        @if (session()->has('password_updated'))
                        <div class="font-semibold w-full px-3 py-2 rounded bg-emerald-200 text-emerald-600 opacity-70">
                            {{ session('password_updated') }}
                        </div>
                        @endif
                        @if (session()->has('password_errors'))
                        <div class="font-semibold w-full px-3 py-2 rounded bg-red-200 text-red-600 opacity-70">
                            {{ session('password_errors') }}
                        </div>
                        @endif
                    </div>

                    <div class="text-right mt-2">
                        <button
                        class="rounded px-3 py-2 text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 focus:ring focus:ring-blue-300">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Hidden Element At The Beginning: Element For Edit Profile Picture --}}
        <div class="absolute bg-white top-5 left-1/2 -translate-x-1/2 sm:w-4/5 p-5 border rounded shadow-xl hidden" id="modal_edit_profile_picture">
            {{-- Tombol Close --}}
            <div class="absolute bg-pink-100 w-6 h-6 rounded-full -right-4 -top-4 text-center hover:cursor-pointer" onclick="closeModalEditProfilePicture()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="w-4 h-4 text-pink-600 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <form action="{{ route('user.profile.update_photo', $user->id) }}" method="POST" class="mt-3 text-center" enctype="multipart/form-data">
                @csrf
                {{-- Bingkai Foto --}}
                {{-- <div class="sm:w-80 sm:h-80 rounded-full overflow-hidden mx-auto bg-orange-100">
                    <img class="w-full" src="">
                </div> --}}
                @if ($user->profile_picture)
                <div class="sm:w-80 sm:h-80 rounded-full overflow-hidden mx-auto bg-orange-100">
                    <img id="preview_photo" class="w-full" src="{{ asset("storage/$user->profile_picture") }}">
                </div>
                @else
                <div class="sm:w-80 sm:h-80 rounded-full overflow-hidden mx-auto bg-orange-100">
                    <img id="preview_photo" class="w-full" src="{{ asset('images/badger.png') }}" alt="">
                    {{-- <a href="https://www.flaticon.com/free-icons/superhero" title="superhero icons" target="_blank" rel="noopener noreferrer">
                    </a> --}}
                </div>
                @endif
                {{-- <div class="text-xs text-center">
                    Superhero icons created by Freepik - Flaticon
                </div> --}}
                <div class="mt-5">
                    <div>
                        {{-- <div @click="$refs.choose_photo.click()">Upload Image</div> --}}
                        <input type="file" name="photo" value="photo" onchange="previewImage(this.files[0])">
                    </div>
                </div>

                <div class="mt-3">
                    @if (session()->has('save_photo_logs'))
                    <div class="font-semibold w-full px-3 py-2 rounded bg-emerald-200 text-emerald-600 opacity-70">{{ session('save_photo_logs') }}</div>
                    @endif
                    @if (session()->has('delete_photo_logs'))
                    <div class="font-semibold w-full px-3 py-2 rounded bg-red-200 text-red-600 opacity-70">{{ session('delete_photo_logs') }}</div>
                    @endif
                </div>
                <div class="mt-3">
                    <button class="rounded px-3 py-2 text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600 active:bg-blue-700 focus:ring focus:ring-blue-300" type="submit">Save Photo</button>
                </div>
            </form>
            <form action="{{ route('user.profile.delete_profile_picture', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-2 py-1 font-semibold text-xs bg-pink-500 text-white hover:bg-pink-600 active:bg-pink-700 focus:ring focus:ring-pink-300 rounded mt-1">Delete</button>
            </form>
        </div>
    </div>
</main>

<script>
    function closeModalEditProfilePicture() {
        $('#modal_edit_profile_picture').hide(300);
    }

    function showModalEditProfilePicture() {
        $('#modal_edit_profile_picture').show(300);
    }

    function previewImage(image_file) {
        if (image_file) {
            // console.log(image_file)
            document.getElementById('preview_photo').src = URL.createObjectURL(image_file);
        }
    }
</script>
@endsection
{{-- <a href="https://www.flaticon.com/free-icons/fox" title="fox icons">Fox icons created by Freepik - Flaticon</a> --}}
{{-- cat --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Freepik - Flaticon</a> --}}
{{-- Honey Badger --}}
{{-- <a href="https://www.flaticon.com/free-icons/badger" title="badger icons">Badger icons created by Freepik - Flaticon</a> --}}
{{-- Panda --}}
{{-- <a href="https://www.flaticon.com/free-icons/cute" title="cute icons">Cute icons created by Smashicons - Flaticon</a> --}}



