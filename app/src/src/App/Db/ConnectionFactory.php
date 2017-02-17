<?php

namespace App\Db;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class ConnectionFactory
{
    public function __invoke(ContainerInterface $container) : Connection
    {
        $config = $container->get('config');
        $dbConnectionConfig = $config['database']['connection'];

        $dbalConfig = $container->get(Configuration::class);

        return DriverManager::getConnection($dbConnectionConfig, $dbalConfig);
    }
}
