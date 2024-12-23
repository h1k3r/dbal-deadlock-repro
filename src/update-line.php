<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/create-connection.php';

$conn = get_connection();

$conn->beginTransaction(); // open transaction
$conn->beginTransaction(); // create savepoint

$process = $_SERVER['argv'][1];

$success = false;

try {
    $conn->executeQuery("UPDATE test_table SET value = value + :val WHERE id = :id", ['val' => random_int(0, 100), 'id' => $process === 'process_2' ? 1 : 2]);

    sleep(1);

    $conn->executeQuery("UPDATE test_table SET value = value + :val WHERE id = :id", ['val' => random_int(0, 100), 'id' => $process === 'process_2' ? 2 : 1]);

    $conn->commit(); // release savepoint
    $conn->commit(); // commit transaction

    $success = true;
} catch (\Throwable $e) {
    dump("Error in transaction: ".$e::class);
} finally {
    if ($success === false) {
        $conn->rollBack(); // release savepoint
        $conn->rollBack(); // rollback transaction
    }
}
