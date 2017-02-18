<?php

namespace App\Vault;

use Jippi\Vault\Services\Data;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class VaultService
{
    /** @var Data */
    private $data;

    /**
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @return Lease
     */
    public function getDatabaseLease(): Lease
    {
        return Lease::fromAuthBackendResponse($this->data->get('mysql/creds/readonly'));
    }
}
