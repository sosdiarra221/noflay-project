<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $titre }}</title>
    @include('locative.pdf.partials.quittance-style')
    <style>
        .doc-titre { text-align: center; border-bottom: 2px solid #1a3c8c; padding-bottom: 10px; margin-bottom: 18px; }
        .doc-titre h1 { font-size: 18px; color: #1a3c8c; margin: 0 0 4px 0; text-transform: uppercase; }
        .doc-titre p { font-size: 10px; color: #666; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .doc-corps { font-size: 12px; line-height: 1.6; color: #26324a; }
        .doc-corps h1 { font-size: 16px; color: #1a3c8c; }
        .doc-corps h2 { font-size: 13px; color: #1a3c8c; background-color: #eef2fb; padding: 6px 10px; margin: 16px 0 8px 0; }
        .doc-corps h3 { font-size: 12px; color: #1a3c8c; }
        .doc-corps p { text-align: justify; margin: 0 0 8px 0; }
        .doc-corps table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .doc-corps table td, .doc-corps table th { border: 1px solid #d8dfef; padding: 6px 8px; }
        .doc-corps table th { background-color: #f5f7fc; }
        .doc-corps hr { border: none; border-top: 1px solid #d8dfef; margin: 14px 0; }
        .doc-corps ul, .doc-corps ol { margin: 0 0 8px 20px; padding: 0; }
    </style>
</head>
<body>
    <div class="conteneur">
        @include('locative.pdf.partials.quittance-entete')

        <div class="doc-titre">
            <h1>{{ $titre }}</h1>
            @if ($sousTitre)
                <p>{{ $sousTitre }}</p>
            @endif
        </div>

        <div class="doc-corps">
            {!! $contenu !!}
        </div>

        @include('locative.pdf.partials.quittance-pied')
    </div>
</body>
</html>
