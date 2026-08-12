{{-- Caminho sugerido: resources/views/filament/pages/dashboard.blade.php --}}

<x-filament-panels::page>
    <div class="overflow-hidden rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
        {{-- Faixa principal --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-[#154D3F] to-[#0E362C] px-6 py-8 sm:px-10 sm:py-10">
            {{-- Textura decorativa --}}
            <div
                class="absolute inset-0 opacity-[0.08]"
                style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 18px 18px;"
            ></div>
            <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full bg-[#C99A4B] opacity-20 blur-3xl"></div>
            <div class="absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-white opacity-10 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/25">
                        <x-heroicon-o-heart class="h-7 w-7 text-white" />
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                            Projeto Social
                        </h1>
                        <p class="mt-1 text-sm text-white/80">
                            O portal de quem faz parte do projeto
                        </p>
                    </div>
                </div>

                <a
                    href="#"
                    class="inline-flex items-center justify-center gap-2 self-start rounded-lg border border-white/40 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10 sm:self-auto"
                >
                    <x-heroicon-o-user-circle class="h-4 w-4" />
                    Meus Dados
                </a>
            </div>
        </div>

        {{-- Faixa de introdução --}}
        <div class="bg-[#DCE7E1] px-6 py-4 dark:bg-white/5 sm:px-10">
            <p class="text-sm font-medium text-[#154D3F] dark:text-white">
                Confira alguns dos recursos disponíveis para você:
            </p>
        </div>
    </div>

    {{-- Cartões de atalho --}}
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($this->getCards() as $card)
            <a
                href="{{ $card['url'] }}"
                class="group flex flex-col items-center rounded-xl bg-white p-6 text-center shadow-sm ring-1 ring-gray-950/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
            >
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-[#154D3F] transition group-hover:bg-[#0E362C]">
                    <x-dynamic-component :component="$card['icon']" class="h-8 w-8 text-white" />
                </span>

                <h3 class="mt-4 text-base font-semibold text-[#154D3F] dark:text-emerald-400">
                    {{ $card['title'] }}
                </h3>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $card['description'] }}
                </p>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
