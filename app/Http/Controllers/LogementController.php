<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogementRequest;
use App\Models\Logement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogementController extends Controller
{
    public function index(): View
    {
        $logements = Logement::where('user_id', Auth::id())->latest()->get();

        return view('logements.index', compact('logements'));
    }

    public function create(): View
    {
        return view('logements.create');
    }

    public function store(LogementRequest $request): RedirectResponse
    {
        Logement::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('logements.index')
            ->with('success', 'Logement créé avec succès.');
    }

    public function show(Logement $logement): View
    {
        $this->authorizeAccess($logement);

        return view('logements.show', compact('logement'));
    }

    public function edit(Logement $logement): View
    {
        $this->authorizeAccess($logement);

        return view('logements.edit', compact('logement'));
    }

    public function update(LogementRequest $request, Logement $logement): RedirectResponse
    {
        $this->authorizeAccess($logement);

        $logement->update($request->validated());

        return redirect()
            ->route('logements.show', $logement)
            ->with('success', 'Logement mis à jour avec succès.');
    }

    public function destroy(Logement $logement): RedirectResponse
    {
        $this->authorizeAccess($logement);

        $logement->delete();

        return redirect()
            ->route('logements.index')
            ->with('success', 'Logement supprimé.');
    }

    private function authorizeAccess(Logement $logement): void
    {
        if ($logement->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
