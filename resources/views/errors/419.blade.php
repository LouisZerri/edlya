@extends('layouts.guest')

@section('title', 'Session expirée — Edlya')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center">
        <div class="text-6xl font-bold text-amber-500 mb-2">419</div>
        <h1 class="text-xl font-semibold text-slate-800 mb-2">Session expirée</h1>
        <p class="text-slate-500 mb-6">Votre session a expiré. Veuillez rafraîchir la page et réessayer.</p>
        <a href="{{ url()->current() }}" class="inline-flex items-center gap-2 bg-primary-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Rafraîchir la page
        </a>
    </div>
@endsection
