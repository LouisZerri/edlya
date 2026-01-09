@extends('layouts.guest')

@section('title', 'Connexion - Edlya')

@section('content')
    <div class="bg-white p-8 rounded-lg border border-slate-200">
        <h1 class="text-xl font-semibold text-slate-800 mb-6">Connexion</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <x-form.input 
                name="email" 
                type="email" 
                label="Email" 
                :required="true" 
                :autofocus="true"
            />

            <x-form.input 
                name="password" 
                type="password" 
                label="Mot de passe" 
                :required="true"
            />

            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                    <span class="ml-2 text-sm text-slate-600">Se souvenir de moi</span>
                </label>
            </div>

            <x-form.button>Se connecter</x-form.button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Pas encore de compte ? 
            <a href="{{ route('register') }}" class="text-primary-600 hover:underline">S'inscrire</a>
        </p>
    </div>
@endsection