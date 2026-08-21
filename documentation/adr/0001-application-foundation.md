# ADR 0001: Application foundation

## Status

Accepted — 2026-08-21.

## Context

TaskPulse is a portfolio REST API that must remain small enough to finish while demonstrating production-oriented backend practices. The first iteration needs a reproducible local environment and a stable API foundation before business modules are added.

The attached work project is used only as a reference for engineering practices. Its code, business logic, internal names, configuration, and secrets are not reused.

## Decision

- Use PHP 8.3 and Yii2 2.0.55.
- Use the Yii2 basic application shape with explicit `protected/controllers`, `protected/services`, `protected/config`, and `protected/migrations` boundaries.
- Run Nginx, PHP-FPM, and PostgreSQL 16 with Docker Compose.
- Keep environment-specific values outside Git and document safe local defaults in `.env.example`.
- Return successful API results as `{data, meta}` and failures as `{error: {code, message, fields?}}`.
- Add tests, static analysis, and code-style checks from the first iteration.
- Add Redis and RabbitMQ only when a completed use case requires them.

## Consequences

The initial project has slightly more infrastructure than a default Yii2 skeleton, but every later iteration can focus on a vertical feature. Infrastructure dependencies will be introduced deliberately instead of existing as unused containers.
