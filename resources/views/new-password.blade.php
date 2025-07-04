@extends("layout")
@section("content")

<!-- Modal Backdrop -->
<div class="fixed inset-0 flex items-center justify-center z-50">
  <!-- Modal Box -->
    <div class="bg-[#fefaed] rounded-md shadow-lg w-full max-w-sm p-8">
        <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900 mb-4">Change Your Password</h2>
        <p class="text-center text-sm">Enter new password for your account.</p>

        @if(session('error'))
            <div class="my-4 p-3 rounded-md bg-red-100    text-red-800 text-sm border border-red-300"> 
            {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="my-4 p-3 rounded-md bg-green-100 text-green-800 text-sm border border-green-300">
            {{ session('success') }}
            </div>
        @endif

        <form class="space-y-6" action="{{ route('reset.password.post') }}" method="POST">
            @csrf
            <input type="text" name="token" hidden value="{{ $token }}">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-900">Email</label>
                        <div class="mt-2">
                            <input type="email" name="email" id="email" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-[#f4dc96] sm:text-sm">
                        </div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900">Enter new password</label>
                        <div class="mt-2">
                            <input type="password" name="password" id="password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-[#f4dc96] sm:text-sm">
                        </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900">Confirm password</label>
                        <div class="mt-2">
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-[#f4dc96] sm:text-sm">
                        </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-[#222527] px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-[#202326] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#222527]"> 
                        Change
                    </button>
                </div>
        </form>
    </div>
</div>

@endsection
