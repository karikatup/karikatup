# Karikatup API

## Installation

Firstly, move to installation directory. (You should change path)

```bash
cd /srv/http
```

After that, clone repository.

```bash
git clone git@github.com:karikatup/karikatup.git karikatup
```

Move to the downloaded directory.

```bash
cd karikatup
```

Install **composer** packages.

```bash
composer install
```

Copy `.env.example` file to `.env`.

```bash
cp .env.example .env
```

Create a new database and type information about the database in `.env` file.

Migrate the database

```
php artisan migrate
```

This command will execute migration files and create tables with relationships.

Test installation with browser. (`http://127.0.0.1/karikatur/public`)
