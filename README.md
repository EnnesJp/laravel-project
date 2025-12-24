# Plataforma de Pagamento

Esse projeto tem como objetivo fazer uma simples plataforma de pagamento, onde é possível receber e transferir créditos entre usuários.

## Decisões Técnicas

### Tech Stack

Para o desenvolvimento deste exercício, a stack escolhida foi **PHP** com **Laravel**, devido à maior familiaridade do desenvolvedor. Além disso, foi utilizado **Docker** para a containerização da aplicação, facilitando a reprodução do ambiente de execução.

#### Tecnologias

- **Redis** - Utilizado para gerenciamento de cache e filas
- **Larastan** - Análise estática de código para Laravel
- **PHPMD** - PHP Mess Detector para detecção de problemas no código
- **Pint** - Code style fixer para PHP
- **Telescope** - Ferramenta de debug para projetos Laravel
- **Horizon** - Dashboard e configuração para filas Redis
- **Sanctum** - Sistema de autenticação API para SPAs e aplicações móveis
- **Pest** - Framework de testes elegante e minimalista
- **Scramble** - Laravel package para gerar documentação de API

### Arquitetura

O projeto utiliza uma abordagem **Domain-Driven Design (DDD)** para organizar o código de forma mais limpa e maintível, separando as responsabilidades por domínios de negócio.

### Domain-Driven Organization

```
app/
├── Adapters/                    #  Adapters para serviços externos globais do app
│   ├── Contracts/               #  Interfaces para serviços externos
│   └── Mocks/                   #  Mocks para serviços externos
├── Domains/ 
│   ├── Transaction/             #  Domínio de transações financeiras
│   │   ├── Adapters/
│   │   │   ├── Contracts/
│   │   │   └── Mocks/
│   │   ├── DTOs/
│   │   ├── Enums/
│   │   ├── Events/
│   │   ├── Exceptions/
│   │   ├── Listeners/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Repositories/        #  Repositórios para conexão com banco de dados do domínio de transações financeiras
│   │   │   └── Contracts/
│   │   ├── Resources/
│   │   └── Services/            #  Lógicas de negócio do domínio de transações financeiras
│   └── User/                    #  Domínio de usuários
│       ├── DTOs/
│       ├── Enums/
│       ├── Models/
│       ├── Repositories/        #  Repositórios para conexão com banco de dados do domínio de usuários
│       │   └── Contracts/
│       ├── Resources/
│       └── Services/            #  Lógicas de negócio do domínio de usuários
├── Http/                        #  Controllers para requisições HTTP
└── Repositories/                #  Repositórios globais do app
    └── Contracts/               #  Interfaces para repositórios globais
```

Além da separação de responsabilidades, essa arquitetura também traz outros benefícios para a aplicação. O primeiro deles é a maior testabilidade e facilidade de manutenção: a separação clara de responsabilidades permite que cada trecho seja testado de forma unitária e independente, e a estrutura simples possibilita um fácil entendimento da responsabilidade de cada parte do código, permitindo que novos desenvolvedores realizem a manutenção sem dificuldades.

Outro ponto importante a ser ressaltado é a escalabilidade. A arquitetura desenvolvida permite a adição de novas funcionalidades sem gerar impactos nas já existentes, além de manter o sistema aberto para extensão.

### Estrutura do Banco de Dados

O banco de dados utilizado foi o **MySQL**. O projeto utiliza o **Eloquent ORM** para realizar as operações de CRUD no banco de dados, além de utilizar o **Migrations** para gerenciar as alterações no banco de dados.

A estrutura do banco de dados é simples, contendo apenas cinco tabelas e uma view para facilitar a busca de dados espacíficos:

**OBS:** não estão sendo consideradas aqui as tabelas criadas pelo Laravel

- **users** - Tabela de usuários

- **transactions** - Tabela de transações financeiras
- **credits** - Tabela de créditos
- **debits** - Tabela de débitos
- **fund_debits** - Tabela de débitos de fundos externos

- **remaining_credits** - View para buscar apenas saldos que ainda não foram totalmente utilizados

Na tabela de `transactions` é possível ver todas as transações financeiras realizadas, tanto de transferencias quanto de depositos. Além disso, temos também os IDs do usuário pagador e daquele que está recebendo o valor.

Toda transação gera um debito na conta do usuário pagador e um credito na conta do usuário que está recebendo o valor. No caso de uma transferencia, portanto, são gerados dois novos registros, um na tabela de `debits` e um na tabela de `credits`. Para permitir uma maior rastreabilidade o debito é lincado ao credito que foi utilizado na transação, isso permite com que possamos identificar com maior facilidade caso haja algum erro no saldo do usuário rastrando os creditos que ele já recebeu e todas as suas utilizações. Já no caso de uma transação de deposito vindo de uma fonte externa, não existe esse rastreio pois não há um crétido sendo utilizado, por isso, ao realizar um deposito é gerado um registro na tabela de `credito` assim como na transferencia e um registro na tabela de `found_debits` que indica uma entrada de credito no app vindo de uma fonte externa.

Com essa estrutura é possível garantir a consistência dos dados, rastreabilidade de uso para facilitar investigações em casos de erro e garantia contábil de que a soma de todos os créditos e debitos será sempre zero.

Por fim, a tabela de `remaining_credits` é uma view que permite buscar apenas os saldos que ainda não foram totalmente utilizados, ou seja, que ainda estão disponíveis para serem utilizados. Ela foi adicionada para facilitar as consultas que verifiam o saldo do usuário antes de uma transferencia.

## Como Executar

### Projeto

Para executar o projeto, é necessário ter o [**Docker**](https://www.docker.com/) instalado.

Abaixo estão os passos para executar o projeto

#### Copiar arquivo de configuração
```bash
cp .env.example .env
```

#### Faça o build da imagem docker
```bash
docker-compose up -d --build
```

#### Gerar chave de aplicação
```bash
docker-compose exec app php artisan key:generate
```

#### Rodar as migrations e seeds
```bash
docker-compose exec app php artisan migrate --seed
```

```bash
echo "Your Laravel app is running at: http://localhost:8000"
echo "Mailpit web interface is available at: http://localhost:8025"
```

### Testes

Para executar os testes do projeto:

```bash
docker-compose exec app php artisan test
```

Os testes foram divididos em tres categorias: **Unitários**, **Integração** e **Arquitetura**. Os testes unitários são executados em cada classe de domínio, enquanto os testes de integração são executados em cada classe de controller. Por fim, os testes de arquitetura, tem como objetivo garantir que as regras de arquitetura do projeto serão seguidas como o esperado.

## Aplicação

A aplicação é uma plataforma simplificada de pagamento, onde é possível receber e transferir créditos entre usuários.

### Tipos de Usuários

Além dos dois tipos de usuário solicitados, foram também adicionados dois novos tipos:

- **admin** - Usuário com permissão para realizar depositos e transferencias em qualquer conta
- **user** - Usuário com permissão para receber e transferir seus próprios créditos para outros usuários
- **seller** - Usuário com permissão para receber transferencias de créditos de outros usuários
- **external_found** - Usuário com permissão para depositar créditos em qualquer conta

Assim como tinhamos o usuário **seller** que apenas recebe transferencias, e pode ser considerado como o ponto **final** do fluxo, optei pela criação de um outro tipo de usuário que pudesse ser considerado o ponto **inicial** do fluxo. O tipo external found representa um usuário que possui uma conta onde sao feitos apenas debitos, ou seja, uma fonte externa de onde vem o dinheiro que futuramente será transferido entre os usuários internamente no app.

O usuário admin é o único usuário que pode realizar todas as ações em qualquer conta do sisitema, ele foi adicionado com o intuito de fazer ajustes caso necessário, mas não deve ser amplamente utilizado.

### Premissas

Para o desenvolvimento deste projeto, algumas premissas foram consideradas:

#### Transferencias

- **Não é possível transferir créditos para si mesmo**
- **Usuários do tipo lojista (seller) não podem transferir créditos**
- **Usuários do tipo fundo externo (external_found) não podem receber créditos**
- **Usuários do tipo comun (user) só podem transferir seus próprios créditos**
- **Usuários só podem transferir créditos se tiverem saldo suficiente**
- **Usuários do tipo admin (admin) podem transferir créditos de qualquer outro usuário**

#### Depositos

- **Não é possível depositar créditos para si mesmo**
- **Apenas usuários do tipo admin (admin) e fundo externo (external_found) podem depositar**
- **Apenas usuários do tipo admin (admin) e comum (user) podem receber depositos**
- **Por ser um fluxo simplificado, não foi implementada nenhuma validação externa para o fluxo de deposito**

### Rotas

#### Rota Principal

A rota principal é a rota de transferencia de créditos entre usuários. Assim como solicitado, ela recebe os IDs do usuários e o valor a ser transferido. Para que a transação seja finalizada, é preciso que o usuário tenha saldo e que a validação externa seja aprovada.

```bash
POST /api/v1/transfer

{
  "value": 100.0,
  "payer": 4,
  "payee": 15
}
```

Documentação da rota:

http://localhost:8000/docs/api#/operations/transactions.transfer

#### Rotas Adicionais

Rota para criar um novo usuário:

```bash
POST /api/v1/users

{
    "name"     : "João Silva",
    "email"    : "user@example.com",
    "document" : "11572437626",
    "password" : "Password123!",
    "role"     : "user"
}
```

Documentação da rota:

http://localhost:8000/docs/api#/operations/users.store

Rota para login:

```bash
POST /api/v1/login

{
    "email": "admin@email.com",
    "password": "Password123!"
}
```

Documentação da rota:

http://localhost:8000/docs/api#/operations/login

Para que a conta de creditos e debitos fechasse, era preciso que houvesse alguma forma de adicionar dinheiro na plataforma, antes que ele pudesse ser transferido. Para isso foi implementada a rota de deposito, assim como a rota de transferencia, ela recebe tres parametros com os IDs dos pagadores e o valor a ser depositado. Para relalizar um deposito, é preciso que o usuário pagador seja do tipo admin ou fundo externo.

```bash
POST /api/v1/deposit

{
  "value": 100.0,
  "payer": 4,
  "payee": 15
}
```

Documentação da rota:

http://localhost:8000/docs/api#/operations/transactions.deposit