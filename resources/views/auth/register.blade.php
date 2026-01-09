@extends('layouts.guest')

@section('title', 'Inscription - Edlya')

@section('content')
    <div class="bg-white p-8 rounded-lg border border-slate-200">
        <h1 class="text-xl font-semibold text-slate-800 mb-6">Créer un compte</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <x-form.input 
                name="name" 
                label="Nom complet" 
                :required="true" 
                :autofocus="true"
            />

            <x-form.input 
                name="email" 
                type="email" 
                label="Email" 
                :required="true"
            />

            <x-form.input 
                name="telephone" 
                type="tel" 
                label="Téléphone" 
                placeholder="Optionnel"
            />

            <x-form.input 
                name="password" 
                type="password" 
                label="Mot de passe" 
                :required="true"
            />

            <x-form.input 
                name="password_confirmation" 
                type="password" 
                label="Confirmer le mot de passe" 
                :required="true"
            />

            <x-form.button>S'inscrire</x-form.button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Déjà un compte ? 
            <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Se connecter</a>
        </p>
    </div>
@endsection