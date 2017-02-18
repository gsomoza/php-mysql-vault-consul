<?php
declare(strict_types=1);

namespace App\Vault;

use App\Vault\Lease\NoOpRenewPolicy;
use App\Vault\Lease\RenewPolicy;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

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
    /** @var \DateTimeImmutable */
    private $createdAt;
    /** @var RenewPolicy */
    private $renewPolicy;

    /**
     * Lease constructor.
     * @param UuidInterface $requestId
     * @param string $leaseId
     * @param int $leaseDuration
     * @param bool $renewable
     * @param array $data
     * @param \DateTimeImmutable $createdAt
     * @param RenewPolicy $renewPolicy
     */
    public function __construct(
        UuidInterface $requestId,
        string $leaseId,
        int $leaseDuration,
        bool $renewable,
        array $data = [],
        \DateTimeImmutable $createdAt = null,
        RenewPolicy $renewPolicy = null
    ) {
        $this->requestId = $requestId;
        $this->leaseId = $leaseId;
        $this->leaseDuration = $leaseDuration;
        $this->renewable = $renewable;
        $this->data = $data;

        if (null === $createdAt) {
            $createdAt = new \DateTimeImmutable();
        }
        $this->createdAt = $createdAt;

        if (null === $renewPolicy) {
            $renewPolicy = new NoOpRenewPolicy();
        }
        $this->renewPolicy = $renewPolicy;
    }

    /**
     * @param ResponseInterface $response
     * @param \DateTimeInterface|null $respondedAt
     * @param RenewPolicy $renewPolicy
     * @return Lease
     */
    public static function fromAuthBackendResponse(
        ResponseInterface $response,
        \DateTimeInterface $respondedAt = null,
        RenewPolicy $renewPolicy = null
    ): Lease
    {
        if ($respondedAt instanceof \DateTime) {
            $respondedAt = \DateTimeImmutable::createFromMutable($respondedAt);
        }

        $body = \json_decode((string) $response->getBody(), true);

        return new static(
            Uuid::fromString((string) $body['request_id']),
            (string) $body['request_id'],
            (int) $body['lease_duration'],
            !empty($body['renewable']),
            $body['data'],
            $respondedAt,
            $renewPolicy
        );
    }

    /**
     * @param Lease $renewedLease
     * @return Lease
     */
    private function updateFromRenewedLease(Lease $renewedLease): self
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->leaseDuration = $renewedLease->leaseDuration;
        $this->renewable = $renewedLease->renewable;

        return $this;
    }

    /**
     * @param $key
     * @return string
     */
    public function getValue($key): string
    {
        $this->renew();
        Assert::keyExists($this->data, $key);

        return $this->data[$key];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $this->renew();

        return $this->data;
    }

    /**
     * @return int Duration of the lease in asSeconds
     */
    public function getDuration()
    {
        $this->renew();

        return $this->leaseDuration;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        $this->renew();

        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getId()
    {
        $this->renew();

        return $this->leaseId;
    }

    /**
     * @return void
     */
    private function renew()
    {
        $this->renewPolicy->renew($this, function (Lease $renewedLease) {
            $this->updateFromRenewedLease($renewedLease);
        });
    }

    /**
     * @return bool
     */
    public function isRenewable()
    {
        $this->renew();

        return $this->renewable;
    }
}
