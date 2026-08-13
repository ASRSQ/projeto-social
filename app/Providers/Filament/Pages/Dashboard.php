<?php

// Caminho sugerido: app/Filament/Pages/Dashboard.php
//
// Substitui a página inicial padrão do Filament por uma home no estilo
// do "Professor Online", adaptada para o Projeto Social. Como a view já
// tem seu próprio cabeçalho (a faixa verde), o cabeçalho automático do
// Filament (título + breadcrumb) é desativado em getHeader().

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Início';

    protected static ?string $title = 'Projeto Social';

    protected static string $view = 'filament.pages.dashboard';

    public function getHeader(): ?View
    {
        return null;
    }

    /**
     * Atalhos exibidos como cartões na página inicial.
     *
     * Troque título, descrição, ícone (nome de um componente blade-heroicons,
     * já incluído junto com o Filament — ex.: "heroicon-o-clock") e a rota
     * de cada item pelos módulos reais do seu sistema.
     */
    public function getCards(): array
    {
        return [
            [
                'icon' => 'heroicon-o-identification',
                'title' => 'Ficha Cadastral',
                'description' => 'Seus dados pessoais e de cadastro sempre atualizados.',
                'url' => '#',
            ],
            [
                'icon' => 'heroicon-o-calendar-days',
                'title' => 'Calendário de Atividades',
                'description' => 'Acompanhe as próximas atividades e eventos do projeto.',
                'url' => '#',
            ],
            [
                'icon' => 'heroicon-o-clock',
                'title' => 'Seus Horários',
                'description' => 'Consulte os horários das suas turmas e oficinas.',
                'url' => '#',
            ],
            [
                'icon' => 'heroicon-o-user-group',
                'title' => 'Suas Turmas',
                'description' => 'Veja as turmas e grupos em que você está inscrito.',
                'url' => '#',
            ],
        ];
    }
}
