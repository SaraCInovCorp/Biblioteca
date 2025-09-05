<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Editoras</title>
</head>
<body>
    <h1>Relatório de Editoras</h1>
    <table border="1" cellspacing="0" cellpadding="5" width="100%">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Logo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($editoras as $editora)
            <tr>
                <td>{{ $editora->nome }}</td>
                <td>
                    @if($editora->logo_url)
                        <img src="{{ Str::startsWith($editora->logo_url, ['http://','https://']) ? $editora->logo_url : public_path('storage/' . $editora->logo_url) }}" width="100" />
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
