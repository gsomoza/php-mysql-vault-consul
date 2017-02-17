<?php

namespace App\Consul;

use Psr\Container\ContainerInterface;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class DiscoveryServiceFactory
{
    public function __invoke(ContainerInterface $container): DiscoveryService
    {
        $config = $container->get('config')['consul'];

        $loop = \React\EventLoop\Factory::create();
        $factory = new \React\Dns\Resolver\Factory();
        $dns = $factory->create($config['host'] . ':' . $config['port'], $loop);

        return new DiscoveryService($dns, $config['datacenter'] ?: 'dc1', $loop);
    }
}
