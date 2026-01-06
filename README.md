# Sistema de Gerenciamento de Filmes Favoritos

Aplicação web para gerenciamento de filmes favoritos integrada com a API do TMDB (The Movie Database). Desenvolvida com Laravel 12 (backend) e Vue.js 3 (frontend), utilizando Docker para containerização.

## Tecnologias Utilizadas

### Backend
- PHP 8.4
- Laravel 12
- MySQL 8.0
- Redis
- PHPUnit (testes)

### Frontend
- Vue.js 3
- TypeScript
- Vite
- Vitest (testes)

## Pré-requisitos

- Docker e Docker Compose instalados
- Chave de API do TMDB (instruções abaixo)

## Configuração e Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/FrancielliAndreghetto/test-kinghost.git
cd test-kinghost
```

### 2. Configuração do Backend

#### 2.1. Configure as variáveis de ambiente

Copie o arquivo de exemplo e edite com suas configurações:

```bash
cp backend/.env.example backend/.env
```

Edite o arquivo `backend/.env` e configure as seguintes variáveis:

```env
APP_NAME="Movie Favorites"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=king_db
DB_USERNAME=king_user
DB_PASSWORD=king_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

TMDB_API_KEY=sua_chave_aqui
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_TIMEOUT=30
```

#### 2.2. Obtenha a chave da API do TMDB

1. Acesse: https://www.themoviedb.org/
2. Crie uma conta gratuita
3. Acesse o painel de configurações: https://www.themoviedb.org/settings/api
4. Solicite uma chave de API (API Key v3)
5. Copie a chave e adicione no arquivo `.env` na variável `TMDB_API_KEY`

### 3. Suba os containers Docker

Na raiz do projeto, execute:

```bash
docker-compose up -d --build
```

Aguarde a construção das imagens e inicialização dos containers. Este processo pode levar alguns minutos na primeira execução.

### 4. Configure a aplicação Laravel

#### 4.1. Gere a chave da aplicação

```bash
docker-compose exec backend php artisan key:generate
```

#### 4.2. Execute as migrations

```bash
docker-compose exec backend php artisan migrate
```

#### 4.3. (Opcional) Execute os seeders para dados de exemplo

```bash
docker-compose exec backend php artisan db:seed
```

```bash
Email: test@example.com
Senha: password
```

### 5. Acesse a aplicação

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8080/api

## Estrutura do Projeto

### CRUD de Filmes Favoritos

A implementação do CRUD de favoritos está organizada seguindo os princípios de Clean Architecture:

#### Backend (Laravel)

**Rotas**
- `backend/routes/api.php` - Definição das rotas da API

**Controllers**
- `backend/app/Http/Controllers/Api/FavoriteController.php` - Gerenciamento de requisições HTTP
- `backend/app/Http/Controllers/Api/AuthController.php` - Autenticação de usuários

**Services (Camada de Negócio)**
- `backend/app/Services/FavoriteService.php` - Lógica de negócio dos favoritos
- `backend/app/Contracts/Services/FavoriteServiceInterface.php` - Interface do serviço

**Repositories (Camada de Dados)**
- `backend/app/Repositories/EloquentFavoriteRepository.php` - Persistência de dados
- `backend/app/Contracts/Repositories/FavoriteRepositoryInterface.php` - Interface do repositório

**Models**
- `backend/app/Models/Favorite.php` - Model Eloquent
- `backend/app/Models/User.php` - Model de usuário

**Validação**
- `backend/app/Http/Requests/StoreFavoriteRequest.php` - Validação de entrada
- `backend/app/Http/Requests/RegisterRequest.php` - Validação de registro
- `backend/app/Http/Requests/LoginRequest.php` - Validação de login

**Migrations**
- `backend/database/migrations/2026_01_02_220441_create_favorites_table.php`

#### Frontend (Vue.js)

**Views**
- `frontend/src/views/HomeView.vue` - Página principal com listagem de filmes
- `frontend/src/views/FavoritesView.vue` - Página de favoritos do usuário

**Components**
- `frontend/src/components/MovieCard.vue` - Card de exibição de filme
- `frontend/src/components/MovieModal.vue` - Modal com detalhes do filme

**Composables (Lógica Reutilizável)**
- `frontend/src/composables/useFavorites.ts` - Gerenciamento de estado dos favoritos

**Services (Integração com API)**
- `frontend/src/services/favoriteService.ts` - Comunicação com API de favoritos
- `frontend/src/services/movieService.ts` - Comunicação com API de filmes

**Rotas**
- `frontend/src/router/index.ts` - Configuração de rotas do Vue Router

### Endpoints da API

**Autenticação**
- `POST /api/register` - Registro de novo usuário
- `POST /api/login` - Login de usuário
- `POST /api/logout` - Logout (requer autenticação)
- `GET /api/user` - Dados do usuário autenticado

**Favoritos**
- `GET /api/favorites` - Lista todos os favoritos do usuário
- `POST /api/favorites` - Adiciona filme aos favoritos
- `DELETE /api/favorites/{movieId}` - Remove filme dos favoritos
- `GET /api/favorites/check/{movieId}` - Verifica se filme é favorito

**Filmes (TMDB)**
- `GET /api/movies/popular` - Filmes populares
- `GET /api/movies/top-rated` - Filmes mais bem avaliados
- `GET /api/movies/now-playing` - Filmes em cartaz
- `GET /api/movies/upcoming` - Próximos lançamentos
- `GET /api/movies/search` - Busca de filmes
- `GET /api/movies/genres` - Listagem de gêneros

## Testando a Aplicação

### Testes Automatizados (Backend)

Execute a suíte de testes unitários:

```bash
docker-compose exec backend vendor/bin/phpunit
```

Execute apenas testes unitários:

```bash
docker-compose exec backend vendor/bin/phpunit --testsuite=Unit
```

Os testes cobrem:
- Repositories (17 testes)
- Services (15 testes)

### Validação Manual

1. **Registro de Usuário**
   - Acesse http://localhost:3000
   - Clique em "Criar Conta"
   - Preencha: Nome, Email, Senha
   - Faça login com as credenciais criadas

2. **Navegação de Filmes**
   - Navegue pelas abas: Início e Favoritos
   - Use a busca para encontrar filmes específicos
   - Filtre por gênero

3. **Gerenciamento de Favoritos**
   - Clique no ícone de coração para adicionar aos favoritos
   - Acesse a página "Favoritos" no menu
   - Filtre favoritos por gênero
   - Remova favoritos clicando novamente no coração

4. **Visualização de Detalhes**
   - Clique no ícone de informação (i) em qualquer card
   - Visualize sinopse, nota e ano de lançamento
   - Adicione/remova dos favoritos diretamente no modal

## Arquitetura e Padrões Implementados

### Backend
- **Clean Architecture**: Separação em camadas (Controllers, Services, Repositories)
- **Dependency Injection**: Inversão de controle via Service Container do Laravel
- **Repository Pattern**: Abstração da camada de dados
- **Service Layer**: Lógica de negócio isolada dos controllers
- **Form Requests**: Validação de entrada em camada dedicada
- **API Resources**: Formatação consistente de respostas
- **Sanctum**: Autenticação via tokens API

### Frontend
- **Composition API**: Vue 3 com script setup
- **TypeScript**: Tipagem estática
- **Composables**: Lógica reutilizável e reativa
- **Event Bus**: Comunicação entre componentes
- **Axios Interceptors**: Tratamento centralizado de erros
- **Vue Router**: Navegação SPA com guards de autenticação

## Comandos Úteis

### Backend

```bash
# Acessar o container
docker-compose exec backend bash

# Limpar cache
docker-compose exec backend php artisan cache:clear
docker-compose exec backend php artisan config:clear

# Recriar banco de dados
docker-compose exec backend php artisan migrate:fresh --seed

# Ver logs
docker-compose logs -f backend
```

### Frontend

```bash
# Acessar o container
docker-compose exec frontend sh

# Instalar dependências (se necessário)
docker-compose exec frontend npm install

# Build de produção
docker-compose exec frontend npm run build

# Ver logs
docker-compose logs -f frontend
```

### Parar os containers

```bash
docker-compose down
```

### Remover volumes (limpar banco de dados)

```bash
docker-compose down -v
```

## Troubleshooting

### Erro de permissão no Laravel

```bash
docker-compose exec backend chmod -R 777 storage bootstrap/cache
```

### Frontend não conecta com backend

Verifique se a variável `VITE_API_URL` está correta no arquivo `frontend/.env`:

```env
VITE_API_URL=http://localhost:8080/api
```

### Erro de conexão com o banco de dados

Aguarde alguns segundos após o `docker-compose up` para o MySQL inicializar completamente, depois execute as migrations.

### Cache do browser

Se mudanças não aparecerem no frontend, limpe o cache do navegador (Ctrl+Shift+R).
