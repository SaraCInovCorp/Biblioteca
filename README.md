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

## Funcionalidade: Integração com API Google Books

Este sistema oferece integração avançada com a API pública do Google Books para enriquecer o cadastro e população de livros, autores e editoras.

- **Cadastro manual com preenchimento por API:**  
No formulário de cadastro manual de livros, o usuário pode buscar na API Google Books pelo título. O sistema exibe os resultados e permite preencher automaticamente o formulário com as informações reais do livro selecionado.

- **População automática via Seeder API:**  
Para facilitar testes e ambientes de desenvolvimento, o sistema possui um seeder específico (`LivroApiSeeder`) que importa dados reais da API Google Books, criando livros com seus autores e editoras relacionados, gerando fotos aleatórias para itens sem imagem.

- **População via Faker com Factory:**  
Existe também o seeder tradicional que usa factories para gerar dados fictícios realistas para testes, com fotos e informações aleatórias.

- **Configuração via variável de ambiente:**  
A seleção entre popular o banco com dados via API ou dados Faker ocorre automaticamente de acordo com a variável `SEEDER_TYPE` no arquivo `.env`.  
Exemplo:  
  - `SEEDER_TYPE=api` para usar a API Google Books  
  - `SEEDER_TYPE=faker` para usar dados Faker

- **Garantias:**  
Para campos obrigatórios com restrições no banco, como ISBN único, o sistema gera identificadores falsos únicos para garantir integridade dos dados.

---

## Funcionalidade: Importação de Livros via API Google Books

O sistema possui uma funcionalidade dedicada que permite importar livros diretamente da API pública do Google Books, simplificando o cadastro e enriquecendo o catálogo com dados reais.

### Como Funciona

- Através de uma interface própria, o usuário pode buscar livros por título, autor, editora, ISBN ou tema.
- Os resultados aparecem em cartões com capa, título, autores e editora, exibidos responsivamente em até 4 colunas conforme o tamanho da tela.
- O usuário pode selecionar um ou vários livros para importar.
- Na importação, o sistema cria automaticamente os registros de livros, autores e editoras, mantendo os relacionamentos pivot corretamente.
- Livros que tiverem ISBN ausente ou que já existam no banco são ignorados para evitar erros, sem interromper o processo.
- Ao final da importação, o sistema exibe uma lista dos livros que não foram importados e o motivo, permitindo que sejam cadastrados manualmente depois.

### Benefícios

- Reduz esforço manual e erros na criação dos registros.
- Utiliza informações oficiais e atualizadas da Google Books API.
- Interface intuitiva, responsiva e moderna para busca e seleção.
- Gerenciamento transparente de duplicidades e dados faltantes.

### Como Utilizar

- Disponível para usuários com permissão (ex.: administradores).
- Acesse a página de importação via menu ou rota: `/livros/import`.
- Busque pelos termos desejados e selecione os livros para importar.
- Visualize mensagens claras sobre livros não importados ao fim do processo.

### Considerações Técnicas

- Paginação incremental implementada na busca da API Google Books, permitindo carregar mais resultados ao usuário de forma dinâmica e responsiva, respeitando o limite padrão de 40 resultados por consulta da API.
- A funcionalidade integra-se com as policies do Laravel para controle de acesso.
- O campo ISBN é utilizado como identificador único para prevenir cadastros duplicados.

Este recurso facilita a manutenção e atualização do acervo, integrando informações reais e garantindo a qualidade dos dados do sistema.

### Funcionalidade: Listagem e Detalhe das Importações

- O sistema inclui uma página para visualizar todas as importações feitas pelo usuário.  
- Permite destaque da importação selecionada, com visualização dos livros importados.  
- Interface responsiva com paginação para melhor navegação.

### Funcionalidade: Exportação para Excel e PDF

- Exporta os livros tanto no contexto geral quanto filtrados por importação selecionada.  
- Suporta exportar via links específicos para cada importação, garantindo dados consistentes.  
- Exportações nos formatos Excel e PDF, com imagens tratadas e layout otimizado.  
- As rotas de exportação incluem parâmetros para filtrar livros conforme origem (importação, filtros gerais, etc).

---

## Banco de Dados e Relacionamentos

- Usa SQLite para armazenamento local simples e ágil.  
- Arquivo `database/database.sqlite` criado manualmente (vazio).  

O projeto possui uma modelagem robusta, com as seguintes tabelas e relacionamentos principais:

- **livros**  
  Armazena os livros com campos: `isbn`, `titulo`, `bibliografia`, `preco`, `capa_url`, `status`, e chave estrangeira `editora_id`.

- **autores**  
  Lista de autores, relacionados a livros via relacionamento muitos-para-muitos.

- **editoras**  
  Editoras vinculadas aos livros.

- **autor_livro** (pivot)  
  Relação muitos-para-muitos entre autores e livros.

- **importacoes**  
  Registros das importações feitas pelos usuários.

- **livro_importacao** (pivot)  
  Relação muitos-para-muitos entre livros e importações.

- **autor_importacao** (pivot)  
  Relação muitos-para-muitos entre autores e importações.

- **editora_importacao** (pivot)  
  Relação muitos-para-muitos entre editoras e importações.

- **book_requests**  
  Requisições feitas pelos usuários, contendo dados como usuário requisitante, datas e status.

- **book_request_items**  
  Ligação individual de livros a requisições, com status, data prevista e data real de entrega.

Essas tabelas pivot garantem a flexibilidade para associar múltiplos autores e editoras a livros e importações, além de armazenar o histórico completo das requisições.

---

## Funcionalidades Principais

- Gerenciamento completo de livros, autores e editoras.  
- Pesquisas complexas com inúmeros filtros (por título, autor, status, data, usuário).  
- Paginação eficiente utilizando o método `paginate` do Laravel.  
- Exportação flexível para Excel e PDF.  
- Sistema de requisição com limite para usuários cidadãos e gerenciamento de status.  
- Controle de acesso avançado via Laravel Jetstream com autenticação 2FA.  
- Interfaces server-rendered modernas com Blade e componentes reutilizáveis.

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

## Testes e População de Dados

- Factories configuradas para gerar dados realistas para livros, editoras e autores.  
- Seeders com opção de usar factorys ou integração com API do Google Books.
- Testes automatizados para validar relacionamentos e regras de negócio.

---

## Instalação

### 1. Clone o repositório:
```

git clone https://github.com/SaraCInovCorp/Biblioteca.git
cd biblioteca

```

### 2. Instale dependências PHP e JS:
```

composer install
npm install

```

### 3. Compile assets:
```

npm run build

```

### 4. Crie banco SQLite vazio:

- Linux/Mac:

```
touch database/database.sqlite

```

- Windows:

```

Crie manualmente um arquivo vazio `database.sqlite` na pasta `database`

```

### 5. Configure o `.env` para usar SQLite e escolha o tipo de seed:
```

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SEEDER_TYPE=api (Coloca api ou faker como prefere popular sua base)
BOOK_API_QUERY=laravel (Coloca o tema que o seeder vai popular a base)

```

### 6. Execute migrações e seeders:
```

php artisan migrate --seed

```

### 7. Inicie o servidor local (se aplicável):

Se estiver desenvolvendo localmente sem servidor web configurado, execute:
```
php artisan serve 

```

### 8. Acesse em 

```

http://nomedoprojeto.test 

```

(ou conforme configurado).

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

## Contato e Contribuições

Este projeto está aberto a sugestões e contribuições.

---

Se precisar de qualquer suporte adicional, estou à disposição!