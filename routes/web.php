<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use App\Models\Ocorrencia;
use App\Models\CodigoOcorrencia;
use App\Http\Middleware\AuthenticateFilamentPanel;

Route::get('/', function () {
    $user = Auth::user();
    $panel = Filament::getPanel('admin');

    if ($user instanceof FilamentUser && $panel && ! $user->canAccessPanel($panel)) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    return redirect()->route('filament.admin.auth.login');
});

Route::get('/ocorrencias/{ocorrencia}/imprimir', function (Ocorrencia $ocorrencia) {
    $user = Auth::user();
    $podeImprimir = $user?->isAdmin()
        || ($user?->isProfessorDt() && $user->turmasComoDiretor()->whereKey($ocorrencia->turma_id)->exists());

    abort_unless($podeImprimir && $ocorrencia->classificado_em, 403);

    $codigoOcorrencia = CodigoOcorrencia::para($ocorrencia);
    $ocorrencia->load(['aluno', 'professor', 'turma.diretorTurma']);

    return view('ocorrencias.imprimir', compact('ocorrencia', 'codigoOcorrencia'));
})->middleware(AuthenticateFilamentPanel::class)->name('ocorrencias.imprimir');
