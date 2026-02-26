@extends('layouts.guest')

@section('title', 'Page non trouvée — Edlya')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center">
        <div class="text-6xl font-bold text-primary-600 mb-2">404</div>
        <h1 class="text-xl font-semibold text-slate-800 mb-2">Page non trouvée</h1>
        <p class="text-slate-500 mb-6">La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-primary-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à l'accueil
        </a>
    </div>
@endsection
