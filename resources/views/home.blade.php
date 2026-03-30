<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Teste</title>
</head>
<body>
    <h1>Ola</h1>
    <p>Meu nome é {{ $name }}</p>
    <h2>Meus hábitos</h2>
    <ul>
        @foreach ($habits as $habit)
            <li>{{ $habit }}</li>
        @endforeach
    </ul>
</body>
</html>
