# Laravel 11 Forum App

A full-featured forum application built with Laravel 11, Vue 3, and Inertia.js. Supports topic-based post categorisation, rich-text editing, comments, likes, full-text search, and user authentication with 2FA.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 11, Laravel Jetstream, Laravel Fortify |
| Frontend | Vue 3, Inertia.js, Tailwind CSS, Tiptap (rich-text editor) |
| Database | SQLite (default) / MySQL / PostgreSQL |
| Search | Meilisearch via Laravel Scout |
| Build | Vite |
| Testing | PHPUnit 11 (SQLite in-memory) |

## Features

- **Posts** – Create, list, and view posts with Markdown/rich-text body (auto-converted to HTML via Tiptap)
- **Topics** – Posts are categorised into topics (General, Technology, Gaming, etc.)
- **Comments** – Create, edit, and delete comments on posts
- **Likes** – Polymorphic like/unlike for posts and comments
- **Full-text search** – Powered by Meilisearch (Laravel Scout)
- **Authentication** – Registration, login, email verification, password reset, 2FA (via Jetstream/Fortify)
- **API tokens** – Personal access token management

## Project Structure

```
app/
  Http/Controllers/   # PostController, CommentController, LikeController
  Models/             # Post, Comment, Topic, Like, User
    Concerns/         # ConvertsMarkdownToHtml
  Policies/           # PostPolicy, CommentPolicy, LikePolicy
  Providers/          # AppServiceProvider, FortifyServiceProvider, JetstreamServiceProvider, TestingServiceProvider
  Support/            # PostFixtures (dev fixture data)
database/
  migrations/         # All DB migrations
  seeders/            # DatabaseSeeder, TopicSeeder, LikeLoadTestSeeder
  factories/          # Model factories for testing/seeding
resources/
  js/
    Pages/            # Inertia Vue pages (Post/, Profile/, Auth/, Dashboard, etc.)
    Components/       # Shared Vue components
    Layouts/          # App layout
  css/app.css
routes/
  web.php             # Main web routes
  api.php             # API routes
  local.php           # Local-only dev routes
tests/
  Feature/
    Controllers/      # PostController, CommentController, LikeController tests
    Models/           # Post, Comment model tests
    (+ Jetstream auth tests)
  Unit/
```

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- Meilisearch (optional – only needed for search; can be disabled)

### Steps

```bash
# 1. Clone the repository
git clone <repo-url>
cd laravel11-forum-app

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Set up environment
cp .env.example .env
php artisan key:generate

# 5. Create the SQLite database
touch database/database.sqlite

# 6. Run migrations
php artisan migrate

# 7. (Optional) Seed with demo data
php artisan db:seed

# 8. Link storage
php artisan storage:link
```

### Meilisearch (optional)

If you want full-text search, start a Meilisearch instance and set these values in `.env`:

```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterkey
```

To disable search entirely, set `SCOUT_DRIVER=null` in `.env`.

## Development

Start all services in one command (PHP server + queue worker + log tail + Vite):

```bash
composer run dev
```

Or start each service separately:

```bash
php artisan serve          # PHP dev server on http://localhost:8000
npm run dev                # Vite HMR
php artisan queue:listen   # Queue worker
php artisan pail           # Log viewer
```

### Local dev route

A local-only endpoint for fetching random post fixture content:

```
GET /post-content   →  returns random fixture markdown
```

## Building for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Testing

Tests use SQLite in-memory database; no external services needed.

```bash
# Run all tests
php artisan test

# Run a specific suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run a specific file
php artisan test tests/Feature/Controllers/PostController/IndexTest.php
```

Test coverage areas:
- `tests/Feature/Controllers/` – PostController (index, show, create, store), CommentController (store, edit, destroy), LikeController (store, destroy)
- `tests/Feature/Models/` – Post, Comment model behaviour
- `tests/Feature/` – Auth flows (registration, login, 2FA, password reset, API tokens, etc.)

## Default Seeded Data

Running `php artisan db:seed` creates:

- 10 predefined **topics** (General, Reviews, Technology, Lifestyle, Announcements, Support, Jobs, Events, Photography, Gaming)
- 10 random **users**
- 200 **posts** (with 18 comments each)
- A test user: `test@example.com` / `password` with 50 posts, 120 comments, and 100 likes

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
