<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Autores</title>
</head>
<body>
<h1>Relatório de Autores</h1>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Foto</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($autores as $autor)
        <tr>
            <td>{{ $autor->nome }}</td>
            <td>
                @if($autor->foto_url)
                    <img src="{{ Str::startsWith($autor->foto_url, ['http://','https://']) ? $autor->foto_url : public_path('storage/' . $autor->foto_url) }}" width="100" />
                @else
                    -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
