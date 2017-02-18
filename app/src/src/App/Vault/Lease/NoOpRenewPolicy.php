<?php
declare(strict_types=1);

namespace App\Vault\Lease;

use App\Vault\Lease;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class NoOpRenewPolicy implements RenewPolicy
{
    /**
     * @inheritdoc
     */
    public function renew(Lease $lease, callable $updateLease): bool
    {
        // no op
        return false;
    }
}
