# API de Integração IBGE

<p align="center">
<img src="https://img.shields.io/badge/Laravel-8.x-red.svg" alt="Laravel Version">
<img src="https://img.shields.io/badge/PHP-8.1%7C8.2-blue.svg" alt="PHP Version">
<img src="https://img.shields.io/badge/Docker-Ready-blue.svg" alt="Docker Ready">
<img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## Sobre o Projeto

Esta API Laravel fornece integração com os serviços do IBGE (Instituto Brasileiro de Geografia e Estatística), permitindo consultas de dados geográficos e demográficos do Brasil.

### Funcionalidades

-   **Consulta de Cidades por Estado**: Busca todas as cidades de um estado específico
-   **Dados Populacionais**: Consulta dados de população por localidade e ano
-   **Cache Inteligente**: Sistema de cache para otimizar performance
-   **Validação Robusta**: Validação de parâmetros UF e dados de entrada
-   **Tratamento de Erros**: Gerenciamento adequado de exceções da API IBGE

## 🐳 Instalação com Docker (Recomendado)

### Pré-requisitos

-   Docker
-   Docker Compose

### Instalação Rápida

1. **Clone o repositório**

```bash
git clone <repository-url>
cd integracao-ibge-api
```

2. **Execute com Docker**

```bash
# Modo desenvolvimento (com hot reload)
docker-compose up -d

# Acesse: http://localhost:8000
```

### Comandos Docker Úteis

```bash
# Ver logs da aplicação
docker-compose logs -f app

# Executar comandos artisan
docker-compose exec app php artisan route:list

# Parar os containers
docker-compose down

# Rebuild da imagem
docker-compose build --no-cache
```

## 🛠️ Instalação Manual

### Pré-requisitos

-   PHP 8.1 ou superior
-   Composer
-   Laravel 8.x
-   Extensões PHP: curl, json, mbstring, xml

### Passos de Instalação

1. **Clone o repositório**

```bash
git clone <repository-url>
cd integracao-ibge-api
```

2. **Instale as dependências**

```bash
composer install
```

3. **Configure o ambiente**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure as variáveis de ambiente**

```env
IBGE_API_URL=https://servicodados.ibge.gov.br/api
IBGE_CACHE_TTL=3600
IBGE_TIMEOUT=30
IBGE_RETRIES=2
APP_ENV=local
APP_DEBUG=true
CACHE_DRIVER=file
```

5. **Execute a aplicação**

```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

## Estrutura da API

### Endpoints Disponíveis

#### Buscar Cidades por Estado

```http
GET /api/v1/ibge/cidades?uf={UF}
```

**Parâmetros:**

-   `uf` (string, obrigatório): Sigla do estado (2 caracteres, ex: SP, RJ)

**Exemplo:**

```bash
curl http://localhost:8000/api/v1/ibge/cidades?uf=SP
```

**Resposta:**

```json
{
    "status": 200,
    "message": "Cidades encontradas com sucesso",
    "body": [
        {
            "id": 3550308,
            "nome": "São Paulo",
            "uf": "SP"
        }
    ]
}
```

#### Buscar População por Localidade

```http
GET /api/v1/ibge/populacao/{locationId}/{year}
```

**Parâmetros:**

-   `locationId` (integer): ID da localidade no IBGE
-   `year` (integer): Ano da consulta (formato YYYY)

**Exemplo:**

```bash
curl http://localhost:8000/api/v1/ibge/populacao/3550308/2021
```

**Resposta:**

```json
{
    "status": 200,
    "message": "População encontrada",
    "body": {
        "populacao": 12396372
    }
}
```

## Arquitetura

### Principais Componentes

-   **[`IbgeIntegrationService`](app/Services/IbgeIntegrator/IbgeIntegrationService.php)**: Serviço principal de integração
-   **[`IbgeRepository`](app/Repositories/IbgeRepository.php)**: Repository para comunicação com API IBGE
-   **[`IntegracaoIbgeController`](app/Http/Controllers/IntegracaoIbgeController.php)**: Controller das rotas da API
-   **[`CidadeDTO`](app/DTOs/CidadeDTO.php)**: Data Transfer Object para cidades
-   **[`RestService`](app/Services/Rest/RestService.php)**: Serviço para requisições HTTP

### Cache

O sistema implementa cache inteligente para otimizar consultas:

-   **TTL configurável**: Tempo de vida do cache definido pela variável `IBGE_CACHE_TTL` (padrão: 1 hora)
-   **Cache por UF**: Dados de cidades são cacheados por estado
-   **Cache populacional**: Dados populacionais são cacheados por localidade/ano
-   **Driver**: Sistema de arquivos (sem necessidade de Redis/Memcached)

## Desenvolvimento

### Executar Testes

```bash
# Com Docker
docker-compose exec app php artisan test

# Manual
php artisan test
```

### Verificar Rotas

```bash
# Com Docker
docker-compose exec app php artisan route:list --path=api/v1/ibge

# Manual
php artisan route:list --path=api/v1/ibge
```

### Limpar Cache

```bash
# Com Docker
docker-compose exec app php artisan cache:clear

# Manual
php artisan cache:clear
```

## Configuração

### Variáveis de Ambiente

```env
# Configurações da API IBGE
IBGE_API_URL=https://servicodados.ibge.gov.br/api
IBGE_CACHE_TTL=3600
IBGE_TIMEOUT=30
IBGE_RETRIES=2

# Configurações da aplicação Laravel
APP_NAME="API Integração IBGE"
APP_ENV=local
APP_DEBUG=true
APP_KEY=
APP_URL=http://localhost:8000

# Configurações de cache
CACHE_DRIVER=file

# Configurações de log
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Configurações de sessão e queue
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Configurações Docker

O projeto utiliza PHP 8.2 com Alpine Linux para otimização de tamanho e performance:

-   **Base**: `php:8.2-fpm-alpine`
-   **Extensões PHP**: mbstring, xml, curl
-   **Servidor**: Built-in PHP server (desenvolvimento) ou Nginx (produção)
-   **Porta**: 8000 (desenvolvimento) / 8080 (produção com Nginx)

## Dependências Principais

-   **Laravel Framework 8.x**: Framework PHP
-   **GuzzleHttp/Guzzle**: Cliente HTTP para requisições à API IBGE
-   **PHP 8.1+**: Versão mínima do PHP

## Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a [Licença MIT](https://opensource.org/licenses/MIT).

## Contato

Para dúvidas ou sugestões sobre a API, entre em contato através das issues do repositório.
