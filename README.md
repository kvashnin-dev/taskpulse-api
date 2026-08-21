# TaskPulse API

TaskPulse is a production-style task management and analytics REST API built as a compact backend portfolio project.

## Stack

- PHP 8.3 and Yii2 2.0.55
- PostgreSQL 16
- Nginx and PHP-FPM
- Docker Compose
- PHPUnit, PHPStan, and PHP CS Fixer

Redis, RabbitMQ, OpenAPI, and CI will be added in later feature iterations.

## Local setup

Requirements: Docker with the Compose plugin and GNU Make.

```bash
cp .env.example .env
make init
```

The API is then available at <http://localhost:8080>.

Check application and database health:

```bash
curl --fail http://localhost:8080/health
```

Expected response:

```json
{
  "data": {
    "status": "ok",
    "services": {
      "app": "ok",
      "postgres": "ok"
    }
  },
  "meta": {}
}
```

## Development commands

```bash
make up                 # start services
make down               # stop services
make migrate            # apply database migrations
make test               # run unit tests
make stan               # run static analysis
make cs                 # check code style
make check              # run every code check
make logs               # follow container logs
```

## API response contract

Successful responses use `data` and `meta` keys. Errors use a stable machine-readable code:

```json
{
  "error": {
    "code": "validation_error",
    "message": "Validation failed",
    "fields": {
      "title": ["Title cannot be blank."]
    }
  }
}
```

## Iteration workflow

The application foundation is developed directly on `main`. Every subsequent iteration is developed in a dedicated `codex/iteration-*` branch and reviewed through a pull request before merging.
