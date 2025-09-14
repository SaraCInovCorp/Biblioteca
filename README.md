

```markdown
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo Laravel"></a></p>

# Sistema de Biblioteca - Gestão de Livros, Editoras e Autores

Sistema web desenvolvido com Laravel no backend e Blade no frontend, destinado a gerenciar de forma eficiente bibliotecas com controle completo de livros, editoras e autores.

---

## Tecnologias Utilizadas

- **Laravel 11+** — Framework PHP robusto e moderno para backend.  
- **Blade** — Template engine nativo do Laravel para renderização server-side.  
- **Tailwind CSS & DaisyUI** — Estilização moderna com componentes reutilizáveis.  
- **Laravel Jetstream** — Sistema de autenticação completo, incluindo autenticação em dois fatores (2FA).  
- **Maatwebsite Excel & Barryvdh Dompdf** — Exportação para Excel e PDF, incluindo suporte a imagens e layout paisagem.  
- **SQLite** — Banco de dados leve, configurado para fácil uso em desenvolvimento local e testes.  
- **Git & GitHub** — Controle de versão e colaboração eficiente.  

---

## Funcionalidades Principais

- Cadastro, edição e exclusão de livros, editoras e autores.  
- Pesquisa e filtros avançados para localização rápida dos registros, incluindo:  
  - Pesquisa por título do livro e nome do autor.  
  - Filtros por status da requisição (ativa/inativa) e status dos itens da requisição (`cancelada`, `realizada`, `entregue_ok`, etc).  
  - Filtros por datas: data da requisição, data prevista de entrega e data real de entrega, com possibilidade de busca exata ou intervalo.  
  - Filtro por usuário (apenas para administradores).  
- Paginação simples e eficiente usando método `paginate` do Laravel.  
- Exportação integrada para Excel e PDF, com tratamento adequado para imagens locais e URLs externas.  
- Geração de PDFs em modo paisagem para melhor visualização.  
- Autenticação segura com Laravel Jetstream, suporte a dois fatores (2FA), e controle de perfis (admin/cidadão).  
- Interface server-rendered com Blade, garantindo simplicidade e desempenho.  
- Listagem detalhada da requisição, com dados individuais por item, incluindo data real de entrega, dias decorridos e status específico do item.  

---

## Processo de Requisição de Livros

O sistema implementa um fluxo completo para o processo de requisição de livros por usuários (cidadãos) e administradores:

- **Criação da Requisição:**  
  Usuários criam requisições selecionando um ou mais livros disponíveis, podendo adicionar notas.  
  - Cidadãos têm limite máximo de 3 livros requisitados simultaneamente para controle.  
  - Data de início não pode ser retroativa para cidadãos.

- **Itens da Requisição:**  
  Cada livro requisitado vira um item com status inicial `'realizada'` e data real de entrega vazia.  

- **Gerenciamento:**  
  Requisições e itens podem ser editados, alterando status, data real de entrega e dias decorridos.  

- **Validação:**  
  Ao criar, verifica-se disponibilidade dos livros.  

- **Cancelamento:**  
  Só permitido antes da data início da requisição, marcando requisição como inativa, livros como disponíveis e itens como cancelados.  

- **Detalhamento:**  
  Visualização exibe dados do usuário (para admin), detalhes da requisição e lista de livros com informações específicas por item.

---

## Banco de Dados

- Usa SQLite para armazenamento local simples e ágil.  
- Arquivo `database/database.sqlite` criado manualmente (vazio).  
- Modelos estruturados para relacionamentos entre livros, autores, editoras, usuários, requisições e itens.  

---

## Testes e População de Dados

- Factories configuradas para gerar dados realistas para livros, editoras e autores.  
- Testes automatizados para validar relacionamentos e regras de negócio.

---

## Instalação

1. Clone o repositório:
```

git clone https://github.com/SaraCInovCorp/Biblioteca.git
cd biblioteca

```

2. Instale dependências PHP e JS:
```

composer install
npm install

```

3. Compile assets:
```

npm run build

```

4. Crie banco SQLite vazio:
```

touch database/database.sqlite

```
(no Windows, crie manualmente no diretório `database`)

5. Configure `.env` para SQLite:
```

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

```

6. Execute migrações e seeders:
```

php artisan migrate --seed

```

7. Inicie o servidor:
```

php artisan serve

```

8. Acesse em http://localhost:8000 (ou conforme configurado).

---

## Instalação do Laravel Jetstream com Livewire

```

composer require laravel/jetstream
php artisan jetstream:install livewire
npm install
npm run build
php artisan migrate

```

---

## Exportação

- Excel e PDF com suporte a imagens locais e controle sobre URLs externas.  
- PDF em modo paisagem para maior legibilidade de tabelas.

---

## Autenticação em Dois Fatores (2FA)

- Habilitação via perfil do usuário.  
- Segurança reforçada no sistema.

---

## Estrutura das Views com Blade

- Views e layouts organizados para modularidade.  
- Uso extensivo de componentes Blade e diretivas.

---

## Filtros Avançados

- Pesquisa integrada para livro e autor.  
- Filtros combinados por status da requisição, status dos itens, data da requisição, previsão e data real de entrega.  
- Controle por usuário para administradores.

---

## Contato e Contribuições

Este projeto está aberto a sugestões e contribuições. Para reportar problemas, solicitar recursos ou enviar pull requests, utilize o GitHub.

---

Se precisar de ajuda para melhorias futuras, integração frontend/backend ou depuração, estamos à disposição!
```

Se precisar de arquivo `.md` completo ou outro formato, é só avisar!

