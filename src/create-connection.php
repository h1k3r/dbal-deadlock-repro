<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;

function get_connection(): Connection
{
    $dsnParser = new DsnParser();
    $connectionParams = $dsnParser
        ->parse('mysql://shopware:shopware@127.0.0.1/shopware_test')
    ;
    $connectionParams['driver'] = 'pdo_mysql';

    $connection = DriverManager::getConnection($connectionParams);

    return $connection;
}

