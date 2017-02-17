<?php

namespace App\Db;

use App\Vault\VaultService;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Jippi\Vault\Exception\ClientException;
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

        /** @var VaultService $vault */
        $vault = $container->get(VaultService::class);
        $dbLease = $vault->getDatabaseLease();

        $dbConnectionConfig['password'] = $dbLease->getData('password');
        $dbConnectionConfig['user'] = $dbLease->getData('username');

        $dbalConfig = $container->get(Configuration::class);

        return DriverManager::getConnection($dbConnectionConfig, $dbalConfig);
    }
}
