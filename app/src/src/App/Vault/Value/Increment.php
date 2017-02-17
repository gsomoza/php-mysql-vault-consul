<?php

namespace App\Vault\Value;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class Increment
{
    /** @var int in seconds */
    private $increment;

    /**
     * @param int $increment in seconds
     */
    public function __construct(int $increment)
    {
        $this->increment = $increment;
    }

    /**
     * @return int
     */
    public function seconds(): int
    {
        return $this->increment;
    }
}
