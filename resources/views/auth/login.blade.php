<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Title -->
    <div class="mb-6 text-center">
        <h1 class="font-serif text-2xl font-bold text-coffee-800">
            Login Admin <span class="text-terracotta-500">- Ruang Seduh</span>
        </h1>
        <p class="font-serif text-sm text-coffee-400 italic mt-1">Diseduh dengan cinta, disajikan dengan hangat.</p>
    </div>

    <!-- Demo Access -->
    <div class="mb-6 rounded-cozy bg-sage-100 px-4 py-3 text-sm text-sage-700">
        💡 <span class="font-semibold">Demo Access:</span>
        Gunakan email <strong>admin@restocafe.test</strong> dan password <strong>password</strong> untuk masuk ke Panel Admin.
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-['Plus_Jakarta_Sans'] font-medium" />
            <x-text-input id="email" class="block mt-1 w-full focus:border-terracotta-500 focus:ring-terracotta-500" type="email" name="email" :value="old('email', 'admin@restocafe.test')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="font-['Plus_Jakarta_Sans'] font-medium" />

            <x-text-input id="password" class="block mt-1 w-full focus:border-terracotta-500 focus:ring-terracotta-500"
                            type="password"
                            name="password"
                            value="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-coffee-100 text-terracotta-500 shadow-sm focus:ring-terracotta-500" name="remember">
                <span class="font-['Plus_Jakarta_Sans'] ms-2 text-sm text-coffee-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="font-['Plus_Jakarta_Sans'] underline text-sm text-coffee-600 hover:text-coffee-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-terracotta-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <button type="submit" class="inline-flex items-center ms-3 px-5 py-2.5 font-['Plus_Jakarta_Sans'] font-semibold text-sm text-white bg-terracotta-500 hover:bg-terracotta-600 active:bg-terracotta-700 rounded-cozy shadow-warm hover:shadow-none transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-terracotta-500 focus:ring-offset-2">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</x-guest-layout>
