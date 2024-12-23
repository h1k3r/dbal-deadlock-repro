<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = get_connection();
//drop_table($conn);
create_table($conn);
insert_users($conn);

function get_connection(): Connection
{
    return DriverManager::getConnection([
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/database.sqlite'
    ]);
}

function drop_table(Connection $conn): void
{
    $sql = "DROP TABLE IF EXISTS users";
    $conn->executeStatement($sql);
}

function create_table(Connection $conn): void
{
    $sql = "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT)";
    $conn->executeStatement($sql);
}

function insert_users(Connection $conn): void
{
    $countUsers = $conn->executeQuery('SELECT count(*) FROM users')->fetchOne();

    if ($countUsers > 0) {
        return;
    }
    $conn->insert('users', ['id' => 1, 'name' => 'John Doe']);
    $conn->insert('users', ['id' => 2, 'name' => 'Jane Doe']);
}


