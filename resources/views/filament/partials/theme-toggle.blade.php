<x-filament::icon-button
    color="gray"
    icon="heroicon-o-moon"
    label="Ativar tema escuro"
    x-data="{}"
    x-cloak
    x-show="$store.theme !== 'dark'"
    x-on:click="$dispatch('theme-changed', 'dark')"
    x-tooltip="{ content: 'Ativar tema escuro', theme: $store.theme }"
/>

<x-filament::icon-button
    color="gray"
    icon="heroicon-o-sun"
    label="Ativar tema claro"
    x-data="{}"
    x-cloak
    x-show="$store.theme === 'dark'"
    x-on:click="$dispatch('theme-changed', 'light')"
    x-tooltip="{ content: 'Ativar tema claro', theme: $store.theme }"
/>
