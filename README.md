# Wallet — Desafio Técnico 

Carteira financeira com suporte a depósitos, transferências entre usuários e reversão de transações. Construída com **Laravel 13**, **Livewire** e **Laravel Sanctum**, com observabilidade via **Telescope** e documentação de API gerada automaticamente pelo **Scramble**.

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend |  Laravel 13 |
| Frontend | Livewire  Tailwind CSS |
| Autenticação | Laravel Breeze + Sanctum |
| Banco de dados | MySQL (dev/test) |
| Observabilidade | Laravel Telescope |
| Documentação | Scramble (OpenAPI) |
| Ambiente | Laravel Sail (Docker) |
| Testes | PHPUnit  |

---

## Pré-requisitos

- Docker Desktop rodando
- Uso via `./vendor/bin/sail`


## Instalação

### Clone e entre no projeto

```bash
git clone https://github.com/RafaelJP13/wallet-nillow.git
```

### Copie o arquivo de ambiente

```bash
cp .env.example .env
```

### Mova a Wallet Nillow para a pasta compartilhada do WSL2 pessoal

```bash
\\wsl$\Ubuntu\home\rafa\projects\wallet-nillow
cd wallet-nillow
```

### use o CLI do WSL2 para os próximos comandos

### Suba o ambiente

```bash
./vendor/bin/sail up -d
```

### Rode as migrations

```bash
./vendor/bin/sail artisan migrate
```

### Abra a IDE

```bash
code .
```
A aplicação estará disponível em **http://localhost**.

### (Opcional) Popule com dados de exemplo

Cria um usuário com saldo inicial de R$ 1.000,00:

```bash
./vendor/bin/sail artisan db:seed
```

Credenciais do usuário seed:
- **Email:** `rafael@example.com`
- **Senha:** `password`

### 8. Instale e compile os assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

## Testes

```bash
./vendor/bin/sail artisan test
```

---

## Documentação da API

Com o Sail rodando, acesse:

```
http://localhost/docs/api
```

A documentação é gerada automaticamente pelo Scramble a partir dos controllers e FormRequests.

### Autenticação

Todas as rotas da API exigem autenticação via **Sanctum**. Faça login pela interface web em `http://localhost/login` e use o cookie de sessão, ou gere um token via Tinker:

### Endpoints disponíveis

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/wallet` | Saldo e dados da carteira |
| `POST` | `/deposits` | Realizar um depósito |
| `GET` | `/transactions` | Histórico de transações (paginado) |
| `POST` | `/transactions` | Realizar uma transferência |
| `GET` | `/transactions/{id}` | Detalhes de uma transação |
| `POST` | `/transactions/{id}/reverse` | Reverter uma transação |

---

## Observabilidade

O **Laravel Telescope** está habilitado em ambiente local. Acesse:

```
http://localhost/telescope
```

Monitora requisições, queries SQL, jobs, exceptions e logs em tempo real.

---

## Arquitetura

```
app/
├── Enums/                  # TransactionType, TransactionStatus
├── Http/
│   ├── Controllers/        # Controllers finos, sem lógica de negócio
│   └── Requests/           # Validação e autorização via FormRequest
├── Livewire/Wallet/        # Componentes interativos do frontend
├── Models/                 # Eloquent: User, Wallet, Transaction, ...
├── Observers/              # UserObserver: cria wallet ao registrar usuário
├── Repositories/           # Acesso a dados com interfaces e implementações
├── Services/               # Regras de negócio: DepositService, TransactionService
└── Providers/
    └── AppServiceProvider  # Bind de interfaces → implementações (DI)
```
