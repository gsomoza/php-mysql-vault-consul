<?php

namespace App\Db;

use App\Vault\VaultService;
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
        /** @var VaultService $vault */
        $vault = $container->get(VaultService::class);

        $dbConnectionConfig = $config['database']['connection'];
        $dbConnectionConfig['password'] = $vault->getMySQLPassword();

        $dbalConfig = $container->get(Configuration::class);

        return DriverManager::getConnection($dbConnectionConfig, $dbalConfig);
    }
}
