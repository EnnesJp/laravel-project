# Plataforma de Pagamento

Este projeto tem como objetivo criar uma plataforma simples de pagamento, onde é possível receber e transferir créditos entre usuários.

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
- **Scramble** - Pacote Laravel para gerar documentação de API  

### Arquitetura

O projeto utiliza uma abordagem **Domain-Driven Design (DDD)** para organizar o código de forma mais limpa e manutenível, separando as responsabilidades por domínios de negócio.

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

O banco de dados utilizado foi o **MySQL**. O projeto utiliza o **Eloquent ORM** para realizar as operações de CRUD no banco de dados, além de utilizar **Migrations** para gerenciar as alterações no banco de dados.

A estrutura do banco de dados é simples, contendo apenas cinco tabelas e uma view para facilitar a busca de dados específicos:

**OBS:** não estão sendo consideradas aqui as tabelas criadas pelo Laravel.

- **users** - Tabela de usuários  
- **transactions** - Tabela de transações financeiras  
- **credits** - Tabela de créditos  
- **debits** - Tabela de débitos  
- **fund_debits** - Tabela de débitos de fundos externos  

- **remaining_credits** - View para buscar apenas saldos que ainda não foram totalmente utilizados  

Na tabela de `transactions` é possível ver todas as transações financeiras realizadas, tanto de transferências quanto de depósitos. Além disso, também estão presentes os IDs do usuário pagador e daquele que está recebendo o valor.

Toda transação gera um débito na conta do usuário pagador e um crédito na conta do usuário que está recebendo o valor. No caso de uma transferência, portanto, são gerados dois novos registros: um na tabela de `debits` e um na tabela de `credits`. Para permitir uma maior rastreabilidade, o débito é vinculado ao crédito que foi utilizado na transação, o que permite identificar com maior facilidade possíveis erros no saldo do usuário, rastreando os créditos que ele já recebeu e todas as suas utilizações.

Já no caso de uma transação de depósito vindo de uma fonte externa, não existe esse rastreio, pois não há um crédito sendo utilizado. Por isso, ao realizar um depósito, é gerado um registro na tabela de `credits`, assim como na transferência, e um registro na tabela de `fund_debits`, que indica uma entrada de crédito no app vinda de uma fonte externa.

Com essa estrutura, é possível garantir a consistência dos dados, a rastreabilidade de uso para facilitar investigações em casos de erro e a garantia contábil de que a soma de todos os créditos e débitos será sempre zero.

Por fim, a tabela de `remaining_credits` é uma view que permite buscar apenas os saldos que ainda não foram totalmente utilizados, ou seja, que ainda estão disponíveis para uso. Ela foi adicionada para facilitar as consultas que verificam o saldo do usuário antes de uma transferência.

## Como Executar

### Projeto

Para executar o projeto, é necessário ter o [**Docker**](https://www.docker.com/) instalado.

Abaixo estão os passos para executar o projeto:

#### Copiar o arquivo de configuração
```bash
cp .env.example .env
```

#### Faça o build da imagem docker
```bash
docker-compose up -d --build
```

#### Gerar a chave da aplicação
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

Os testes foram divididos em três categorias: **Unitários**, **Integração** e **Arquitetura**. Os testes unitários são executados em cada classe de domínio, enquanto os testes de integração são executados em cada controller. Por fim, os testes de arquitetura têm como objetivo garantir que as regras arquiteturais do projeto sejam seguidas conforme o esperado.

## Aplicação

A aplicação é uma plataforma simplificada de pagamento, onde é possível receber e transferir créditos entre usuários.

### Tipos de Usuários

Além dos dois tipos de usuário solicitados, também foram adicionados dois novos tipos:

- **admin** - Usuário com permissão para realizar depósitos e transferências em qualquer conta
- **user** - Usuário com permissão para receber e transferir seus próprios créditos para outros usuários
- **seller** - Usuário com permissão para receber transferências de créditos de outros usuários
- **external_found** - Usuário com permissão para depositar créditos em qualquer conta

Assim como o usuário **seller**, que apenas recebe transferências e pode ser considerado o ponto **final** do fluxo, foi criada a figura de um outro tipo de usuário que pode ser considerado o ponto **inicial** do fluxo. O tipo **external_found** representa um usuário que possui uma conta onde são feitos apenas débitos, ou seja, uma fonte externa de onde vem o dinheiro que futuramente será transferido entre os usuários internamente no app.

O usuário **admin** é o único que pode realizar todas as ações em qualquer conta do sistema. Ele foi adicionado com o intuito de permitir ajustes quando necessário, mas não deve ser amplamente utilizado.

### Premissas

Para o desenvolvimento deste projeto, algumas premissas foram consideradas:

#### Transferencias

- **Não é possível transferir créditos para si mesmo**
- **Usuários do tipo lojista (seller) não podem transferir créditos**
- **Usuários do tipo fundo externo (external_found) não podem receber créditos**
- **Usuários do tipo comum (user) só podem transferir seus próprios créditos**
- **Usuários só podem transferir créditos se tiverem saldo suficiente**
- **Usuários do tipo admin (admin) podem transferir créditos de qualquer outro usuário**

#### Depositos

- **Não é possível depositar créditos para si mesmo**
- **Apenas usuários do tipo admin (admin) e fundo externo (external_found) podem depositar**
- **Apenas usuários do tipo admin (admin) e comum (user) podem receber depósitos**
- **Por ser um fluxo simplificado, não foi implementada nenhuma validação externa para o fluxo de depósito**

### Rotas

#### Rota Principal

A rota principal é a rota de transferência de créditos entre usuários. Conforme solicitado, ela recebe os IDs dos usuários e o valor a ser transferido. Para que a transação seja finalizada, é necessário que o usuário tenha saldo e que a validação externa seja aprovada.

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

Para que a conta de créditos e débitos fechasse, era necessário haver alguma forma de adicionar dinheiro na plataforma antes que ele pudesse ser transferido. Para isso, foi implementada a rota de depósito. Assim como a rota de transferência, ela recebe três parâmetros com os IDs dos usuários e o valor a ser depositado. Para realizar um depósito, é necessário que o usuário pagador seja do tipo admin ou fundo externo.

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