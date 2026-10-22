<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name'))</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-gray-50 text-gray-900">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-3xl items-center justify-between p-4">
                <a href="{{ route('subscribers.index') }}" class="font-semibold">{{ config('app.name') }}</a>

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 underline">Esci</button>
                    </form>
                @endauth
            </div>
        </header>

        <main class="mx-auto max-w-3xl p-4">
            @if (session('status'))
                <p class="mb-4 rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</p>
            @endif

            @yield('content')
        </main>
    </body>
</html>
