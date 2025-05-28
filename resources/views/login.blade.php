@extends('layout')
@section('title', 'Sign In')
@section('content')
<div class="flex min-h-screen items-center justify-center px-6 py-12 lg:px-8">
  <div class="w-full max-w-sm bg-[#fefaed] p-8 rounded-md shadow">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2 class="mt-10 text-center text-2xl font-bold tracking-tight text-gray-900">Log in to your account</h2>
      <p class="text-center">Don't have an account? <a class="text-[#cc7700] hover:text-[#a35f00]" href="registration">Register</a> here.</p>

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
    </div>

    <form class="space-y-6 mt-8" action="{{ route('login.post') }}" method="POST">
      @csrf
      <div>
        <label for="email" class="block text-sm font-medium text-gray-900">Email</label>
        <div class="mt-2">
          <input type="email" name="email" id="email" required
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-[#f4dc96] sm:text-sm">
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
          <div class="text-sm">
            <a href="{{ route('forget.password') }}" class="font-semibold text-[#cc7700] hover:text-[#a35f00]">Forgot password?</a>
          </div>
        </div>
        <div class="mt-2">
          <input type="password" name="password" id="password" autocomplete="current-password" required
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-[#f4dc96] sm:text-sm">
        </div>
      </div>

      <div>
        <button type="submit"
          class="flex w-full justify-center rounded-md bg-[#222527] px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-[#202326] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#222527]">
          Log In
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
