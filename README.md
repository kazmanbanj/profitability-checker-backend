https://profitoptimiser.onrender.com

# 📊 Profit Optimiser

This is the backend API for the Profit Optimiser project, built with **Laravel 12**. It calculates quote profitability, tracks quote versions, and integrates with the **Gemini AI API** to provide suggestions for margin improvement, labor allocation, and overall financial health.

---

## 🚀 Tech Stack

- **Framework:** Laravel 12
- **Language:** PHP 8.1+
- **Database:** MySQL
- **AI Integration:** Google Gemini API
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

Edit your `.env` file with your database and Gemini API credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

GEMINI_API_KEY="your_gemini_api_key"
GEMINI_API_BASE_URL="https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent"
GEMINI_ROLE="user"

FRONTEND_URL=http://localhost:5173
```

> **Note:**
> To obtain a Gemini API key, visit [Gemini API Docs](https://ai.google.dev/gemini-api/docs), sign in, and follow the instructions to create and copy your API key.

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

```
├── 📁 .git/ 🚫 (auto-hidden)
├── 📁 .vscode/ 🚫 (auto-hidden)
├── 📁 app/
│   ├── 📁 Exceptions/
│   │   └── 🐘 ApiNotFoundException.php
│   ├── 📁 Helpers/
│   │   ├── 🐘 ApiResponse.php
│   │   └── 🐘 Helper.php
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── 📁 Api/
│   │   │   │   └── 📁 V1/
│   │   │   │       └── 🐘 QuoteController.php
│   │   │   └── 🐘 Controller.php
│   │   └── 📁 Requests/
│   │       ├── 🐘 AnalyzeQuoteRequest.php
│   │       ├── 🐘 BaseFormRequest.php
│   │       └── 🐘 ReAnalyzeQuoteRequest.php
│   ├── 📁 Models/
│   │   ├── 🐘 LineItem.php
│   │   ├── 🐘 Quote.php
│   │   ├── 🐘 QuoteAiAnalysisVersion.php
│   │   └── 🐘 User.php
│   ├── 📁 Providers/
│   │   └── 🐘 AppServiceProvider.php
│   └── 📁 Services/
│       ├── 📁 AI/
│       │   └── 🐘 GeminiService.php
│       └── 🐘 QuoteService.php
├── 📁 bootstrap/
│   ├── 📁 cache/ 🚫 (auto-hidden)
│   ├── 🐘 app.php
│   └── 🐘 providers.php
├── 📁 config/
│   ├── 🐘 app.php
│   ├── 🐘 auth.php
│   ├── 🐘 cache.php
│   ├── 🐘 cors.php
│   ├── 🐘 database.php
│   ├── 🐘 filesystems.php
│   ├── 🐘 logging.php
│   ├── 🐘 mail.php
│   ├── 🐘 queue.php
│   ├── 🐘 services.php
│   └── 🐘 session.php
├── 📁 database/
│   ├── 📁 factories/
│   │   ├── 🐘 QuoteFactory.php
│   │   └── 🐘 UserFactory.php
│   ├── 📁 migrations/
│   │   ├── 🐘 0001_01_01_000000_create_users_table.php
│   │   ├── 🐘 0001_01_01_000001_create_cache_table.php
│   │   ├── 🐘 0001_01_01_000002_create_jobs_table.php
│   │   ├── 🐘 2025_06_10_161221_create_quotes_table.php
│   │   ├── 🐘 2025_06_10_161224_create_line_items_table.php
│   │   ├── 🐘 2025_06_10_161234_create_analyses_table.php
│   │   └── 🐘 2025_06_18_204818_create_quote_ai_analysis_versions_table.php
│   ├── 📁 seeders/
│   │   ├── 🐘 DatabaseSeeder.php
│   │   └── 🐘 QuoteSeeder.php
│   ├── 🚫 .gitignore
│   └── 🗄️ database.sqlite
├── 📁 docker/
│   └── 📁 env/ 🚫 (auto-hidden)
├── 📁 node_modules/ 🚫 (auto-hidden)
├── 📁 public/
│   ├── 📄 .htaccess
│   ├── 🖼️ favicon.ico
│   ├── 🐘 index.php
│   └── 📄 robots.txt
├── 📁 resources/
│   ├── 📁 css/
│   │   └── 🎨 app.css
│   ├── 📁 js/
│   │   ├── 📄 app.js
│   │   └── 📄 bootstrap.js
│   └── 📁 views/
│       ├── 📁 layouts/
│       │   └── 🐘 app.blade.php
│       ├── 📁 pdf-exports/
│       │   └── 🐘 quote-summary.blade.php
│       └── 🐘 welcome.blade.php
├── 📁 routes/
│   ├── 🐘 api.php
│   ├── 🐘 console.php
│   └── 🐘 web.php
├── 📁 storage/
│   ├── 📁 app/
│   │   ├── 📁 private/
│   │   │   └── 🚫 .gitignore
│   │   ├── 📁 public/
│   │   │   └── 🚫 .gitignore
│   │   └── 🚫 .gitignore
│   ├── 📁 framework/
│   │   ├── 📁 cache/ 🚫 (auto-hidden)
│   │   ├── 📁 sessions/
│   │   │   └── 🚫 .gitignore
│   │   ├── 📁 testing/
│   │   │   └── 🚫 .gitignore
│   │   └── 🚫 .gitignore
│   └── 📁 logs/
│       ├── 🚫 .gitignore
│       └── 📋 laravel.log 🚫 (auto-hidden)
├── 📁 tests/
│   ├── 📁 Feature/
│   │   └── 🐘 QuoteTest.php
│   ├── 📁 Unit/
│   │   └── 🐘 ProfitabilityCalculationTest.php
│   ├── 📄 .DS_Store 🚫 (auto-hidden)
│   └── 🐘 TestCase.php
├── 📁 vendor/ 🚫 (auto-hidden)
├── 📄 .DS_Store 🚫 (auto-hidden)
├── 📄 .editorconfig
├── 🔒 .env 🚫 (auto-hidden)
├── 📄 .env.example
├── 📄 .gitattributes
├── 🚫 .gitignore
├── 🗑️ .phpunit.result.cache 🚫 (auto-hidden)
├── 📖 README.md
├── 📄 artisan
├── 📄 composer.json
├── 🔒 composer.lock 🚫 (auto-hidden)
├── 📄 package.json
├── 📄 phpstan.neon
├── 📄 phpunit.xml
├── 📄 pint.json
└── 📄 vite.config.js
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
    Verify that `GEMINI_API_KEY` is valid and active.

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

Edit your `.env` file with your database and Gemini API credentials:

```env
DB_CONNECTION=mysql
DB_HOST=mysql // make sure this is set to mysql
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

GEMINI_API_KEY="your_gemini_api_key"
GEMINI_API_BASE_URL="https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent"
GEMINI_ROLE="user"

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
