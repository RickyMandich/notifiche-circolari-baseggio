@extends('layouts.app')

@section('title', 'Accedi')

@section('content')
    <h1 class="mb-4 text-xl font-semibold">Accedi</h1>

    <form method="POST" action="{{ route('login') }}" class="space-y-4 rounded border border-gray-200 bg-white p-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 w-full rounded border border-gray-300 p-2">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password" required
                   class="mt-1 w-full rounded border border-gray-300 p-2">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1"> Ricordami
        </label>

        <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-white">Accedi</button>
    </form>
@endsection
