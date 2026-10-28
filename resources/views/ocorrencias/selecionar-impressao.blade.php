<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selecionar ocorrências para impressão</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 16px; background: #f3f4f6; color: #111827; font-family: Arial, Helvetica, sans-serif; }
        main { width: min(760px, 100%); margin: 0 auto; padding: 24px; background: white; border: 1px solid #e5e7eb; border-radius: 10px; }
        h1 { margin: 0 0 8px; font-size: 22px; }
        p { margin: 0 0 20px; color: #4b5563; }
        .list { display: grid; gap: 8px; max-height: 60vh; overflow: auto; margin-bottom: 20px; }
        label { display: flex; gap: 10px; align-items: flex-start; padding: 12px; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; }
        label:hover { background: #f9fafb; }
        input { margin-top: 2px; }
        button { border: 0; border-radius: 6px; padding: 10px 16px; background: #1f2937; color: white; font-size: 14px; cursor: pointer; }
        button:disabled { opacity: .5; cursor: not-allowed; }
        .empty { padding: 16px; color: #6b7280; background: #f9fafb; border-radius: 6px; }
    </style>
</head>
<body>
    <main>
        <h1>Imprimir mais de uma ocorrência</h1>
        <p>Selecione pelo menos duas ocorrências. A impressão organizará duas por folha em paisagem.</p>
        <form method="get" action="{{ route('ocorrencias.imprimir-lote') }}">
            <div class="list">
                @forelse ($ocorrencias as $ocorrencia)
                    <label>
                        <input type="checkbox" name="ids[]" value="{{ $ocorrencia->id }}">
                        <span>{{ $ocorrencia->data_ocorrido?->format('d/m/Y') ?? 'Sem data' }} — {{ $ocorrencia->aluno?->nome ?? 'Aluno sem nome' }} — {{ $ocorrencia->turma?->serie }} - {{ $ocorrencia->turma?->curso }}</span>
                    </label>
                @empty
                    <div class="empty">Não há ocorrências classificadas disponíveis para impressão.</div>
                @endforelse
            </div>
            <button type="submit" @disabled($ocorrencias->count() < 2)>Abrir impressão</button>
        </form>
    </main>
    <script>
        const form = document.querySelector('form');
        const submit = form.querySelector('button[type="submit"]');
        const updateSubmit = () => {
            submit.disabled = form.querySelectorAll('input[name="ids[]"]:checked').length < 2;
        };
        form.addEventListener('change', updateSubmit);
        updateSubmit();
    </script>
</body>
</html>
