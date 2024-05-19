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
        $dsn = 'mysql:' . http_build_query(config('database'), '', ';');

        $this->connection = new PDO(
            $dsn,
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
        $this->statement = $this->connection->prepare($query);

        foreach ($bindings as $key => $value) {
            $this->statement->bindValue(':' . $key, $value);
        }

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

}
