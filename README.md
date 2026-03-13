# Sistema de Bonificação — Backend


# Guia de Instalação e Configuração

Este projeto é uma API legada desenvolvida em Laravel. Siga os passos abaixo para configurar o ambiente utilizando Docker.

---

### 1. Pré-requisitos

* Docker e Docker Compose instalados.<br>
* Terminal (PowerShell, Git Bash ou CMD).<br>

### 2. Configurando o Ambiente Laravel

1. Copie o arquivo de exemplo para as configurações locais:<br>
   cp .env.example .env<br>
2. Ajuste as variáveis de banco de dados no .env para comunicar com os containers.<br>

### 3. Subindo o Ambiente via Docker

Navegue até a pasta do projeto e suba os containers em modo segundo plano:<br>
cd teste-recrutamento-laravel-pl-main/projeto<br>
docker compose up -d<br>

Certifique-se de que os containers estão rodando:<br>
docker ps<br>

### 4. Instalação e Chaves

Execute a instalação das dependências e gere a chave da aplicação:<br>
docker exec -it painel_app composer install<br>
docker exec -it painel_app php artisan key:generate<br>

### 5. Banco de Dados e Migrations

Para criar a estrutura das tabelas no banco de dados:<br>
docker exec -it painel_app php artisan migrate<br>

### 6. Criando um Administrador Inicial

Para testar os endpoints protegidos, acesse o console interativo:<br>
docker exec -it painel_app php artisan tinker<br>

Dentro do Tinker, execute o comando de criação:<br>
use Illuminate\Support\Facades\Hash;<br>
App\Models\Administrador::create(['nome' => 'Admin Teste', 'login' => 'admin', 'senha' => Hash::make('123456')]);<br>

### 7. Front-end básico pra exibição dos funcionários

localhost:8000

### 8. Documentação da API - Gerenciamento de Funcionários

Esta API fornece um sistema completo para gerenciamento de colaboradores e controle de saldo financeiro (entradas e saídas) com segurança e integridade de dados.

## Autenticação (Laravel Sanctum)

Com exceção do `/login`, todos os endpoints exigem um token Bearer válido.

| Endpoint | Método | Descrição | Payload (JSON) |
| :--- | :--- | :--- | :--- |
| `/api/login` | `POST` | Autentica e retorna o token de acesso. | `{"login": "admin", "senha": "..."}` |
| `/api/logout` | `POST` | Invalida o token atual. | N/A |

> **Header:** `Authorization: Bearer {token}`

---

## 👥 Funcionários

Gerenciamento de registros de funcionários ativos no sistema.

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `GET` | `/api/funcionarios` | Lista funcionários (id, nome, login, saldo). |
| `GET` | `/api/funcionarios/{id}` | Busca detalhes de um funcionário específico. |
| `POST` | `/api/funcionarios` | Cria um novo funcionário (Saldo inicial: 0). |
| `PUT` | `/api/funcionarios/{id}` | Atualiza nome e login de um funcionário. |
| `DELETE` | `/api/funcionarios/{id}` | Remove o funcionário (Soft Delete). |

---

## 💸 Movimentações Financeiras

Este módulo controla o saldo dos funcionários. Possui travas de segurança para evitar saldo negativo e concorrência de dados.

### Listar Histórico
- **Endpoint:** `GET /api/funcionarios/{id}/movimentacoes`
- **Descrição:** Retorna todas as entradas e saídas do colaborador, ordenadas pelas mais recentes.

### Registrar Movimentação
- **Endpoint:** `POST /api/funcionarios/{id}/movimentacoes`
- **Payload:**
  json
{
  "tipo": "entrada", 
  "valor": 100.50,
  "descricao": "Bônus mensal"
}


# Code Review

## Problemas encontrados

### API sem autenticação
**Arquivo:** `routes/api.php`
**Descrição:** Todas as rotas da API estavam públicas, sem qualquer tipo de autenticação ou autorização. Nenhuma dessas rotas possuía middleware de autenticação.
**Risco real:** Qualquer cliente que consiga acessar a API poderia executar operações administrativas como listar, criar ou editar funcionários, registrar movimentações financeiras e acessar relatórios. Como o sistema possui apenas administradores que operam o painel, isso permitiria que um agente externo manipulasse registros financeiros ou acessasse dados internos da empresa.

### Sistema de autenticação frágil
**Arquivo:** `app/Http/Controllers/AuthController.php`
**Descrição:** Dentro do método `login()`, o sistema gerava tokens utilizando apenas `rand(100000, 999999)`. O método gera apenas 900 mil combinações, tornando-o vulnerável a força bruta. Além disso, o token não possuía expiração, era armazenado na tabela `administradores`, não seguia o padrão Laravel e não utilizava hash.

### Estrutura de domínio limitada
**Arquivo:** Diversos Controllers
**Descrição:** Boa parte das consultas ao banco era realizada diretamente nos controllers utilizando `DB::table()` ou consultas manuais, sem a utilização de models específicos para representar as entidades (administradores, funcionários, movimentações).
**Risco real:** Baixa organização da camada de domínio, dificuldade de manutenção, duplicação de regras de negócio e maior chance de inconsistência de dados. Dificulta o uso de relacionamentos Eloquent e validações centralizadas.

### Senhas armazenadas em texto puro
**Arquivo:** Migrations (`administradores` e `funcionarios`)
**Descrição:** As tabelas armazenavam senhas diretamente no banco sem qualquer tipo de hash. Exemplo: `$table->string('senha');`.
**Risco real:** Risco grave de segurança; caso o banco de dados seja comprometido, todas as senhas ficariam expostas.

### Falta de validação de entrada
**Arquivos:** `FuncionarioController.php`, `MovimentacaoController.php` e `AuthController.php`
**Descrição:** Os controllers recebiam dados diretamente do request sem validação (`$request->nome`, `$request->valor`, etc).
**Risco real:** Permite valores negativos, tipos incorretos, campos obrigatórios ausentes e inconsistências financeiras (ex: débito maior que saldo), resultando em risco de quebra do sistema.

### Operações financeiras sem transação
**Arquivo:** `MovimentacaoController.php`
**Descrição:** O registro era feito em múltiplas queries separadas (inserção da movimentação e atualização do saldo do funcionário).
**Risco real:** Caso uma operação falhasse, o sistema ficaria inconsistente (movimentação registrada sem atualização de saldo ou vice-versa).

### Possível condição de corrida (race condition)
**Arquivo:** `MovimentacaoController.php`
**Descrição:** O saldo do funcionário era lido, calculado e atualizado em operações separadas.
**Risco real:** Em cenários simultâneos, duas operações poderiam usar o mesmo saldo inicial. Exemplo: Saldo 100, duas saídas de 100 simultâneas poderiam resultar em saldo 0 quando deveria ocorrer erro de saldo insuficiente.

---

## O que foi priorizado

### Segurança e Autenticação
* **Laravel Sanctum:** Implementada a autenticação e proteção das rotas com o middleware `auth:sanctum`.
* **Tokens Seguros:** Substituição da geração manual pelo uso de `createToken` do Sanctum, com armazenamento em formato hash na tabela `personal_access_tokens`.
* **Hash de Senhas:** Implementação de `Hash::make()` no cadastro e `Hash::check()` na autenticação.

### Integridade e Validação
* **Form Requests:** Criação de `FuncionarioRequest`, `MovimentacaoRequest` e `LoginRequest` para centralizar a validação e retornar `422 Unprocessable Entity` em caso de erros.
* **Transações de Banco:** Utilização de `DB::transaction()` para garantir a atomicidade das operações financeiras.
* **Lock de Registro:** Utilização de `lockForUpdate()` para evitar race conditions durante a atualização de saldos.

### Arquitetura
* **Criação de Models:** Implementados os models `Administrador`, `Funcionario` e `Movimentacao` para organizar o domínio e utilizar os recursos do Eloquent ORM.

---

## O que decidiu não corrigir (Pontos a serem melhorados)

### Problema de performance no relatório (N+1 Query)
* **Motivo:** O relatório ainda executa 1 query para buscar funcionários + 1 query por funcionário para as movimentações.
* **Ação futura:** Refatoração utilizando agregações SQL ou eager loading para reduzir o volume de queries.

### Falta de paginação em listagens
* **Motivo:** Endpoints de listagem retornam todos os registros, causando alto consumo de memória.
* **Ação futura:** Implementação de paginação utilizando o método `paginate()`.

### Controllers com múltiplas responsabilidades
* **Motivo:** Os controllers ainda concentram validação, regras de negócio e lógica financeira.
* **Ação futura:** Separação da lógica de negócio em **Services**, mantendo os controllers apenas para orquestração das requisições.

---

## Conclusão
As correções focaram prioritariamente na segurança da aplicação, confiabilidade das operações financeiras e validação de dados. Problemas de performance e refatoração arquitetural profunda foram mapeados para ciclos futuros de desenvolvimento.




# Guia de Testes e Execução (Ambiente Docker)

Este projeto utiliza o framework de testes do Laravel para validar as correções de segurança, integridade financeira e regras de negócio descritas no Code Review.

---

## Funcionamento do Banco de Dados de Testes

Para garantir a velocidade e o isolamento total dos dados, os testes utilizam um banco de dados **SQLite em memória (`:memory:`)**. 

* **Isolamento:** Os testes não utilizam o banco de dados MySQL de produção/desenvolvimento. Um banco SQLite novo é criado em memória no início de cada execução.
* **Limpeza Automática:** Graças à trait `RefreshDatabase`, o banco é resetado e as migrations são executadas a cada teste, garantindo que um teste nunca interfira no resultado de outro.
* **Performance:** Por rodar inteiramente na RAM, o feedback dos testes é muito mais rápido do que se estivesse escrevendo em disco.

---

# Uso de Inteligência Artificial
Para este teste, utilizei IA de forma estratégica para acelerar o desenvolvimento e garantir a qualidade técnica.

Esqueleto da documentação técnica (README).

O que foi revisado: Grande parte da lógica de segurança (Sanctum).

Ajustei manualmente a lógica de lockForUpdate() para garantir a prevenção de condições de corrida, refinei as mensagens de erro para o padrão da API e corrigi as queries de relatório para assegurar a precisão dos cálculos de saldo.

## Como executar os testes via Docker

Como a aplicação está containerizada, utilize os comandos abaixo no seu terminal para disparar as suítes de teste:


**Executar todos os testes:**
```bash
docker exec -it painel_app php artisan test
```

