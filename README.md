# Laboratory Management System

This repository contains a full-featured **Laboratory Information System (LIS)**, consisting of two primary components:

1. **API Backend** (`/api`) – A Python-based RESTful service built with FastAPI and SQLAlchemy.
2. **Frontend Application** (`/frontend`) – A Laravel (PHP) web application that interacts with the backend and provides the user interface.

The system is designed to manage patients, samples, lab requests, results, quotations, queues, devices, agreements, and administrative tasks. It supports desktop and mobile clients via the API, and the Laravel frontend serves as the primary web UI for lab technicians, receptionists, administrators, and managers.

---

## 🧩 System Overview

- **Backend (API)**: Stateless REST API powered by FastAPI. Exposes endpoints for every domain entity and handles authentication using JWT tokens. Intended to be consumed by the Laravel frontend and other clients.
- **Frontend (Web UI)**: Laravel-based single‑page administration portal. Acts as an API consumer and a thin server-side layer for rendering views, handling sessions, and managing UI logic.
- **Database**: MySQL (or compatible) with SQLAlchemy ORM on the backend and Eloquent ORM on the frontend during user/account-related tasks if needed.
- **Authentication**: JWT handled by the API; the frontend stores tokens in session and sends them with requests.
- **Static/Uploads**: Profile photos and other uploads are stored under `api/uploads` and served via `/static` routes in FastAPI.

---

## 🚀 Features

The system includes comprehensive laboratory workflows such as:

- **Patient Management** – create, search, and edit patient records.
- **Sample Lifecycle** – barcoding, status tracking (pending, processing, completed, rejected, etc.), and rejections.
- **Analysis Requests** – submit partial/complete analyses, queue handling, assign to lab devices.
- **Results & Reporting** – enter results, bulk uploads, download printable reports.
- **Quotations & Payments** – manage price lists, generate invoices, record payments.
- **Queue System** – multiple queue types (reception, blood draw) with public display screens.
- **Lab Devices & Formulas** – maintain devices, formulas, and assign analyses based on sample tubes.
- **Agreements & Leave Requests** – HR features for staff management.
- **Analytics & Dashboard** – metrics, recent activities, financial summaries, custom stats endpoints.
- **User Administration** – roles, logs, settings, and static assets management.

---

## 📌 Detailed Functionality

Below is a breakdown of endpoints, behaviors, and user‑facing features for each major module. This section is intended for developers and administrators who need to understand every capability the system exposes.

### Authentication

- **POST `/token`** - obtain a JWT using `username` and `password` (form data).
- **POST `/logout`** - revoke the token (frontend calls this at sign‑out).
- **GET `/users/me`** - return the currently authenticated user object (used by the frontend to populate the session).
- The API uses `get_current_user` dependency to guard protected routes. Roles (`admin`, `user`, etc.) are returned in the user payload.
- Frontend middleware `auth.api` checks for a session token and redirects to login if missing. `admin` middleware restricts certain pages to administrative users.

### Patients

- **Routes**: `/patients`, `/patients/{id}`, `/patients/table`, `/patients/{id}/results`.
- On creation the backend auto‑generates a unique `file_number` (format `PYYYYNNN`) and calculates age from DOB when returning records.
- Searchable by name, file number, phone. List endpoints support pagination (`skip`, `limit`) and text query `q`.
- `/patients/table` returns trimmed data suitable for AJAX table refreshes (used by the Laravel `patients.table` route).
- `/patients/{id}/results` returns all past lab results grouped by category/analysis and includes units, device names, and timestamps.
- Frontend provides full CRUD UI, integrated doctor selection, patient detail page, and embedded results viewer.

### Samples

- **Routes**: `/samples` (GET/POST), `/samples/{id}`, `/samples/{id}/status`, `/samples/status/{status}`, `/samples/{id}/barcode`, `/samples/{id}/receipt`.
- CRUD endpoints allow creating samples with optional auto‑generated barcode. Users can search/filter by patient, sample type, barcode, and filter by status.
- The `status` endpoint updates pipeline state and accepts an optional `rejection_reason` when marking as `rejected`.
- Barcode and receipt endpoints provide data for printing labels/receipts (e.g. barcode value, sample type, patient info).
- Frontend sample module includes forms for registering a specimen, changing status via dedicated buttons, and a multi‑step wizard that supports patient lookups (Select2) and doctor assignment.

### Analysis Requests & Lab Formulas

- **Routes**: `/analysis`, `/analysis_request` and `/lab-formulas` (via `/lab_formulas` router).
- Analysis catalog can be managed (name, price, category, SMV, sample tube type). Additional endpoints support category, unit, and sample‑type CRUD used by the UI.
- `/analysis_request` endpoints create single or bulk requests, allow attaching partial analyses, and track request status.
- Lab formulas assign one or more analyses to a tube type; used by sample creation to auto‑populate request items.
- Frontend offers data tables for analyses, dynamic forms to build quotations, and wizard steps to send requests to the lab.

### Lab Results

- **Routes**: `/lab-results` and nested paths for patient-specific results, bulk upload, download, etc.
- Users can enter single results or upload spreadsheets (bulk) for faster data entry.
- Results include normal ranges, interpretation text, device name, and are linked to quotations/analysis items.
- A download endpoint returns a printable PDF of the result for a given request.
- The Laravel UI has dedicated pages for browsing all results, viewing a patient’s history, creating new entries, and exporting.

### Quotations & Payments

- **Routes**: `/quotations` with GET/POST/PUT/DELETE, additional actions `/quotations/{id}/convert` and `/quotations/{id}/download`.
- The system handles quotation creation (price calculation), conversion to paid invoices, and tracking of payment records (partial or full).
- The `stats` endpoint returns revenue summaries by day, month, and outstanding balances.
- Frontend provides tables with lazy loading, real‑time conversion buttons, downloadable invoice PDFs, and patient lookup for on‑the‑fly creation.

### Queue Management

- **Routes**: `/queues`, `/queues/move-next`, `/queues/{id}`, `/queues/{id}/priority` and `/api/queues/status`.
- Two queue types are supported (reception and blood-draw). Items can be enqueued, advanced to the next position, deleted, and reprioritized.
- A public `/queues/display` page shows the current queue and is used on wall monitors.
- The API status endpoint returns JSON suitable for screen updates or AJAX polling.
- The frontend provides an interactive queue management panel with buttons for moving patients and updating priorities.

### Lab Devices & Formulas

- **Routes**: `/lab-devices` and `/lab-formulas`.
- Devices capture machine name, type, and supported tube types; formulas associate them with analysis groups.
- The UI allows administrators to add/modify devices, view sample‑type compatibility, and view formulas per device.

### Agreements & Leave Requests

- **Routes**: `/agreements` and `/leave-requests`.
- Staff agreements capture contract terms and documents; leave requests track employee time off with approval status.
- Frontend allows users to submit leave requests and admins to manage agreements, including file uploads and expirations.

### Analytics & Dashboard

- **Routes**: `/dashboard/metrics`, `/dashboard/recent-patients`, `/dashboard/recent-activities` and `/analytics`.
- Metrics include patient counts, samples today, pending reports, queue sizes, quotation counts, and financials.
- The analytics router exposes additional endpoints for custom reports such as urgent samples and daily statistics.
- Frontend dashboard displays cards with counts, recent patient list, merged activities from API logs and application events, and quick revenue overview.

### Profiles & Settings

- **Routes**: `/profile/{id}` for viewing/updating user profiles including photo upload.
- **Settings router** exposes `/settings` CRUD for metadata, options, and media (logo/video).
- Admin pages in Laravel provide forms for updating global options (e.g., laboratory name, working hours), uploading logos/videos, and toggling defaults.

### Users & Logs (Admin)

- **Routes**: `/users` for full CRUD and `/logs` with paged results plus partial fetch for dashboard cards.
- Logs record actions across the system; they can be filtered by user, action type, or date.
- Admin UI includes a management console to add/edit users, assign roles, and view system activity in real time.

Each router module includes detailed input validation via Pydantic schemas and business rules implemented in the `crud` layer. Most endpoints support query parameters for searching and pagination, enabling efficient UI integration.

### Utilities & Logging

- The `app/utils` package in the API contains helpers such as `app_logging.py` (a wrapper around Python's logger that records user actions) and `tcp_client.py` (used to communicate with laboratory machines). A dummy device implementation (`dummy_device_enhanced.py`) can simulate machine responses during development or testing.
- Backend logs are written to standard error via Uvicorn and are also stored in a `Log` model that powers the `/logs` endpoints. Frontend controllers invoke the API when a user performs create/update/delete actions, and any errors are caught and displayed using Laravel's validation/flash messaging.



---

## 📁 Folder Structure

```
lab-management-system/
├── api/                  # FastAPI backend
│   ├── main.py
│   ├── app/
│   │   ├── database.py
│   │   ├── crud/          # business/data access logic
│   │   ├── models/        # SQLAlchemy models
│   │   ├── routers/       # route definitions
│   │   ├── schemas/       # Pydantic validation models
│   │   └── utils/         # helpers, logging, TCP client, tests
│   ├── static/           # served files (e.g. profile photos)
│   └── tests/            # unit/integration tests
├── frontend/             # Laravel web app
│   ├── app/
│   │   ├── Http/Controllers/   # controllers for various domains
│   │   ├── Services/           # HTTP API wrapper
│   │   └── Models/             # Eloquent models (user, analysis, etc.)
│   ├── resources/        # views, assets, language files
│   ├── routes/           # web & api route definitions
│   ├── config/           # Laravel configuration
│   ├── public/           # public assets
│   └── tests/            # Laravel feature/unit tests
└── README.md             # ← this document
```

---

## 🛠️ Setup & Configuration

### Backend (API)
1. **Python environment**: create a venv and activate it.
   ```bash
   cd api
   python -m venv venv
   source venv/bin/activate   # Linux/macOS
   venv\Scripts\activate    # Windows
   ```
2. **Install dependencies**:
   ```bash
   pip install -r requirements.txt
   ```
3. **Environment variables** (in `api/.env` or system):
   ```text
   DB_USER=
   DB_PASSWORD=
   DB_HOST=
   DB_PORT=
   DB_NAME=
   SECRET_KEY=        # JWT secret
   ```
4. **Run the server**:
   ```bash
   uvicorn main:app --reload
   ```
5. **API docs**: visit `http://localhost:8000/docs` or `/redoc`.

> The API is modular; to add a new resource, create the model, schema, CRUD functions, and a router following existing patterns.

### Frontend (Laravel)
1. **Install PHP & Composer** (Laravel prerequisites).
2. **Install npm/yarn** to build frontend assets.
3. **Set up environment**: copy `.env.example` to `.env` and update:
   ```text
   APP_URL=http://localhost
   FASTAPI_URL=http://127.0.0.1:8000   # points to backend
   ```
4. **Install dependencies**:
   ```bash
   cd frontend
   composer install
   npm install && npm run dev    # or npm run build for production
   ```
5. **Generate application key**:
   ```bash
   php artisan key:generate
   ```
6. **Run the server**:
   ```bash
   php artisan serve
   ```

> The Laravel application proxies user actions to the FastAPI backend via `App\Services\ApiService`. It maintains a session token and user data.

---

## 🔗 Integration between Backend and Frontend

- `FASTAPI_URL` environment variable configures the base URL used by controllers.
- Authentication flow:
  1. User submits credentials via Laravel login form (`/login`).
  2. Laravel posts to `/token` endpoint of API, receives JWT.
  3. Token stored in session; subsequent API requests include `Authorization: Bearer <token>`.
  4. User info fetched from `/users/me` and stored in session for UI.
- File uploads (e.g. profile photos) are sent as multipart requests using `ApiService::multipart`.
- Each controller corresponds to a domain (patients, samples, quotations, etc.) and mirrors the API endpoints.

---

## 🧠 Architecture & Flow

### Backend Request Lifecycle
1. **Router** in `app/routers` validates inputs against Pydantic schemas.
2. **CRUD layer** performs database operations and business rules.
3. **Models** define tables and relationships via SQLAlchemy.
4. **`get_db()` dependency** provides a scoped session.
5. Responses returned as Pydantic models and JSON.

> Example: sample creation, timezone handling, barcode generation.

### Frontend MVC Flow
1. **Routes** (see `routes/web.php`) map URLs to controller methods.
2. **Controllers** call `ApiService` or `Http` facade to interact with backend.
3. **Views** under `resources/views` render data using Blade templates.
4. **Middleware** (`auth.api`, `admin`) guard routes and check session tokens/roles.

---

## 🧪 Testing

- Backend tests live under `api/app/tests`; run with `pytest`.
- Laravel tests under `frontend/tests` can be run with `php artisan test`.


---

## 📦 Dependency Highlights

- **Backend**: `fastapi`, `uvicorn`, `sqlalchemy`, `pydantic`, `pymysql`, `python-dotenv`, `pytest`
- **Frontend**: Laravel framework, `guzzlehttp` (via Http facade), Tailwind/Vite for assets.

---

## 💡 Extensibility & Maintenance

- Follow the established router-schema-crud-model pattern in the API.
- Use Laravel's resource controllers and form request validation to keep UI logic clean.
- Log actions consistently using `app/utils/app_logging.py` (backend) and `LogsController` (frontend).
- Configuration options can be added via the Admin settings pages; they propagate to the API through a simple table.
- Queue endpoints support public display screens and priority updates.

---

## 🚧 Deployment & Production Notes

- Secure the API with HTTPS and configure CORS appropriately.
- Use database migrations separately for backend (SQLAlchemy) and frontend (Laravel) if required.
- Ensure `SECRET_KEY` is kept safe and sessions on the frontend are cookie-secure.
- Configure caching, queue workers, and cron tasks as needed for production features.

---

## 📞 Support & Contacts

For questions, refer to inline comments in code or contact the original author/maintainer. Contributions are welcome—fork, modify, and submit pull requests.

---

**This README** aims to provide a high‑level, system‑wide description. Each subdirectory contains its own README with deeper framework-specific docs.

Happy developing! 🧪🔬