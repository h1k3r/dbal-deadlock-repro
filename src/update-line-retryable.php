<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/create-connection.php';
require_once __DIR__ . '/RetryableTransaction.php';

$conn = get_connection();


$process = $_SERVER['argv'][1];

$success = false;

RetryableTransaction::retryable($conn, function($conn) use ($process) {

    $conn->beginTransaction(); // open transaction

    // updating first line
    dump("Process {$process} query 1");
    $conn->executeQuery("UPDATE test_table SET value = value + :val WHERE id = :id", ['val' => random_int(0, 100), 'id' => $process === 'process_2' ? 1 : 2]);
    sleep(1);

    dump("Process {$process} query 2");
    try {
        // updating second line
        $conn->executeQuery("UPDATE test_table SET value = value + :val WHERE id = :id", ['val' => random_int(0, 100), 'id' => $process === 'process_2' ? 2 : 1]);
        $conn->commit(); // release savepoint
    } catch (\Throwable $e) {
        dump("Error in transaction: ".$e::class);
        throw $e;
    }
});

try {
    $conn->beginTransaction(); // open transaction
    $updated = $conn->update('test_table', ['value' => 0], ['id' => $process === 'process_2' ? 1 : 2]);
    $conn->commit(); // commit
    dump("Query after RetryableTransaction::retryable worked - connection healthy: " . $updated);
} catch (\Throwable $e) {
    dump("Error in fetchOne: ".$e::class);
}
