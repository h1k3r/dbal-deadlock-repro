<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/create-connection.php';

$conn = get_connection();
create_table($conn);

$proc1 = new Process(['php', __DIR__ . '/update-line.php', 'process_1']);
$proc2 = new Process(['php', __DIR__ . '/update-line.php', 'process_2']);
$proc1->start();
$proc2->start();

while ($proc1->isRunning() || $proc2->isRunning()) {
    usleep(100000);
}

if (!$proc1->isSuccessful() || !$proc2->isSuccessful()) {
    dump('error when releasing savepoint !');

    if (!$proc1->isSuccessful()) {
        dump("output process 1: {$proc1->getOutput()}\n{$proc1->getErrorOutput()}");
    }

    if (!$proc2->isSuccessful()) {
        dump("output process 2: {$proc2->getOutput()}\n{$proc2->getErrorOutput()}");
    }
}

function create_table(Connection $conn): void
{
    $sql = "CREATE TABLE IF NOT EXISTS test_table ( id INT PRIMARY KEY, value INT);";
    $conn->executeStatement($sql);

    $count = $conn->executeQuery('SELECT count(*) FROM test_table')->fetchOne();
    if ($count > 0) {
        return;
    }

    $sql = "INSERT INTO test_table (id, value) VALUES (1, 100), (2, 200);";
    $conn->executeStatement($sql);
}

function dump_data(Connection $conn): void
{
    $users = $conn->executeQuery('SELECT * FROM test_table')->fetchAllAssociative();

    dump($users);
}


