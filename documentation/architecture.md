# TaskPulse architecture

TaskPulse is a JSON REST API. HTTP controllers validate transport concerns and delegate work to application services. Persistent data is stored in PostgreSQL through Yii2 database components and, in later iterations, Active Record and focused repositories.

```text
Client -> Nginx -> Yii2 controller -> Application service -> PostgreSQL
```

## Current components

- **Nginx** terminates HTTP traffic and forwards `index.php` requests to PHP-FPM.
- **PHP-FPM** runs the Yii2 web application and console commands.
- **PostgreSQL** is the source of truth for application data.
- **HealthCheckService** verifies that the application can query PostgreSQL.

Redis caching and RabbitMQ events are intentionally deferred until their feature iterations.
