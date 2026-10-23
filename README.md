# HR Management System (HRMS) — MPWT

A modern, robust Human Resource Management System built for the Ministry of Public Works and Transport (MPWT), powered by **Laravel**, **MySQL**, **Redis**, **Bootstrap 5**, and **Docker**.

---

## 🚀 Features

- 👥 **Staff Directory & Profiles**: Comprehensive staff records, departments, offices, positions, and employment statuses.
- 🕒 **Attendance & QR Code Clock-In**: Instant QR code scanning for daily check-in/out with location validation and history logs.
- 📝 **Leave Management**: Leave request workflows, leave types, balances, and approval processes.
- 📅 **Meetings & Agendas**: Schedule meetings, track attendees, and manage meeting agenda items.
- 🤝 **Working Groups & Committees**: Team and committee assignments with member role tracking.
- 📊 **Dashboard & Visual Analytics**: Interactive metrics and charts powered by Chart.js.
- 🔐 **Role-Based Access Control (RBAC)**: Distinct permissions and views for Administrators and Staff members.
- 🇰🇭 **Khmer Localization**: Default Khmer language interface (`km`) with English fallback support.

---

## 🛠️ Technology Stack

| Layer | Technology |
|---|---|
| **Framework** | [Laravel](https://laravel.com) (PHP 8.3+) |
| **Database** | [MySQL 8.0](https://www.mysql.com) |
| **Cache & Queues** | [Redis 7](https://redis.io) |
| **Web Server** | [Nginx](https://nginx.org) |
| **Frontend** | [Bootstrap 5](https://getbootstrap.com), [Vite](https://vite.dev), [Chart.js](https://www.chartjs.org) |
| **Containers** | Docker & Docker Compose |

---

## 💻 Local Development Setup

### Prerequisites
- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 20.x & **npm**
- **Docker** & **Docker Compose**

### Step-by-Step Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd mpwt
   ```

2. **Set up the environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

4. **Generate the application key:**
   ```bash
   php artisan key:generate
   ```

5. **Start the local MySQL database with Docker:**
   ```bash
   docker compose up -d
   ```

6. **Run database migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```

7. **Create the storage symlink:**
   ```bash
   php artisan storage:link
   ```

8. **Start the development servers:**
   ```bash
   # Terminal 1: Compile frontend assets with Vite
   npm run dev

   # Terminal 2: Run Laravel backend server
   php artisan serve
   ```
   Open your browser at **`http://localhost:8000`**.

---

## 🔑 Default Demo Accounts

After running `php artisan migrate --seed`, you can log in with:

| Role | Email | Password |
|---|---|---|
| **Administrator** | `admin@mpwt.gov.kh` | `password` |
| **Staff Member** | `sokha@mpwt.gov.kh` | `password` |
| **Test Account** | `test@example.com` | `password` |

---

## 🐳 Production Deployment (Docker Compose)

The repository provides a production-ready Docker Compose configuration with multi-stage builds, OPcache optimizations, an Nginx reverse proxy, MySQL, Redis, a queue worker, and an automated task scheduler.

### Architecture

```
                       ┌─────────────────────────┐
                       │  HTTP/HTTPS Traffic     │
                       │ (hrm-portal.mpwt.dev)   │
                       └────────────┬────────────┘
                                    │ Port 8010 (or 443 via proxy)
                                    ▼
                       ┌─────────────────────────┐
                       │        hrms_web         │
                       │     (Nginx Alpine)      │
                       └────────────┬────────────┘
                                    │ FastCGI (:9000)
                                    ▼
                       ┌─────────────────────────┐
                       │        hrms_app         │
                       │   (PHP 8.3 FPM Alpine)  │
                       └──────┬───────────┬──────┘
                              │           │
         ┌────────────────────┴───┐   ┌───┴────────────────────┐
         ▼                        ▼   ▼                        ▼
┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐
│     hrms_db      │    │    hrms_redis    │    │    hrms_queue    │
│    (MySQL 8)     │    │ (Cache/Sessions) │    │  (Queue Worker)  │
└──────────────────┘    └──────────────────┘    └──────────────────┘
```

### Production Setup Instructions

1. **Prepare the production environment:**
   ```bash
   cp .env.production.example .env
   ```
   *Set `APP_URL=https://hrm-portal.mpwt.dev` and `APP_PORT=8010` (the origin port Cloudflare Tunnel or reverse proxy routes traffic to).*

2. **Build and start all services:**
   ```bash
   docker compose -f docker-compose.prod.yml up -d --build
   ```
   The application will be accessible via Cloudflare at **`https://hrm-portal.mpwt.dev`** (with traffic routed to local port `8010`).

3. **Check container status:**
   ```bash
   docker compose -f docker-compose.prod.yml ps
   ```

4. **Monitor logs:**
   ```bash
   # View all service logs
   docker compose -f docker-compose.prod.yml logs -f

   # View only application logs
   docker compose -f docker-compose.prod.yml logs -f app
   ```

5. **Run maintenance & Artisan commands inside Docker:**
   ```bash
   # Run migrations manually if needed
   docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

   # Clear or refresh caches
   docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
   docker compose -f docker-compose.prod.yml exec app php artisan optimize

   # Database backup
   docker compose -f docker-compose.prod.yml exec db mysqldump -u root -p hrms > backup.sql
   ```

---

## 📁 Project Structure

```text
├── app/                  # Application core (Controllers, Models, Services)
├── bootstrap/            # Application bootstrap and configuration cache
├── config/               # Application configuration files
├── database/
│   ├── factories/        # Model factories
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders (demo data & roles)
├── docker/
│   ├── entrypoint.sh     # Production container startup & cache script
│   ├── nginx/            # Production Nginx virtual host configuration
│   └── php/              # Custom php.ini and opcache.ini configurations
├── public/               # Web root (compiled Vite assets, index.php)
├── resources/            # Blade views, CSS, and JavaScript
├── routes/               # Web, console, and API routes
├── storage/              # Uploaded files, cache, and application logs
├── Dockerfile            # Multi-stage production Dockerfile (App & Web)
├── docker-compose.yml    # Development Docker Compose (MySQL service)
└── docker-compose.prod.yml # Production Docker Compose (Full stack)
```

---

## 🧪 Testing & Code Quality

Run tests and linting with:

```bash
# Run test suite
php artisan test

# Format code with Laravel Pint
./vendor/bin/pint
```

---

## 📄 License

This project is proprietary software developed for the Ministry of Public Works and Transport (MPWT).
