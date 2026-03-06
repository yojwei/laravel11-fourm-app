# Laravel 11 論壇應用程式

以 Laravel 11、Vue 3 與 Inertia.js 建構的全功能論壇。支援主題分類、富文字編輯、留言、按讚、全文搜尋，以及含 2FA 的完整使用者認證流程。

## 技術架構

| 層級 | 技術 |
|---|---|
| 後端 | PHP 8.2+、Laravel 11、Laravel Jetstream、Laravel Fortify |
| 前端 | Vue 3、Inertia.js、Tailwind CSS、Tiptap（富文字編輯器） |
| 資料庫 | SQLite（預設）／MySQL／PostgreSQL |
| 搜尋 | Meilisearch（透過 Laravel Scout） |
| 建置工具 | Vite |
| 測試 | PHPUnit 11（SQLite in-memory） |

## 功能特色

- **文章（Posts）** – 建立、列表、檢視文章；內容支援 Markdown／富文字（透過 Tiptap 自動轉為 HTML）
- **主題（Topics）** – 文章依主題分類（General、Technology、Gaming 等）
- **留言（Comments）** – 對文章新增、編輯、刪除留言
- **按讚（Likes）** – 多型按讚／取消讚（適用於文章與留言）
- **全文搜尋** – 由 Meilisearch（Laravel Scout）驅動
- **使用者認證** – 註冊、登入、Email 驗證、重設密碼、2FA（Jetstream／Fortify）
- **API Token** – 個人存取 Token 管理

## 專案結構

```
app/
  Http/Controllers/   # PostController、CommentController、LikeController
  Models/             # Post、Comment、Topic、Like、User
    Concerns/         # ConvertsMarkdownToHtml
  Policies/           # PostPolicy、CommentPolicy、LikePolicy
  Providers/          # AppServiceProvider、FortifyServiceProvider、JetstreamServiceProvider、TestingServiceProvider
  Support/            # PostFixtures（開發用假資料）
database/
  migrations/         # 所有資料庫遷移
  seeders/            # DatabaseSeeder、TopicSeeder、LikeLoadTestSeeder
  factories/          # 測試與 Seed 用的 Model Factory
resources/
  js/
    Pages/            # Inertia Vue 頁面（Post/、Profile/、Auth/、Dashboard 等）
    Components/       # 共用 Vue 元件
    Layouts/          # 應用程式版型
  css/app.css
routes/
  web.php             # 主要 Web 路由
  api.php             # API 路由
  console.php         # Artisan 指令路由
  local.php           # 僅限本機的開發路由
tests/
  Feature/
    Controllers/      # PostController、CommentController、LikeController 測試
    Models/           # Post、Comment Model 測試
    (+ Jetstream 認證相關測試)
  Unit/
```

## 安裝步驟

### 前置需求

- PHP 8.2+
- Composer
- Node.js 18+
- Meilisearch（選用，僅全文搜尋功能需要；可停用）

### 安裝

```bash
# 1. 複製專案
git clone <repo-url>
cd laravel11-forum-app

# 2. 安裝 PHP 套件
composer install

# 3. 安裝 JS 套件
npm install

# 4. 設定環境變數
cp .env.example .env
php artisan key:generate

# 5. 建立 SQLite 資料庫檔案
touch database/database.sqlite

# 6. 執行資料庫遷移
php artisan migrate

# 7. （選用）填入示範資料
php artisan db:seed

# 8. 建立 storage 符號連結
php artisan storage:link
```

### Meilisearch（選用）

若需要全文搜尋功能，請啟動 Meilisearch 並在 `.env` 設定：

```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterkey
```

若要完全停用搜尋功能，請在 `.env` 設定 `SCOUT_DRIVER=null`。

## 開發

以下單一指令同時啟動所有服務（PHP 伺服器、Queue Worker、日誌監看、Vite）：

```bash
composer run dev
```

亦可分別啟動各服務：

```bash
php artisan serve                       # PHP 開發伺服器（http://localhost:8000）
npm run dev                             # Vite HMR
php artisan queue:listen --tries=1      # Queue Worker
php artisan pail --timeout=0            # 日誌檢視器
```

### 本機專用路由

僅在本機環境載入（`routes/local.php`），用於取得隨機假文章內容：

```
GET /post-content   →  回傳隨機 fixture Markdown 內容
```

## 生產環境建置

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 測試

測試使用 SQLite in-memory 資料庫，無需外部服務。

```bash
# 執行所有測試
php artisan test

# 執行特定測試套件
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# 執行單一測試檔案
php artisan test tests/Feature/Controllers/PostController/IndexTest.php
```

測試覆蓋範圍：
- `tests/Feature/Controllers/` – PostController（index、show、create、store）、CommentController（store、edit、destroy）、LikeController（store、destroy）
- `tests/Feature/Models/` – Post、Comment Model 行為
- `tests/Feature/` – 認證流程（註冊、登入、2FA、重設密碼、API Token 等）

## 預設 Seed 資料

執行 `php artisan db:seed` 將建立：

- 10 個預定義**主題**（General、Reviews、Technology、Lifestyle、Announcements、Support、Jobs、Events、Photography、Gaming）
- 10 個隨機**使用者**
- 200 篇**文章**（每篇各含 18 則留言）
- 測試帳號：`test@example.com` / `password`，含 50 篇文章、120 則留言、100 個按讚

## 授權

本專案採用 [MIT 授權條款](https://opensource.org/licenses/MIT)。
