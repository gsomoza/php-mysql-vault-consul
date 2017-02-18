<?php

namespace AppTest\Vault;

use App\Vault\Lease;
use App\Vault\Value\Increment;
use GuzzleHttp\Psr7\Response;
use Jippi\Vault\Services\Sys;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Zend\Http\Response\Stream;

/**
 * Class LeaseTest
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class LeaseTest extends \PHPUnit_Framework_TestCase
{
    public function testRenewalCallback()
    {
        $requestId = Uuid::uuid4();
        /** @var ObjectProphecy|Sys $sys */
        $sys = $this->prophesize(Sys::class);
        $json = \json_encode([
            'data' => ['fiz' => 'baz'],
            'lease_duration' => 60 * 30, // 30 minutes
            'renewable' => false,
            'request_id' => $requestId,
        ]);
        $sys->renew('test-lease', Increment::oneHour()->asSeconds())->willReturn(
            new Response(200, ['Content-Type' => 'application/json'], $json)
        );


        $policy = new Lease\BufferedRenewPolicy(0, null, $sys->reveal());
        $lease = new Lease(
            $requestId,
            'test-lease',
            60, // one minute
            true,
            ['foo' => 'bar'],
            new \DateTimeImmutable('10 minutes ago'), // it already expired
            $policy
        );

        $lease->getValue('foo'); // will trigger the renewal
    }
}
