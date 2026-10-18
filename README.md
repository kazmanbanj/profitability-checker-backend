# 📊 Specifi Profit Optimiser

This is the backend API for the Specifi Profit Optimiser project, built with **Laravel 12**. It calculates quote profitability, tracks quote versions, and integrates with the **Groq AI API** to provide suggestions for margin improvement, labor allocation, and overall financial health.

---

## 🚀 Tech Stack

- **Framework:** Laravel 12
- **Language:** PHP 8.1+
- **Database:** MySQL
- **AI Integration:** Groq API (GPT OSS 20B)
- **PDF Export:** barryvdh/laravel-dompdf

---

## 📦 Installation & Setup

### 1. Clone and Install Dependencies

```bash
git clone https://github.com/kazmanbanj/profitability-checker-backend.git
cd profitability-checker-backend
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configure Environment

Edit your `.env` file with your database and Groq API credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

GROQ_API_KEY="your_groq_api_key"
GROQ_MODEL="openai/gpt-oss-20b"

FRONTEND_URL=http://localhost:5173
```

> **Note:**
> To obtain a Groq API key, visit [Groq Console](https://console.groq.com), sign in, go to **API Keys**, and create a new key.

### 3. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 4. Start the Development Server

```bash
php artisan serve
```

API will be available at: [http://localhost:8000](http://localhost:8000)

---

## 📁 Project Structure

```text
project-root/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Models/
│   │   └── Services/
│   ├── routes/api.php
│   └── database/migrations/
```

---

## 🔌 API Endpoints

| Method | Endpoint                                   | Description                                       |
|--------|--------------------------------------------|---------------------------------------------------|
| POST   | `/api/v1/quotes/analyze`                   | Analyze quote and return AI suggestions           |
| POST   | `/api/v1/quotes/{quoteId}/re-analyze`      | Re-analyze the quote and re-prompt AI suggestions |
| GET    | `/api/v1/quotes/{quoteId}/export-analysis` | Export quote analysis                             |
| GET    | `/api/v1/quotes`                           | List all quotes and AI suggestions                |
| GET    | `/api/v1/quotes/{quoteId}`                 | Get one quote and AI suggestion                   |
| GET    | `/api/v1/quotes/{quoteId}/versions`        | Get quote suggestion versions                     |

---

## 🛠 Troubleshooting

- **CORS Issues:**
    Ensure `FRONTEND_URL` is set correctly in your `.env`.

- **API Key Issues:**
    Verify that `GROQ_API_KEY` is valid and active, and that `GROQ_MODEL` is set to a supported model (e.g. `openai/gpt-oss-20b`).

- **Database Errors:**
    Confirm your database credentials and that MySQL is running.

---


### BONUS
## 🐳 Setting up with Docker

Follow the below steps if you want to run the application in a Dockerized development environment using **Docker Compose**.

---

## 🧱 Requirements

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/)

---

## 🚀 Getting Started

### 1. Clone the Repository (Skip this step if you have it cloned already)

```bash
git clone https://github.com/kazmanbanj/profitability-checker-backend.git
cd profitability-checker-backend
cp .env.example .env
cp docker/env/mysql.env.example docker/env/mysql.env
```

### 2. Configure Environment

Edit your `.env` file with your database and Groq API credentials:

```env
DB_CONNECTION=mysql
DB_HOST=mysql // make sure this is set to mysql
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

GROQ_API_KEY="your_groq_api_key"
GROQ_MODEL="openai/gpt-oss-20b"

FRONTEND_URL=http://localhost:5173
```

Also, edit your `docker/env/mysql.env` file as well:

```env
MYSQL_DATABASE=your_database_name
MYSQL_ROOT_PASSWORD=your_password
```

### 3. Build and Start the Containers

```bash
docker compose up -d --build
```

### 4. Build and Start the Containers

```bash
docker compose run --rm composer install
```

### 5. Run Database Migrations

```bash
docker compose exec php php artisan migrate
```

### 6. Run other artisan commands (Optional)

```bash
docker compose exec php php artisan key:generate
docker compose exec php php artisan optimize:clear
docker compose exec php php artisan db:seed
docker compose exec php php artisan test
```

API will be available at: [http://localhost:8000](http://localhost:8000)

---

## 🐋 Common Docker Commands

### Start containers
```
docker compose up -d
```

### Stop containers
```
docker compose down
```

### Rebuild containers
```
docker compose up -d --build
```

### Run artisan commands
```
docker compose exec php php artisan
```

### Run composer
```
docker compose run --rm composer -V
docker compose run --rm composer install
docker compose run --rm composer update
```

### Run composer
```
docker compose run --rm composer -V
docker compose run --rm npm install
docker compose run --rm npm update
```

### Enter container shell
```
docker compose exec php bash
```

### Command to clear everything:
#### 🚨 WARNING: This is destructive — all images, containers, and persistent data (e.g. databases) will be deleted.
```
docker compose down -v --remove-orphans
docker system prune -a --volumes --force

docker build -t laravel-app .
docker run -d --name laravel-app-container -p 8080:80 laravel-app
docker start laravel-app-container
```

---

## 🎯 Docker Build Targets

The Dockerfile uses multi-stage builds with two targets:

| Target | Description |
|--------|-------------|
| `dev`  | Copies `.env.docker` into the container as `.env`. Use for local development. |
| `base` | No `.env` baked in. Use for production — supply env vars at runtime. |

### Build for development

```bash
docker build --target dev -t laravel-app:dev .
docker run -d --name laravel-app-container -p 8080:80 laravel-app:dev
```

### Build for production

```bash
docker build --target base -t laravel-app:prod .
docker run -d --name laravel-app-container -p 80:80 --env-file .env.production laravel-app:prod
```

### Using Docker Compose

Set the `target` in your `docker-compose.yml`:

```yaml
build:
  context: .
  target: dev   # change to base for production
```