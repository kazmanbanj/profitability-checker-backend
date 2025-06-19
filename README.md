# 📊 Specifi Profit Optimiser

This is the backend API for the Specifi Profit Optimiser project, built with **Laravel 12**. It calculates quote profitability, tracks quote versions, and integrates with the **Gemini AI API** to provide suggestions for margin improvement, labor allocation, and overall financial health.

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
    Verify that `GEMINI_API_KEY` is valid and active.

- **Database Errors:**
    Confirm your database credentials and that MySQL is running.

---
