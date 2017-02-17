<?php

namespace App\Consul;

use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;

/**
 * Helps to discover services via Consul
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class DiscoveryService
{
    /** @var Resolver */
    private $dns;
    /** @var string */
    private $datacenter;
    /** @var LoopInterface */
    private $loop;

    /**
     * @param Resolver $dns
     * @param string $datacenter
     * @param LoopInterface $loop
     */
    public function __construct(Resolver $dns, string $datacenter, LoopInterface $loop)
    {
        $this->dns = $dns;
        $this->datacenter = $datacenter;
        $this->loop = $loop;
    }

    /**
     * @param string $serviceName
     * @return array
     */
    public function findServiceIP(string $serviceName)
    {
        $this->loop->stop();

        $domain = $this->getDomainForService($serviceName);
        /** @var Promise $promise */
        $promise = $this->dns->resolve($domain);
        $result = null;
        $promise->then(function ($ip) use (&$result) {
            $result = $ip;
        }, function (\Exception $exception) {
            echo $exception->getMessage();die;
        });

        $this->loop->run();

        return $result;
    }

    /**
     * @param string $serviceName
     * @return string
     */
    private function getDomainForService(string $serviceName): string
    {
        $domain = [$serviceName, 'service'];
        if (!empty($this->datacenter)) {
            $domain[] = $this->datacenter;
        }
        $domain[] = 'consul';

        return implode('.', $domain);
    }
}
