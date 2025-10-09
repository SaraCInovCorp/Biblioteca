<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo Laravel"></a></p>

# Sistema de Biblioteca e Loja de Livros - Gestão de Livros, Editoras e Autores

Sistema web desenvolvido com Laravel no backend e Blade no frontend, destinado a gerenciar de forma eficiente bibliotecas com controle completo de livros, editoras e autores, e funcionando também como uma loja online de livros com carrinho, checkout e histórico de compras.

---

## Tecnologias Utilizadas

- **Laravel 11+** — Framework PHP robusto e moderno para backend.  
- **Blade** — Template engine nativo do Laravel para renderização server-side.  
- **Tailwind CSS & DaisyUI** — Estilização moderna com componentes reutilizáveis.  
- **Alpine.js** — Biblioteca leve para interatividade frontend em páginas Blade, facilitando a criação de interfaces modernas sem frameworks pesados.
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

- **livros** — armazena os livros com campos: `isbn`, `titulo`, `bibliografia`, `preco`, `capa_url`, `status`, e chave estrangeira `editora_id`.  
- **autores** — lista de autores, relacionados a livros via relacionamento muitos-para-muitos.  
- **editoras** — editoras vinculadas aos livros.  
- **autor_livro** (pivot) — relação muitos-para-muitos entre autores e livros.  
- **importacoes** — registros das importações feitas pelos usuários.  
- **livro_importacao** (pivot) — relação muitos-para-muitos entre livros e importações.  
- **autor_importacao** (pivot) — relação muitos-para-muitos entre autores e importações.  
- **editora_importacao** (pivot) — relação muitos-para-muitos entre editoras e importações.  
- **book_requests** — requisições feitas pelos usuários, contendo dados como usuário requisitante, datas e status.  
- **book_request_items** — ligação individual de livros a requisições, com status, data prevista e data real de entrega.

Essas tabelas pivot garantem a flexibilidade para associar múltiplos autores e editoras a livros e importações, além de armazenar o histórico completo das requisições.

---

## Funcionalidades Principais

- Gerenciamento completo: Adicione, edite, pesquise e remova livros, autores e editoras por uma interface intuitiva, com relacionamentos automáticos entre entidades.  
- Filtros e busca avançada: Pesquise por título, autor, editora, status, data de cadastro ou usuário requisitante.  
- Paginação eficiente: Exibe resultados de forma paginada usando `paginate` do Laravel.  
- Exportação de dados: Excel e PDF com suporte a imagens e layout personalizado.  
- Sistema de requisição: Usuários solicitam empréstimo de livros, com limites configuráveis e acompanhamento em tempo real.  
- Criação e gerenciamento de reviews: Usuários avaliam livros e administradores podem revisar, aprovar ou alterar status.  
- Lista de espera inteligente e notificações automáticas.  
- Controle de acesso robusto com Laravel Jetstream, incluindo 2FA.  
- Interface moderna usando Blade, Tailwind CSS e DaisyUI.  
- Loja online integrada ao Stripe, com carrinho, checkout e histórico de compras.  
- Registro detalhado de atividades no sistema para auditoria e monitoramento.

Essas funcionalidades garantem uma gestão profissional, moderna e segura para acervos de bibliotecas digitais ou físicas, trazendo automação e inteligência para operações do dia a dia do usuário e do administrador.

---

## Processo de Requisição de Livros

- **Criação da Requisição:** Usuários criam requisições selecionando livros disponíveis.  
  - Cidadãos têm limite de 3 livros simultâneos.  
  - Data de início não pode ser retroativa.  
- **Itens da Requisição:** Cada livro vira um item com status `'realizada'`.  
- **Gerenciamento:** Edição de status, data real de entrega e dias decorridos.  
- **Validação:** Verifica disponibilidade ao criar requisições.  
- **Cancelamento:** Permitido apenas antes da data de início.  
- **Detalhamento:** Visualização completa do usuário, livros e status por item.

---

## Testes Automatizados com Pest

Este projeto utiliza **Pest** como framework de testes, substituindo os testes unitários padrão do Laravel.

### 1. Instalando o Pest

```
composer require pestphp/pest --dev
composer require pestphp/pest-plugin-laravel --dev
php artisan pest:install
```

Isso instalará o Pest e criará a estrutura inicial de testes (tests/Feature e tests/Unit) pronta para uso.

### 2. Convertendo testes existentes do PHPUnit

Se você já possui testes em PHPUnit, é possível converter rapidamente para Pest usando o drift:

```
composer require pestphp/drift --dev
./vendor/bin/drift
```
O Drift analisa seus testes PHPUnit existentes e gera versões equivalentes em Pest, preservando toda a lógica e asserts.

**Exemplo de conversão:**

- Antes (PHPUnit):

```
public function test_example()
{
    $this->assertTrue(true);
}
```

- Depois (Pest):

```
it('passes an example test', function () {
    expect(true)->toBeTrue();
});
```

### 3. Estrutura de Testes

- tests/Unit: Testes unitários de modelos, factories, e regras de negócio.

- tests/Feature: Testes de integração e funcionalidades completas, como rotas, autenticação e Livewire.

Agora todos os testes neste projeto foram convertidos para Pest, garantindo sintaxe moderna e legível.

### 4. Plugins Oficiais do Pest

O Pest possui uma série de plugins oficiais que adicionam funcionalidades extras, como testes de cobertura, testes de Livewire, HTTP e mais:

- [Documentação de Plugins do Pest](https://pestphp.com/docs/plugins)

**Exemplos de plugins úteis para Laravel:**

- **pest-plugin-laravel**: Integrações adicionais com Laravel.  
- **pest-plugin-livewire**: Facilita testes de componentes Livewire.  
- **pest-plugin-expectations**: Sintaxe mais expressiva para asserts.  

Para instalar um plugin, basta usar:

```
composer require <plugin-name> --dev
```
### 5. Executando os testes

```
php artisan test
# ou
./vendor/bin/pest
```
- Para rodar testes específicos:

```
./vendor/bin/pest --filter=UpdatePasswordTest
```

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

SEEDER_TYPE=api # ou faker - como prefere popular sua base para teste
BOOK_API_QUERY=laravel  # tema que o seeder vai popular a base para teste

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

---

## Como configurar Stripe para testes e desenvolvimento

### 1. Instale a SDK Stripe no Laravel

No terminal, execute:

```
composer require stripe/stripe-php
```


Essa dependência instala a API oficial do Stripe para PHP, usada para integrar e processar cobranças de forma segura e robusta.

---

### 2. Configure as credenciais no `.env`

Acesse o painel do Stripe em [https://dashboard.stripe.com/test/apikeys](https://dashboard.stripe.com/test/apikeys), copie suas chaves de teste e adicione ao `.env` do Laravel:

```
STRIPE_KEY=pk_test_seuTokenAqui
STRIPE_SECRET=sk_test_seuTokenAqui
```


Recarregue a aplicação após alterar o `.env`.

---

### 3. Requisitos para ambiente de testes

- Em desenvolvimento, o Stripe permite uso em `http://localhost` ou dominios `.test`.
- Para produção, é obrigatório usar HTTPS.

---

### 4. Realize pagamentos de teste

Use os dados oficiais de cartão fornecidos pelo Stripe para simular compras (exemplo: 4000 0566 5566 5556, qualquer data/CCV).

---

### 5. Ative métodos de pagamento

Pela dashboard Stripe, você pode ativar ou desativar métodos de pagamento locais (como Bancontact, Klarna, etc) conforme desejar.

---

### 6. Configuração frontend

No Blade de pagamento, carregue o Stripe JS:

```
<script src="https://js.stripe.com/v3/"></script>
```


Garanta que a inicialização está passando o `clientSecret` gerado pelo backend:

```
const stripe = Stripe("{{ env('STRIPE_KEY') }}");
const elements = stripe.elements({clientSecret: "{{ $clientSecret }}"});
```

A confirmação do pagamento é feita sempre passando o clientSecret recebido no controller.

---

### 7. Observações importantes

- O webhook Stripe pode ser configurado posteriormente para processamento automático de pagamentos assíncronos. Em ambiente de teste, isso não é obrigatório para o fluxo básico (checkout + confirmação automática).
- Em produção, é necessário registrar o domínio na Stripe e ativar métodos Apple Pay/Google Pay se for usar.

---

## Limpeza da Base Stripe para Ambiente de Teste

Durante o desenvolvimento, é comum executar comandos como `php artisan migrate:fresh` para resetar a base de dados SQLite local. Contudo, a base de dados Stripe em modo teste pode acumular clientes, pagamentos e cobranças antigos que causam conflito ao executar novos testes.

Para garantir validações corretas e evitar problemas com dados "presas" ou emails antigos aparecendo no Stripe, é necessário limpar a base Stripe de teste regularmente.

### Como limpar a base Stripe de teste

Este projeto inclui um comando Artisan especial para facilitar essa limpeza automática:

```
php artisan stripe:limpar-testdata
```


Esse comando executa as seguintes ações:

- Cancela todos os PaymentIntents que ainda podem ser cancelados.
- Reembolsa todas as cobranças (Charges) ativas na base de teste.
- Remove todos os clientes (Customers) que forem deletáveis.

### Importante

- Execute esse comando **apenas em ambiente de desenvolvimento ou teste**, nunca em produção.
- Caso você reinicie sua base SQLite local, execute a limpeza do Stripe para evitar inconsistência de ids de usuário e clientes.
- O comando gerencia falhas comuns, como PaymentIntents já cancelados ou cobranças já reembolsadas, para dar feedback útil no terminal.

---

## Funcionalidade: Loja Online com Integração Stripe

Este sistema possui um módulo completo de loja online, integrado ao Stripe para gerenciamento e processamento de pagamentos de forma segura e moderna.

### Principais recursos implementados:

- **Carrinho de Compras Dinâmico:**  
Usuários autenticados podem adicionar livros ao carrinho, alterar quantidades e remover itens.  
O carrinho é persistente, vinculado ao usuário, permitindo continuidade em sessões distintas.

- **Checkout Guiado e Seguro:**  
O fluxo de checkout inclui seleção de endereço de entrega, resumo completo dos itens e confirmação final antes do pagamento.  
Utiliza o Stripe PaymentElement, que suporta múltiplos métodos de pagamento, incluindo cartões internacionais e métodos locais como Bancontact e Klarna.

- **Processamento e Atualização de Pagamentos:**  
Pagamentos são realizados via Stripe PaymentIntent, com associação clara do usuário autenticado e do pedido.  
Após confirmação do pagamento, o sistema atualiza automaticamente o status do pedido para 'pago' e altera o carrinho para status finalizado.

- **Visualização Completa de Pedidos:**  
Usuários podem consultar o histórico de pedidos feitos, com detalhes completos de itens, valores e informações de endereço.

- **Robustez no Controle de Pedidos:**  
Cada novo pagamento gera uma nova encomenda, evitando sobrescrever pedidos pendentes ou históricos.  
Pedidos com status pendente permanecem até serem pagos ou expurgados conforme regras administrativas.

### Benefícios para o usuário e desenvolvedor

- Experiência fluida e confiável para o cliente final, com UI moderna e responsiva.  
- Segurança e conformidade garantidas pelo Stripe, incluindo suporte a múltiplos métodos de pagamento e proteção PCI.  
- Facilidade de manutenção e testes, com comandos para resetar o ambiente Stripe em desenvolvimento.

---


Com esses passos, o sistema já estará preparado e seguro para processar pagamentos em modo desenvolvimento, sendo facilmente adaptável para produção apenas trocando as chaves no `.env` e colocando o domínio em HTTPS.

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