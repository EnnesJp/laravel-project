# Plataforma de Pagamento

Esse projeto tem como objetivo fazer uma simples plataforma de pagamento, onde é possível receber e transferir créditos entre usuários.

## Decisões Técnicas

### Tech Stack

Para o desenvolvimento deste exercício, a stack escolhida foi **PHP** com **Laravel**, devido à maior familiaridade do desenvolvedor. Além disso, foi utilizado **Docker** para a containerização da aplicação, facilitando a reprodução do ambiente de execução.

#### Tecnologias

- **MySQL** - Banco de dados principal
- **Redis** - Utilizado para gerenciamento de cache e filas
- **Larastan** - Análise estática de código para Laravel
- **PHPMD** - PHP Mess Detector para detecção de problemas no código
- **Pint** - Code style fixer para PHP
- **Telescope** - Ferramenta de debug para projetos Laravel
- **Horizon** - Dashboard e configuração para filas Redis
- **Sanctum** - Sistema de autenticação API para SPAs e aplicações móveis
- **Pest** - Framework de testes elegante e minimalista

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