<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#071827] px-4">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('img/jf-logo.jpeg') }}"
                     alt="JF Instalações"
                     class="w-28 h-28 rounded-full object-cover shadow-lg border-4 border-[#F4C21A]" />
            </div>

            <div class="bg-white/95 backdrop-blur rounded-2xl shadow-2xl p-6">
                <div class="mb-4 text-sm text-gray-600">
                    Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para redefinir sua senha.
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
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

                    <div class="mt-6 flex items-center justify-between gap-3">
                        <a href="{{ route('login') }}"
                           class="text-sm font-semibold text-[#0B2A45] hover:text-[#F4C21A] underline">
                            Voltar ao login
                        </a>

                        <button type="submit"
                                class="inline-flex justify-center items-center px-4 py-2 rounded-xl
                                       bg-[#F4C21A] text-[#071827] font-extrabold
                                       hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F4C21A]">
                            Enviar link
                        </button>
                    </div>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ date('Y') }} JF Instalações
            </p>
        </div>
    </div>
</x-guest-layout>
