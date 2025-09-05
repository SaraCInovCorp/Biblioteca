<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Livros</title>
</head>
<body>
    <h1>Relatório de Livros</h1>
    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Título</th>
                <th>Editora</th>
                <th>Autores</th>
                <th>Preço</th>
                <th>ISBN</th>
                <th>Bibliografia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($livros as $livro)
            <tr>
                <td>{{ $livro->titulo }}</td>
                <td>{{ $livro->editora->nome ?? '-' }}</td>
                <td>{{ $livro->autores->pluck('nome')->join(', ') }}</td>
                <td>{{ $livro->preco }}</td>
                <td>{{ $livro->isbn }}</td>
                <td>{{ $livro->bibliografia }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
