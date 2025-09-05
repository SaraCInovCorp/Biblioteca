<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo Laravel"></a></p>

# Sistema de Biblioteca - Gestão de Livros, Editoras e Autores

Sistema web desenvolvido com Laravel no backend e Blade no frontend, destinado a gerenciar de forma eficiente bibliotecas com controle completo de livros, editoras e autores.

***

## Tecnologias Utilizadas

- **Laravel 11+** — Framework PHP robusto e moderno para backend.
- **Blade** — Template engine nativo do Laravel para renderização server-side.
- **Tailwind CSS \& DaisyUI** — Estilização moderna com componentes reutilizáveis.
- **Laravel Jetstream** — Sistema de autenticação completo, incluindo autenticação em dois fatores (2FA).
- **Maatwebsite Excel \& Barryvdh Dompdf** — Exportação para Excel e PDF, incluindo suporte a imagens e layout paisagem.
- **SQLite** — Banco de dados leve, configurado para fácil uso em desenvolvimento local e testes.
- **Git \& GitHub** — Controle de versão e colaboração eficiente.

***

## Funcionalidades Principais

- Cadastro, edição e exclusão de livros, editoras e autores.
- Pesquisa e filtros avançados para localização rápida dos registros.
- Paginação simples e eficiente usando o método `paginate` do Laravel, com navegação por botões Next/Back.
- Exportação para Excel e PDF com suporte a imagens locais; imagens externas indicadas com mensagem “Imagem indisponível”.
- Geração de PDFs em formato paisagem para melhor visualização de tabelas extensas.
- Autenticação segura com Laravel Jetstream, incluindo suporte a autenticação em dois fatores (2FA).
- Interface server-rendered com Blade, garantindo simplicidade, desempenho e facilidade de manutenção.

***

## Banco de Dados

- Utiliza SQLite para armazenamento local simples e ágil.
- O arquivo do banco `database/database.sqlite` deve ser criado manualmente (arquivo vazio).
- Configuração padrão do Laravel já preparada para facilitar uso com SQLite.

***

## Testes e População de Dados

- Criadas **factories completas** para modelos Livro, Editora e Autor, permitindo geração de dados realistas para testes.
- Implementados testes automatizados para validar relacionamentos entre livros, autores e editoras, garantindo integridade dos dados.
- Facilita ambiente robusto para desenvolvimento e testes confiáveis.

***

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/SaraCInovCorp/Biblioteca.git
cd biblioteca
```

2. Instale dependências PHP e JavaScript:
```bash
composer install
npm install
```

3. Compile os assets CSS e JS:
```bash
npm run build
```

4. Crie o arquivo SQLite vazio:
```bash
touch database/database.sqlite
```

(se estiver no Windows, crie este arquivo manualmente na pasta database)

5. Configure seu arquivo `.env`, conferindo que está com:
```
DB_CONNECTION=sqlite
DB_DATABASE=/caminho/absoluto/para/database/database.sqlite
```

Ou, para desenvolvimento local, algo como:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

6. Execute migrações e seeders populando o banco:
```bash
php artisan migrate --seed
```

7. Inicie o servidor local:
```bash
php artisan serve
```

8. Acesse via navegador `bibliote.test`.

***

## Instalação do Laravel Jetstream com Livewire

Utilizamos Laravel Jetstream para autenticação. A instalação foi feita conforme a documentação oficial:

```bash
composer require laravel/jetstream

php artisan jetstream:install livewire
```

Para suporte a equipes:

```bash
php artisan jetstream:install livewire --teams
```

Depois instale dependências NPM e compile assets:

```bash
npm install
npm run build
```

E finalize com migrações:

```bash
php artisan migrate
```


***

## Exportação

- Exportação integrada para Excel e PDF.
- Suporte a imagens locais e tratamento de URLs externas.
- PDF gerado em modo paisagem para maior legibilidade.

***

## Autenticação em Dois Fatores (2FA)

- Ativação e gerenciamento de 2FA pela área do perfil do usuário.
- Aumento significativo da segurança para acesso ao sistema.

***

## Estrutura das Views com Blade

- Layouts e views organizados em `resources/views` usando herança e componentes Blade.
- Uso extensivo de diretivas Blade para modularidade e eficiência.

***

## Contato e Contribuições

O projeto está aberto para contribuições e melhorias. 

***