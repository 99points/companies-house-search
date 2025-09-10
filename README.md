# Companies House Search

A Laravel-based modular monolithic application for searching and browsing companies across multiple countries.  
Designed for scalability, easy onboarding of new countries, and lightning-fast search.

---

## ✨ Features

- **Modular Monolithic Architecture**  
  Business logic is cleanly separated into modules (e.g., Company Registry), but remains a single deployable Laravel application.  
  Adding a new country is as simple as:
  - Add DB connection in `.env`
  - Add provider entry in `config/company_providers.php` (no new PHP class needed).

- **Multi-Country Support**  
  Currently supports:
  - SG (Singapore)
  - MX (Mexico)
  - UK (United Kingdom)

- **High-Performance Full-Text Search**  
  Powered by [Meilisearch](https://www.meilisearch.com/) — blazing fast, can handle millions of records with proper server resources.

- **Pagination & Merged Data**  
  When no search query is given, the system loads company data from all connected countries, merges, and paginates them seamlessly.

---

## 🚀 Setup Instructions

### Requirements

- PHP 8.1+
- Composer
- MySQL (multiple connections, one per country)
- [Meilisearch](https://www.meilisearch.com/) server running
- Node.js + NPM/Yarn (if frontend assets need building)

---

### Installation

```bash
git clone git@github.com:99points/companies-house-search.git
cd companies-house-search
composer install
cp .env.example .env
php artisan key:generate
