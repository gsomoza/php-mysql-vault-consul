<?php
declare(strict_types=1);

namespace App\Vault\Lease;

use App\Vault\Lease;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
interface RenewPolicy
{
    /**
     * @param Lease $lease The lease instance to renew
     * @param callable $updateLease A callable that will update the original lease instance with data from the new lease
     * @return bool Whether the lease was renewed or not
     */
    public function renew(Lease $lease, callable $updateLease): bool;
}
