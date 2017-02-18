<?php

namespace App\Vault\Lease;

use App\Vault\Lease;
use App\Vault\Value\Increment;
use Jippi\Vault\Services\Sys;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class BufferedRenewPolicy extends AbstractRenewPolicy
{
    /** @var int */
    private $buffer;

    /**
     * @param int $buffer Defaults to 300s (5 minutes)
     * @param Increment $increment Defaults to 1 hour (3600 asSeconds)
     * @param Sys $sys
     */
    public function __construct(int $buffer = 300, Increment $increment = null, Sys $sys)
    {
        if (null === $increment) {
            $increment = Increment::oneHour();
        }
        $this->increment = $increment;
        $this->buffer = $buffer;
        $this->sys = $sys;
    }

    /**
     * @inheritdoc
     */
    protected function shouldRenew(Lease $lease): bool
    {
        if (!$lease->isRenewable()) {
            return false;
        }

        $duration = \DateInterval::createFromDateString($lease->getDuration() . ' asSeconds');
        $buffer = \DateInterval::createFromDateString($this->buffer . ' asSeconds');

        $expiry = $lease->getCreatedAt()->add($duration)->add($buffer)->getTimestamp();

        return \time() > $expiry;
    }
}
