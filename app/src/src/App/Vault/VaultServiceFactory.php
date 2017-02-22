<?php

namespace App\Vault;

use App\Consul\DiscoveryService;
use Jippi\Vault\ServiceFactory as VaultApiFactory;
use Jippi\Vault\Services\Data;
use Psr\Container\ContainerInterface;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class VaultServiceFactory
{
    public function __invoke(ContainerInterface $container): VaultService
    {
        $tokenPath = $container->get('config')['vault']['token'];
        $discovery = $container->get(DiscoveryService::class);

        $vaultIp = $discovery->findServiceIP('vault'); // query IP for vault.service.consul
        $serviceFactory = new VaultApiFactory([
            'base_uri' => 'http://' . $vaultIp . ':8200',
            'headers' => [
                'User-Agent' => 'Vault-PHP-SDK/1.0',
                'Content-Type' => 'application/json',
                'X-Vault-Token' => Token::fromPath($tokenPath)->toString(),
            ],
        ]);

        /** @var Data $data */
        $data = $serviceFactory->get('data'); // get Vault's "data" API

        return new VaultService($data);
    }
}
