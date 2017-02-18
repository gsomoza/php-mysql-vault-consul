<?php
declare(strict_types=1);

namespace App\Vault\Lease;

use App\Vault\Lease;
use App\Vault\Value\Increment;
use Jippi\Vault\Services\Sys;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
abstract class AbstractRenewPolicy implements RenewPolicy
{
    private $locks = [];

    /** @var Sys */
    protected $sys;

    /** @var Increment */
    protected $increment;

    /**
     * @param Lease $lease
     * @return bool Whether the lease should be renewed
     */
    abstract protected function shouldRenew(Lease $lease): bool;

    /**
     * @inheritdoc
     */
    final public function renew(Lease $lease, callable $updateLease): bool
    {
        if (\in_array($lease, $this->locks, true)) {
            return false; // already locked for renewal
        }

        // set lock for renewal (avoids infinite recursion)
        $result = false;
        $this->locks[] = $lease;

        // renewal process
        if ($this->shouldRenew($lease)) {
            /** @var ResponseInterface $response */
            $response = $this->sys->renew($lease->getId(), $this->increment->asSeconds());
            $renewedLease = Lease::fromAuthBackendResponse($response);
            $updateLease($renewedLease);

            $result = true;
        }

        // remove lock
        $index = \array_search($lease, $this->locks);
        unset($this->locks[$index]);

        return $result;
    }
}
