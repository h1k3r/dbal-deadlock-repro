<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;

function get_connection(): Connection
{
    $dsnParser = new DsnParser();
    $connectionParams = $dsnParser
        ->parse('mysql://root:1234@127.0.0.1:3307/repro_dbal?serverVersion=5.7.42')
    ;
    $connectionParams['driver'] = 'pdo_mysql';

    $connection = DriverManager::getConnection($connectionParams);
    $connection->setNestTransactionsWithSavepoints(true);

    return $connection;
}

