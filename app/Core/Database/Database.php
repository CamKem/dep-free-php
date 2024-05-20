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

    // method to return the prepared statement, with the bound values
    public function raw(string $query, array $bindings = []): PDOStatement|false
    {
        return $this->prepareQueryString($query, $bindings);
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

    /**
     * @param string $query
     * @param array $bindings
     * @return false|PDOStatement
     */
    public function prepareQueryString(string $query, array $bindings): PDOStatement|false
    {
        $this->statement = $this->connection->prepare($query);

        foreach ($bindings as $key => $value) {
            $this->statement->bindValue($key, $value);
        }

        // Log the query with its bound values
        $fullQuery = $query;
        foreach ($bindings as $key => $value) {
            $fullQuery = str_replace(":" . $key, $this->connection->quote($value), $fullQuery);
        }
        logger("Full Query: {$fullQuery}");

        return $this->statement;
    }

}
