# Habitly - Habit Tracker

> Habit tracker with streaks, contribution graphs, and Google authentication

![Badge](https://img.shields.io/badge/Laravel_13-FF2D20?style=flat-square\&logo=laravel\&logoColor=white)
![Badge](https://img.shields.io/badge/PHP_8.4-777BB4?style=flat-square\&logo=php\&logoColor=white)
![Badge](https://img.shields.io/badge/Tailwind_CSS_v4-06B6D4?style=flat-square\&logo=tailwindcss\&logoColor=white)
![Badge](https://img.shields.io/badge/Vite-646CFF?style=flat-square\&logo=vite\&logoColor=white)
![Badge](https://img.shields.io/badge/PostgreSQL-4169E1?style=flat-square\&logo=postgresql\&logoColor=white)
![Badge](https://img.shields.io/badge/Docker-2496ED?style=flat-square\&logo=docker\&logoColor=white)
![Badge](https://img.shields.io/badge/Google_Auth-4285F4?style=flat-square\&logo=google\&logoColor=white)
![Badge](https://img.shields.io/badge/License-MIT-yellow?style=flat-square\&logo=open-source-initiative\&logoColor=white)

---

## 📌 Overview

Habitly is a personal habit tracking application focused on productivity, simplicity, and consistency.
The platform allows users to manage daily habits, track streaks, and visualize progress using contribution-style graphs inspired by GitHub commits.
Built with Laravel 13 and Tailwind CSS, the project includes dark/light themes, Google authentication, an interactive calendar, and a fully Dockerized development environment for streamlined setup and deployment.

## 🛠️ Technologies

* **Laravel 13** — Backend framework and application architecture
* **PHP 8.4** — Main backend programming language
* **Tailwind CSS v4** — Utility-first CSS framework for UI design
* **Vite** — Frontend build tool and hot-reload development server
* **Blade Templates** — Server-side rendering engine for Laravel
* **PostgreSQL** — Relational database for application data
* **Laravel Socialite** — Google authentication integration
* **Docker & Docker Compose** — Containerized development environment
* **JavaScript (Vanilla)** — Interactive frontend functionality

## 📁 Project Structure

```bash id="m8q2tk"
.
├── app/                            # Core Laravel application logic
├── resources/
│   ├── views/                      # Blade templates
│   ├── css/                        # Tailwind CSS files
│   └── js/                         # Frontend JavaScript
│
├── routes/                         # Application routes
├── database/                       # Migrations and seeders
├── public/                         # Public assets and entry point
├── storage/                        # Logs and generated files
│
├── dockerfile/
│   ├── dev/
│   │   └── Dockerfile.dev          # Development Docker image
│   └── prod/
│       └── Dockerfile.prod         # Production Docker image
│
├── docker-entrypoint.sh            # Development startup script
├── docker-compose.yml              # Docker Compose configuration
├── .dockerignore                   # Docker ignored files
├── .env.example                    # Environment variables example
├── README.md                       # Project documentation
└── LICENSE                         # Project license
```

## 🚀 Getting Started

Clone the repository:

```bash id="z7x4pa"
git clone https://github.com/LuizBMJ/habitly-habit-tracker.git
```

Navigate to the project folder:

```bash id="r5m1vk"
cd habitly-habit-tracker
```

Start the Docker containers:

```bash id="n3q8fy"
docker-compose up --build
```

After the containers are ready, access the application at:

```text id="t9k6pw"
http://localhost:10000
```

Useful Docker commands:

```bash id="x2v7la"
# Stop containers
docker-compose down

# Stop containers and remove volumes
docker-compose down -v

# Run containers in detached mode
docker-compose up -d

# View application logs
docker-compose logs -f app

# Access the container terminal
docker-compose exec app bash
```

Optional Google authentication setup:

```env id="f1p8zn"
GOOGLE_CLIENT_ID="your_client_id"
GOOGLE_CLIENT_SECRET="your_client_secret"
GOOGLE_REDIRECT_URI="http://localhost:10000/auth/google/callback"
```

## 📄 License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).
