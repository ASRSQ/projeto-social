<div class="space-y-4">
    @if ($alunos->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Não há alunos cadastrados nesta turma.
        </p>
    @else
        <div style="overflow-x: auto; border: 1px solid #d1d5db; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr>
                        <th style="background-color: #15803d; color: #fff; border: 1px solid #166534; padding: 12px 16px; font-weight: 600;">Nº chamada</th>
                        <th style="background-color: #15803d; color: #fff; border: 1px solid #166534; padding: 12px 16px; font-weight: 600;">Nome</th>
                        <th style="background-color: #15803d; color: #fff; border: 1px solid #166534; padding: 12px 16px; font-weight: 600;">Matrícula</th>
                        <th style="background-color: #15803d; color: #fff; border: 1px solid #166534; padding: 12px 16px; font-weight: 600;">Situação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($alunos as $aluno)
                        <tr>
                            <td style="border: 1px solid #d1d5db; padding: 10px 16px;">{{ $aluno->numero }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 10px 16px;">{{ $aluno->nome }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 10px 16px;">{{ $aluno->matricula }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 10px 16px;">
                                @if ($aluno->concluinte)
                                    Concluinte{{ $aluno->concluido_em ? ' em '.$aluno->concluido_em->format('Y') : '' }}
                                @else
                                    Ativo
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
