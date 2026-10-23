<?php

namespace App\Console\Commands;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class PromoverAlunos extends Command
{
    protected $signature = 'alunos:promover-turmas';

    protected $description = 'Promove alunos de turma e registra os concluintes do ano letivo';

    public function handle(): int
    {
        $hoje = now('America/Fortaleza');
        $anoLetivo = (int) $hoje->year;
        $dataConclusao = sprintf('%d-12-31', $anoLetivo - 1);

        try {
            $processou = DB::transaction(function () use ($anoLetivo, $hoje, $dataConclusao): bool {
                if (DB::table('execucoes_progressao')->where('ano_letivo', $anoLetivo)->exists()) {
                    return false;
                }

                DB::table('execucoes_progressao')->insert([
                    'ano_letivo' => $anoLetivo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $turmasPorCurso = Turma::query()->get()->groupBy('curso');

                foreach ($turmasPorCurso as $curso => $turmas) {
                    $turmasPorAno = $turmas->keyBy(
                        fn (Turma $turma): string => $this->normalizarSerie($turma->serie)
                    );

                    $primeiro = $turmasPorAno->get('1º');

                    if (! $primeiro) {
                        continue;
                    }

                    $segundo = $turmasPorAno->get('2º');
                    $terceiro = $turmasPorAno->get('3º');

                    if (! $segundo || ! $terceiro) {
                        throw new \RuntimeException(
                            "Curso {$curso}: turmas de 1º, 2º e 3º ano precisam estar cadastradas."
                        );
                    }

                    Aluno::query()
                        ->where('turma_id', $terceiro->id)
                        ->where('concluinte', false)
                        ->update([
                            'concluinte' => true,
                            'concluido_em' => $dataConclusao,
                        ]);

                    Aluno::query()
                        ->where('turma_id', $segundo->id)
                        ->where('concluinte', false)
                        ->update(['turma_id' => $terceiro->id]);

                    Aluno::query()
                        ->where('turma_id', $primeiro->id)
                        ->where('concluinte', false)
                        ->update(['turma_id' => $segundo->id]);
                }

                return true;
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Falha na progressão. A transação foi revertida.');

            return self::FAILURE;
        }

        if (! $processou) {
            $this->info("A progressão de {$anoLetivo} já foi executada.");

            return self::SUCCESS;
        }

        $this->info("Progressão do ano letivo {$anoLetivo} concluída.");

        return self::SUCCESS;
    }

    private function normalizarSerie(string $serie): string
    {
        return trim(str_replace('°', 'º', $serie));
    }
}
