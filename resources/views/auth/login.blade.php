<x-guest-layout>
    <!-- Status der Sitzung -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Anzeige für ungültige Lizenz -->
    @if (session('error'))
        <div class="mb-4 text-red-600 text-center">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- E-Mail-Adresse -->
        <div>
            <x-input-label for="email" :value="__('E-Mail')" style="color: black;" />
            <x-text-input id="email" class="block mt-1 w-full border border-black-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <!-- Passwort -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Passwort')" style="color: black;" />
            <x-text-input id="password" class="block mt-1 w-full border border-black-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Angemeldet bleiben -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-black-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-black-800">{{ __('Angemeldet bleiben') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-black-800 hover:text-black-900" style="color: black !important;" href="{{ route('password.request') }}">
                    {{ __('Passwort vergessen?') }}
                </a>
            @endif

            <a class="ms-3" style="color: black !important; text-decoration: none;" href="{{ route('register') }}">
                {{ __('Nicht registriert?') }}
            </a>

            <x-primary-button class="ms-3" style="color: black !important;">
                {{ __('Anmelden') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
