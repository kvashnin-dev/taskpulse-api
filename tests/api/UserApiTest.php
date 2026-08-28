<?php

declare(strict_types=1);

namespace tests\api;

use JsonException;
use PDO;
use PHPUnit\Framework\TestCase;

final class UserApiTest extends TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        $this->db = new PDO(
            sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $_ENV['DB_HOST'] ?? 'postgres',
                $_ENV['DB_PORT'] ?? '5432',
                $_ENV['DB_NAME'] ?? 'taskpulse',
            ),
            $_ENV['DB_USER'] ?? 'taskpulse',
            $_ENV['DB_PASSWORD'] ?? 'taskpulse',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $this->db->exec('TRUNCATE TABLE users RESTART IDENTITY');
    }

    /**
     * @throws JsonException
     */
    public function testCrudAndSoftDelete(): void
    {
        $created = $this->request('POST', '/users', [
            'full_name' => 'Иван Петров',
            'phone' => '+79991234567',
        ]);

        self::assertSame(201, $created['status']);
        self::assertSame(1, $created['body']['id']);
        self::assertSame('Иван Петров', $created['body']['full_name']);
        self::assertIsString($created['body']['created_at']);

        $view = $this->request('GET', '/users/1');
        self::assertSame(200, $view['status']);
        self::assertSame('+79991234567', $view['body']['phone']);

        $updated = $this->request('PATCH', '/users/1', [
            'full_name' => 'Пётр Иванов',
            'phone' => null,
        ]);
        self::assertSame(200, $updated['status']);
        self::assertSame('Пётр Иванов', $updated['body']['full_name']);
        self::assertNull($updated['body']['phone']);

        $deleted = $this->request('DELETE', '/users/1');
        self::assertSame(204, $deleted['status']);

        $missing = $this->request('GET', '/users/1');
        self::assertSame(404, $missing['status']);

        $deletedAt = $this->db->query('SELECT deleted_at FROM users WHERE id = 1')->fetchColumn();
        self::assertIsString($deletedAt);
    }

    /**
     * @throws JsonException
     */
    public function testPagination(): void
    {
        foreach (['Первый пользователь', 'Второй пользователь', 'Третий пользователь'] as $name) {
            $response = $this->request('POST', '/users', ['full_name' => $name]);
            self::assertSame(201, $response['status']);
        }

        $response = $this->request('GET', '/users?page=2&per_page=2');

        self::assertSame(200, $response['status']);
        self::assertCount(1, $response['body']['items']);
        self::assertSame(3, $response['body']['_meta']['totalCount']);
        self::assertSame(2, $response['body']['_meta']['pageCount']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationError(): void
    {
        $response = $this->request('POST', '/users', [
            'full_name' => 'И',
            'phone' => '89991234567',
        ]);

        self::assertSame(422, $response['status']);
        self::assertSame('full_name', $response['body'][0]['field']);
        self::assertSame('Имя должно содержать не менее 3 символов.', $response['body'][0]['message']);
    }

    /**
     * @throws JsonException
     */
    public function testPaginationValidationError(): void
    {
        $response = $this->request('GET', '/users?page=wrong&per_page=101');

        self::assertSame(422, $response['status']);
        self::assertSame('page', $response['body'][0]['field']);
        self::assertSame(
            'Номер страницы должен быть целым числом.',
            $response['body'][0]['message'],
        );
    }

    /**
     * @param array<string, mixed>|null $body
     * @return array{status: int, body: array<int|string, mixed>}
     * @throws JsonException
     */
    private function request(string $method, string $path, ?array $body = null): array
    {
        $headers = ['Accept: application/json'];
        $options = [
            'method' => $method,
            'ignore_errors' => true,
        ];

        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
            $options['content'] = json_encode($body, JSON_THROW_ON_ERROR);
        }
        $options['header'] = implode("\r\n", $headers);

        $response = file_get_contents(
            ($_ENV['API_BASE_URL'] ?? 'http://nginx') . $path,
            false,
            stream_context_create(['http' => $options]),
        );
        self::assertNotFalse($response);

        /** @var list<string> $http_response_header */
        $statusLine = $http_response_header[0] ?? '';
        preg_match('/\s(\d{3})\s/', $statusLine, $matches);
        self::assertArrayHasKey(1, $matches);

        $decoded = $response === '' ? [] : json_decode($response, true, flags: JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        return [
            'status' => (int) $matches[1],
            'body' => $decoded,
        ];
    }
}
