# Linxx - Social Platform

Linxx is a modern, scalable and secure social platform built with Laravel, Vue.js, Docker, and Redis. It supports media-rich posts, real-time updates, dark mode, and bilingual (FA/EN) localization.

---

## 🚀 Features

- 🖼️ Post creation with **text**, **images**, **videos**, **audio**, and **files**
- 🌙 Dark mode toggle with localStorage persistence
- 🌐 Locale switch (EN/FA) with RTL support
- 📡 Real-time updates using **Laravel Reverb** + **Echo**
- ⚙️ Multi-queue architecture (e.g. `media`, `notifications`, `emails`)
- 🐳 Dockerized environment for app, queues, webserver, Redis, MySQL, and ClamAV
- 🧪 Unit & feature testing ready
- 🛡️ Secure file handling with ClamAV scanning

---

## 📦 Requirements

- Docker & Docker Compose
- Node.js + npm

---

## 🛠️ Setup Instructions

```bash
# 1. Clone the repo
$ git clone https://github.com/your-org/linxx.git
$ cd linxx

# 2. Copy environment files
$ cp .env.example .env
$ cp .env .env.testing

# 3. Build containers
$ docker-compose up -d --build

# 4. Install dependencies
$ docker-compose exec app composer install
$ docker-compose exec app npm install && npm run build

# 5. Setup Laravel
$ docker-compose exec app php artisan key:generate
$ docker-compose exec app php artisan migrate --seed

# 6. Start background services
✅ Laravel queues:
- `queue-default` (handles all jobs)
- `queue-media` (separated for heavy processing)

✅ Laravel Reverb:
- Real-time broadcasting via `reverb` service
```

---

## 📁 Folder Structure

```
├── docker-config/         # Nginx, PHP, MySQL, etc.
├── linxx-vue/             # Vue frontend
├── app/                   # Laravel backend
├── storage/               # File uploads, logs
├── database/              # Migrations, seeders
├── tests/                 # Feature & unit tests
└── docker-compose.yml     # Container definitions
```

---

## 🧪 Running Tests
```bash
$ docker-compose exec app php artisan test
```

---

## 🧯 Tips
- Queue workers automatically start on container launch
- Media jobs use higher timeout and dedicated container
- All services restart automatically unless stopped manually

---

## 👨‍💻 Maintainers
- [Rezyy](https://github.com/rezyyy61/)

---

## 📝 License
MIT
