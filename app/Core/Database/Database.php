<?php

namespace app\Core\Database;

use PDO;
use PDOStatement;

class Database
{
    public ?PDO $connection;
    public PDOStatement $statement;

    public function connect(): void
    {
        $this->connection = new PDO(
            'mysql:' . http_build_query(
                config('database'),
                '',
                ';'
            ),
            config('database.username'),
            config('database.password'),
            config('database.options')
        );
    }

    public function disconnect(): void
    {
        $this->connection = null;
    }

    public function execute(string $query, array $bindings = []): static
    {
        $this->prepareQueryString($query, $bindings);
        $this->statement->execute();
        return $this;
    }

    public function get(): false|array
    {
        return $this->statement->fetchAll();
    }

    public function count(): int
    {
        return $this->statement->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function prepareQueryString(string $query, array $bindings): ?PDOStatement
    {
        $this->statement = $this->connection->prepare($query);

        foreach ($bindings as $key => $value) {
            if (str_contains($key, '.')) {
                $key = str_replace('.', '_', $key);
            }
            $placeholder = $key;
            $this->statement->bindValue($placeholder, $value);
        }

        return $this->statement;
    }

}
