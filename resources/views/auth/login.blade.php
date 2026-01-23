<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#071827] px-4">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('img/jf-logo.jpeg') }}"
                     alt="JF Instalações"
                     class="w-28 h-28 rounded-full object-cover shadow-lg border-4 border-[#F4C21A]" />
            </div>

            <div class="bg-white/95 backdrop-blur rounded-2xl shadow-2xl p-6">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="'E-mail'" />
                        <x-text-input id="email"
                                      class="block mt-1 w-full"
                                      type="email"
                                      name="email"
                                      :value="old('email')"
                                      required
                                      autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" :value="'Senha'" />
                        <x-text-input id="password"
                                      class="block mt-1 w-full"
                                      type="password"
                                      name="password"
                                      required
                                      autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me"
                                   type="checkbox"
                                   class="rounded border-gray-300 text-[#F4C21A] shadow-sm focus:ring-[#F4C21A]"
                                   name="remember">
                            <span class="ms-2 text-sm text-gray-600">Lembrar de mim</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-semibold text-[#0B2A45] hover:text-[#F4C21A] underline"
                               href="{{ route('password.request') }}">
                                Esqueci minha senha
                            </a>
                        @endif
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl
                                       bg-[#F4C21A] text-[#071827] font-extrabold
                                       hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F4C21A]">
                            Entrar
                        </button>

                        <p class="mt-4 text-center text-xs text-gray-500">
                            Acesso restrito • Apenas usuários autorizados
                        </p>
                    </div>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ date('Y') }} JF Instalações
            </p>
        </div>
    </div>
</x-guest-layout>
