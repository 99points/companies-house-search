# Companies House Search

A Laravel-based modular monolithic application for searching and browsing companies across multiple countries.
Designed for scalability, easy onboarding of new countries, and lightning-fast search.

---

## ✨ Features

* **Modular Monolithic Architecture**
  Business logic is cleanly separated into modules (e.g., Company Registry), but remains a single deployable Laravel application.

* **Multi-Country Support**
  Currently supports:

  * SG (Singapore)
  * MX (Mexico)
  * UK (United Kingdom)

* **High-Performance Full-Text Search**
  Powered by [Meilisearch](https://www.meilisearch.com/) — blazing fast, can handle millions of records with proper server resources.

* **Pagination & Merged Data**
  When no search query is given, the system loads company data from all connected countries, merges, and paginates them seamlessly.

--- 

## 🚀 Setup Instructions

### Requirements

* PHP 8.1+
* Composer
* MySQL (multiple connections, one per country)
* [Meilisearch](https://www.meilisearch.com/) server running
* Node.js + NPM/Yarn (if frontend assets need building)

### Installation

```bash
git clone git@github.com:99points/companies-house-search.git
cd companies-house-search
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run dev OR npm run build
```

This project requires 3 dbs, we need these connection in .env
```bash
DB_UK_HOST=127.0.0.1
DB_UK_PORT=8889
DB_UK_DATABASE=companies_house_uk
DB_UK_USERNAME=root
DB_UK_PASSWORD=root

DB_SG_HOST=127.0.0.1
DB_SG_PORT=8889
DB_SG_DATABASE=companies_house_sg
DB_SG_USERNAME=root
DB_SG_PASSWORD=root

DB_MX_HOST=127.0.0.1
DB_MX_PORT=8889
DB_MX_DATABASE=companies_house_mx
DB_MX_USERNAME=root
DB_MX_PASSWORD=root
```

#IMPORTANT FOR MULTIDATABSE PROJECT:
For Laravel apps with multiple databases, it’s common to still have a default DB set, because some services like cache, queue, or session need a “primary” connection.
BUT IF WE ARE NOT USING DEFAULT DB SET, LIKE I DID THIS FOR THIS PROJECT AS YOU SEE ABOVE 3 CONNECTIONS DO NOT HAVE DEFAULT DB SET. 

THEN YOU MUST set: `SESSION_DRIVER=file` in .env

### Install and Run Meilisearch

1. Download Meilisearch binary from [official site](https://docs.meilisearch.com/learn/getting_started/installation.html)
2. Start Meilisearch:

```bash
./meilisearch
```

3. Add this to .env

```bash
MEILI_HOST=http://127.0.0.1:7700
MEILI_KEY=masterKey
```

4. Create the Meilisearch index and populate it:

```bash
php artisan companies:reindex
```

---

## 🚀 Adding a new country

  1. Add DB connection in `.env`:

  ```bash
  DB_UK_HOST=127.0.0.1
  DB_UK_PORT=8889
  DB_UK_DATABASE=companies_house_uk
  DB_UK_USERNAME=root
  DB_UK_PASSWORD=root
  ```

  2. Update `/app/Providers/CompanyModuleServiceProvider.php` to add new country provider.
  3. Add new country provider in `app/CompanyRegistry/Infrastructure/Providers/` (copy the other country and make changes).
  4. In `app/Http/Controllers/SearchController.php` update index() accordingly.
  5. Add new connection to `config/database.php`
  
  ```bash
  'mysql_uk' => [
        'driver' => 'mysql',
        'host' => env('DB_UK_HOST'),
        'port' => env('DB_UK_PORT'),
        'database' => env('DB_UK_DATABASE'),
        'username' => env('DB_UK_USERNAME'),
        'password' => env('DB_UK_PASSWORD'),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
```
