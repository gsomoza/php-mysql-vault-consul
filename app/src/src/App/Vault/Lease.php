<?php
declare(strict_types=1);

namespace App\Vault;

use App\Vault\Lease\Policy;
use App\Vault\Value\Increment;
use Jippi\Vault\Services\Sys;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class Lease
{
    /** @var UuidInterface */
    private $requestId;
    /** @var string */
    private $leaseId;
    /** @var int */
    private $leaseDuration;
    /** @var bool */
    private $renewable;
    /** @var array<string,string> */
    private $data;
    /** @var string */
    private $warnings;
    /** @var \DateTimeImmutable */
    private $createdAt;
    /** @var Policy */
    private $policy;

    /**
     * Lease constructor.
     * @param UuidInterface $requestId
     * @param string $leaseId
     * @param int $leaseDuration
     * @param bool $renewable
     * @param array $data
     * @param string $warnings
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(
        UuidInterface $requestId,
        string $leaseId,
        int $leaseDuration,
        bool $renewable,
        array $data = [],
        string $warnings = null,
        \DateTimeImmutable $createdAt
    ) {
        $this->requestId = $requestId;
        $this->leaseId = $leaseId;
        $this->leaseDuration = $leaseDuration;
        $this->renewable = $renewable;
        $this->data = $data;
        $this->warnings = $warnings;
        $this->createdAt = $createdAt;
        $this->policy = new Policy();
    }

    /**
     * @param ResponseInterface $response
     * @param \DateTimeInterface|null $responseTime
     * @return Lease
     */
    public static function fromAuthBackendResponse(
        ResponseInterface $response,
        \DateTimeInterface $responseTime = null
    ): Lease
    {
        if (null == $responseTime) {
            $responseTime = new \DateTimeImmutable();
        } else {
            $responseTime = clone $responseTime;
        }

        $body = \json_decode((string) $response->getBody(), true);

        return new static(
            Uuid::fromString((string) $body['request_id']),
            (string) $body['request_id'],
            (int) $body['lease_duration'],
            !empty($body['renewable']),
            $body['data'],
            (string) $body['warnings'],
            $responseTime
        );
    }

    /**
     * @param Policy $policy
     * @return Lease
     */
    public function withPolicy(Policy $policy)
    {
        $instance = clone $this;
        $instance->policy = $policy;

        return $instance;
    }

    /**
     * @param Sys $service
     * @param Increment $increment
     * @return self
     */
    public function renew(Increment $increment, Sys $service): self
    {
        /** @var ResponseInterface $response */
        $response = $service->renew($this->leaseId, $increment->seconds());

        return self::fromAuthBackendResponse($response);
    }

    /**
     * @param $key
     * @return string
     */
    public function getData($key): string
    {
        Assert::keyExists($this->data, $key);

        return $this->data[$key];
    }
}
