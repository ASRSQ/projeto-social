<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ocorrencia-{{ $codigoOcorrencia->codigo }} - Registro de Intervenção Disciplinar</title>
    <style>
        :root { --ink: #111; --line: #333; --muted: #555; --paper: #fff; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        html, body { margin: 0; padding: 0; background: #e9e9e9; color: var(--ink); font-family: Arial, Helvetica, sans-serif; font-size: 10pt; line-height: 1.25; }
        .toolbar { width: 210mm; margin: 12px auto; display: flex; justify-content: flex-end; }
        .toolbar button { border: 1px solid #222; border-radius: 4px; background: #fff; padding: 9px 14px; font-size: 14px; cursor: pointer; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto 16px; padding: 12mm 13mm 10mm; background: var(--paper); box-shadow: 0 2px 12px rgba(0,0,0,.15); }
        .header { display: grid; grid-template-columns: 34mm minmax(0, 1fr) 16mm; align-items: start; gap: 3mm; padding-bottom: 8px; border-bottom: 1.5px solid var(--ink); margin-bottom: 12px; }
        .header-logo { display: block; width: 100%; height: auto; object-fit: contain; }
        .header-center { text-align: center; padding-top: 2mm; }
        .project { font-weight: 700; font-size: 8.5pt; letter-spacing: .25px; margin-bottom: 5px; }
        .school { font-weight: 700; font-size: 8pt; margin-bottom: 10px; }
        h1 { margin: 0; font-size: 15pt; letter-spacing: .45px; text-transform: uppercase; }
        .intro { margin: 0 0 10px; text-align: justify; line-height: 1.55; }
        .field { display: inline-block; min-height: 1.15em; border-bottom: 1px solid var(--line); vertical-align: baseline; padding: 0 3px; text-align: center; font-weight: 700; position: relative; top: .08em; }
        .w-professor { min-width: 48mm; } .w-discipline { min-width: 35mm; } .w-series { min-width: 15mm; } .w-class { min-width: 34mm; } .w-student { min-width: 53mm; } .w-number { min-width: 10mm; } .w-date { min-width: 24mm; } .w-time { min-width: 15mm; } .w-place { min-width: 42mm; }
        .section { margin-top: 9px; break-inside: avoid; }
        .section-title { font-weight: 700; margin: 0 0 5px; }
        .choices { display: flex; flex-wrap: wrap; column-gap: 16px; row-gap: 5px; padding-left: 3px; }
        .choice { white-space: nowrap; }
        .boxmark { display: inline-flex; align-items: center; justify-content: center; width: 11px; height: 11px; border: 1px solid #111; margin-right: 5px; font-size: 9px; line-height: 1; }
        .write-area { border: 1px solid var(--line); width: 100%; margin-top: 4px; padding: 0 1.5mm; white-space: pre-wrap; overflow-wrap: anywhere; font-weight: 700; line-height: 7.35mm; background-image: repeating-linear-gradient(to bottom, transparent 0, transparent 7.2mm, #b9b9b9 7.2mm, #b9b9b9 7.35mm); }
        .description-area { min-height: 35mm; } .measures-area { min-height: 22mm; } .referral-area { min-height: 25mm; }
        .severity { margin-top: 6px; }
        .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18mm; margin-top: 13mm; break-inside: avoid; }
        .signature { text-align: center; border-top: 1px solid var(--ink); padding-top: 5px; min-height: 12mm; }
        .signature strong { display: block; font-size: 9.5pt; }
        .signature span { display: block; font-size: 8.5pt; margin-top: 4px; }
        .footer-note { margin-top: 7px; color: var(--muted); font-size: 8pt; text-align: center; }
        @page { size: A4 portrait; margin: 0; }
        @media print {
            html, body { width: 210mm; min-height: 297mm; background: #fff; font-size: 9.5pt; }
            .toolbar { display: none !important; }
            .page { width: 210mm; min-height: 297mm; margin: 0; padding: 11mm 13mm 9mm; box-shadow: none; }
            .section, .signature-grid, .header { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Imprimir / Salvar como PDF</button></div>
    @include('ocorrencias._formulario', ['ocorrencia' => $ocorrencia])
</body>
</html>
