@extends('layouts.app')

@section('title', 'Iscritti')

@section('content')
    <h1 class="mb-4 text-xl font-semibold">Iscritti alle notifiche</h1>

    <form method="POST" action="{{ route('subscribers.store') }}"
          class="mb-6 grid gap-3 rounded border border-gray-200 bg-white p-4 sm:grid-cols-3">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                   class="mt-1 w-full rounded border border-gray-300 p-2">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   class="mt-1 w-full rounded border border-gray-300 p-2">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-end">
            <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-white">Aggiungi</button>
        </div>
    </form>

    <table class="w-full rounded border border-gray-200 bg-white text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-2">Nome</th>
                <th class="p-2">Email</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subscribers as $subscriber)
                <tr class="border-t border-gray-200">
                    <td class="p-2">{{ $subscriber->name }}</td>
                    <td class="p-2">{{ $subscriber->email }}</td>
                    <td class="p-2 text-right">
                        <form method="POST" action="{{ route('subscribers.destroy', $subscriber) }}"
                              onsubmit="return confirm('Eliminare {{ e($subscriber->email) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 underline">Elimina</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-4 text-center text-gray-500">Nessun iscritto.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
