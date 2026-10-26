<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Intervenção Disciplinar — {{ $ocorrencia->aluno->nome }}</title>
    <style>
        @page { size: A4 portrait; margin: 11mm 14mm 12mm; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { margin: 0; color: #101010; font: 10.5pt Arial, Helvetica, sans-serif; line-height: 1.2; }
        main { margin: 0 auto; max-width: 760px; }
        .toolbar { display: flex; justify-content: flex-end; margin: 0 auto 12px; max-width: 760px; }
        .toolbar button { border: 0; border-radius: 4px; background: #176b3a; color: #fff; cursor: pointer; font-size: 10pt; padding: 9px 14px; }
        .header { align-items: center; border-bottom: 1px solid #222; display: grid; grid-template-columns: 1fr 2.45fr 52px; gap: 12px; min-height: 62px; padding: 0 0 8px; }
        .government { align-items: center; display: flex; gap: 6px; min-width: 0; }
        .crest { flex: 0 0 42px; height: 58px; width: 42px; }
        .government-name { color: #343a40; font-family: Georgia, 'Times New Roman', serif; font-size: 13pt; font-variant: small-caps; font-weight: bold; line-height: .94; }
        .government-name small { color: #222; display: block; font-family: Georgia, serif; font-size: 6.3pt; font-style: italic; font-variant: normal; font-weight: normal; margin-top: 3px; }
        .project { font-size: 9pt; text-align: center; }
        .project strong { display: block; font-size: 9pt; letter-spacing: .16em; margin-bottom: 5px; white-space: nowrap; }
        .project span { font-size: 8pt; font-weight: bold; text-decoration: underline; white-space: nowrap; }
        .ppdt-mark { height: 49px; justify-self: end; width: 49px; }
        h1 { font-size: 15pt; letter-spacing: .04em; margin: 12px 0 20px; text-align: center; }
        .statement { line-height: 1.22; text-align: justify; }
        .write { border-bottom: 1px solid #222; display: inline-block; height: 1.1em; line-height: 1.05em; min-width: 75px; padding: 0 3px; text-align: center; vertical-align: baseline; }
        .w-sm { min-width: 48px; }
        .w-md { min-width: 100px; }
        .w-lg { min-width: 175px; }
        .w-xl { min-width: 250px; }
        .attitudes { margin: 12px 0 15px 24px; }
        .attitude { margin: 3px 0; }
        .mark-box { display: inline-block; min-width: 28px; }
        .section-title { margin: 13px 0 5px; }
        .ruled { background-image: linear-gradient(to bottom, transparent calc(100% - 1px), #222 0); background-size: 100% 20px; line-height: 20px; min-height: 80px; overflow-wrap: anywhere; padding: 0 2px; white-space: pre-wrap; }
        .ruled.measures { min-height: 60px; }
        .ruled.forwarding { min-height: 60px; }
        .frequency { display: flex; justify-content: space-between; margin: 5px 8px 17px; }
        .severity { margin-top: 5px; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 36px; text-align: center; }
        .signature-line { border-top: 1px solid #222; padding-top: 2px; }
        .signature-name { font-weight: bold; line-height: 1.1; }
        .signature-caption { line-height: 1.15; }
        @media print { .toolbar { display: none; } main { max-width: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Imprimir / Salvar como PDF</button></div>
    @php
        $atitudesSelecionadas = $ocorrencia->atitudes_tomadas ?? [];
        $atitudeMarcada = fn (string $codigo): string => in_array($codigo, $atitudesSelecionadas, true) ? 'X' : ' ';
        $frequencias = [
            'primeira_vez' => '1ª vez',
            'reincidente_pouco_frequente' => 'Reincidente (pouco frequente)',
            'reincidente_frequente' => 'Reincidente (frequente)',
        ];
        $gravidades = ['leve' => 'Leve', 'media' => 'Média', 'grave' => 'Grave', 'gravissima' => 'Gravíssima'];
    @endphp
    <main>
        <header class="header">
            <div class="government">
                <svg class="crest" viewBox="0 0 48 66" role="img" aria-label="Brasão do Estado do Ceará">
                    <path d="M8 9 13 3h22l5 6-4 5H12z" fill="#d7b84b" stroke="#7d6521" stroke-width="1.5"/>
                    <path d="M6 14h36l-3 30-15 17L9 44z" fill="#f4f0dc" stroke="#356944" stroke-width="3"/>
                    <path d="M10 17h14v20H9z" fill="#2875a8"/><path d="M24 17h13l-2 20H24z" fill="#e8d34b"/>
                    <path d="M10 37h14v17L11 43z" fill="#ce3e43"/><path d="M24 37h11l-1 7-10 10z" fill="#38864c"/>
                    <circle cx="24" cy="29" r="5" fill="#f5f1df" stroke="#27633d" stroke-width="1.5"/>
                    <path d="M24 24v10m-4-5h8" stroke="#27633d" stroke-width="1.4"/>
                </svg>
                <div class="government-name">GOVERNO DO<br>ESTADO DO CEARÁ<small>Secretaria da Educação</small></div>
            </div>
            <div class="project">
                <strong>PROJETO PROFESSOR DIRETOR DE TURMA – PPDT</strong>
                <span>ESCOLA: EEEP MONSENHOR LUÍS XIMENES FREIRE</span>
            </div>
            <svg class="ppdt-mark" viewBox="0 0 64 64" role="img" aria-label="Símbolo do PPDT">
                <rect x="1" y="1" width="30" height="30" rx="3" fill="#2788dc"/>
                <rect x="33" y="1" width="30" height="30" rx="3" fill="#079b69"/>
                <rect x="1" y="33" width="30" height="30" rx="3" fill="#ee3158"/>
                <rect x="33" y="33" width="30" height="30" rx="3" fill="#f2d51f"/>
                <path d="M13 42 31 47 51 41 49 52 32 58 14 52z" fill="#fff" stroke="#e4e4e4" stroke-width="1"/>
                <path d="M13 42 31 47v11L14 52zm38-1-20 6v11l18-6z" fill="#f8f8f8" stroke="#c7c7c7" stroke-width="1"/>
                <circle cx="32" cy="25" r="7" fill="#f7a627" stroke="#fff" stroke-width="2"/>
                <path d="M27 32h10l5 10-10 4-10-4z" fill="#f7a627" stroke="#fff" stroke-width="2"/>
            </svg>
        </header>

        <h1>REGISTRO DE INTERVENÇÃO DISCIPLINAR</h1>

        <div class="statement">
            <span class="write w-lg">{{ $ocorrencia->professor->nome }}</span>, professor(a) que leciona a disciplina
            <span class="write w-md">{{ $ocorrencia->disciplina?->nome }}</span>, vem, por este meio, informar ao(à) diretor(a) de turma do/da
            <span class="write w-md">{{ $ocorrencia->turma->curso }}</span>, ano/série
            <span class="write w-sm">{{ $ocorrencia->turma->serie }}</span>, da turma
            <span class="write w-md">{{ $ocorrencia->turma->serie }} — {{ $ocorrencia->turma->curso }}</span>, que o(a) aluno(a)
            <span class="write w-lg">{{ $ocorrencia->aluno->nome }}</span>, nº
            <span class="write w-sm">{{ $ocorrencia->aluno->numero }}</span>, teve um comportamento inadequado no dia
            <span class="write w-sm">{{ $ocorrencia->data_ocorrido?->format('d/m/Y') }}</span>, pelas
            <span class="write w-sm">{{ substr((string) $ocorrencia->hora_ocorrido, 0, 5) }}</span> horas, no(a)
            <span class="write w-md">{{ $ocorrencia->local_ocorrido }}</span>, local onde se encontrava. Em consequência disso, tomei a seguinte atitude:
        </div>

        <div class="attitudes">
            <div class="attitude"><span class="mark-box">( {{ $atitudeMarcada('ordem_saida') }} )</span> ordem de saída da sala de aula</div>
            <div class="attitude"><span class="mark-box">( {{ $atitudeMarcada('advertencia') }} )</span> advertência</div>
            <div class="attitude"><span class="mark-box">( {{ $atitudeMarcada('outro') }} )</span> outro(s): <span class="write w-xl">{{ $ocorrencia->outra_atitude }}</span></div>
        </div>

        <div class="section-title">Descrição da situação:</div>
        <div class="ruled">{{ $ocorrencia->descricao_situacao }}</div>

        <div class="section-title">O comportamento observado neste(a) aluno(a) é:</div>
        <div class="frequency">
            @foreach ($frequencias as $codigo => $rotulo)
                <span>( {{ $ocorrencia->frequencia === $codigo ? 'X' : ' ' }} ) {{ $rotulo }}</span>
            @endforeach
        </div>

        <div class="section-title">Tendo em vista a situação descrita acima, considero conveniente que o(a) diretor(a) de turma tome a(s) seguinte(s) medida(s):</div>
        <div class="ruled measures">{{ $ocorrencia->medidas_sugeridas }}</div>

        <div class="section-title">Encaminhamentos do(a) Professor(a) Diretor(a) de Turma:</div>
        <div class="ruled forwarding">{{ $ocorrencia->encaminhamento_diretor }}</div>
        <div class="severity"><strong>Classificação:</strong>
            @foreach ($gravidades as $codigo => $rotulo)
                <span style="margin-left: 12px">( {{ $ocorrencia->gravidade === $codigo ? 'X' : ' ' }} ) {{ $rotulo }}</span>
            @endforeach
        </div>

        <div class="signatures">
            <div>
                <div class="signature-line">&nbsp;</div>
                <div class="signature-name">{{ $ocorrencia->professor->nome }}</div>
                <div class="signature-caption">Professor(a)<br>(Encaminhado em: {{ $ocorrencia->data_encaminhamento?->format('d/m/Y') }})</div>
            </div>
            <div>
                <div class="signature-line">&nbsp;</div>
                <div class="signature-name">{{ $ocorrencia->turma->diretorTurma?->nome }}</div>
                <div class="signature-caption">Diretor(a) de Turma<br>(Recebido em: {{ $ocorrencia->classificado_em?->format('d/m/Y') }})</div>
            </div>
        </div>
    </main>
</body>
</html>
