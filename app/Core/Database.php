<?php

namespace App\Core;

use PDO;
use PDOStatement;
use SensitiveParameter;

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
        new PDO($dsn, config('database.username'), config('database.password'), [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function disconnect(): void
    {
        $this->connection = null;
    }

    public function query($query, $params = []): static
    {
        $this->statement = $this->connection->prepare($query);

        $this->statement->execute($params);

        return $this;
    }

    public function count(): int
    {
        return $this->statement->rowCount();
    }

    public function get(): false|array
    {
        return $this->statement->fetchAll();
    }

    public function find(): false|array
    {
        return $this->statement->fetch();
    }

    public function findOrFail(): false|array
    {
        $result = $this->find();

        if (! $result) {
            abort();
        }

        return $result;
    }

    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

}
