<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Follow-up da proposta</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #111;">
    <p>{!! nl2br(e($body)) !!}</p>

    <hr>

    <p style="font-size: 12px; color: #666;">
        Negócio: <strong>{{ $deal->title }}</strong><br>
        Valor: {{ $deal->value ?? '—' }} €
    </p>
</body>
</html>
