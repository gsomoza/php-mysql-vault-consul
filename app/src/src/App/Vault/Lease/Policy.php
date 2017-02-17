<?php

namespace App\Vault\Lease;

use App\Vault\Lease;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class Policy
{
    /** @var int */
    private $buffer;
    /** @var int */
    private $increment;

    /**
     * @param int $buffer
     * @param int $increment
     */
    public function __construct(int $buffer = 300, int $increment = 3600)
    {
        $this->buffer = $buffer;
        $this->increment = $increment;
    }

    /**
     * @param int $duration In seconds
     * @param \DateTimeInterface $createdAt
     * @return bool Whether the lease should be renewed
     */
    public function shouldRenew(\DateTimeInterface $createdAt, int $duration): bool
    {
        $duration = \DateInterval::createFromDateString($duration . ' seconds');
        $buffer = \DateInterval::createFromDateString($this->buffer . ' seconds');

        $expiry = $createdAt->add($duration)->add($buffer)->getTimestamp();

        return \time() > $expiry;
    }
}
