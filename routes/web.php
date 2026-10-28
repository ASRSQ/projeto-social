<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

Route::get('/ocorrencias/selecionar-impressao', function () {
    $user = Auth::user();
    abort_unless($user?->isAdmin() || $user?->isProfessorDt(), 403);

    $query = Ocorrencia::query()
        ->whereNotNull('classificado_em')
        ->with(['aluno', 'turma'])
        ->orderByDesc('data_ocorrido');

    if (! $user->isAdmin()) {
        $query->whereIn('turma_id', $user->turmasComoDiretor()->select('id'));
    }

    $ocorrencias = $query->get();

    return view('ocorrencias.selecionar-impressao', compact('ocorrencias'));
})->middleware(AuthenticateFilamentPanel::class)->name('ocorrencias.selecionar-lote');

Route::get('/ocorrencias/imprimir-lote', function (Request $request) {
    $user = Auth::user();
    abort_unless($user?->isAdmin() || $user?->isProfessorDt(), 403);

    $validated = Validator::make($request->query(), [
        'ids' => ['required', 'array', 'min:2'],
        'ids.*' => ['required', 'integer', 'distinct'],
    ])->validate();

    $ids = array_map('intval', $validated['ids']);
    $ocorrenciasPorId = Ocorrencia::query()
        ->whereIn('id', $ids)
        ->whereNotNull('classificado_em')
        ->with(['aluno', 'professor', 'disciplina', 'turma.diretorTurma'])
        ->get()
        ->keyBy('id');

    abort_unless($ocorrenciasPorId->count() === count($ids), 403);

    if (! $user->isAdmin()) {
        foreach ($ocorrenciasPorId as $ocorrencia) {
            abort_unless($user->turmasComoDiretor()->whereKey($ocorrencia->turma_id)->exists(), 403);
        }
    }

    $ocorrencias = collect($ids)->map(fn (int $id) => $ocorrenciasPorId->get($id));

    return view('ocorrencias.imprimir-lote', compact('ocorrencias'));
})->middleware(AuthenticateFilamentPanel::class)->name('ocorrencias.imprimir-lote');
