<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function index(): View
    {
        return view('subscribers.index', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:subscribers,email'],
        ]);

        Subscriber::create($data);

        return redirect()
            ->route('subscribers.index')
            ->with('status', 'Iscritto aggiunto.');
    }

    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return redirect()
            ->route('subscribers.index')
            ->with('status', 'Iscritto eliminato.');
    }
}
