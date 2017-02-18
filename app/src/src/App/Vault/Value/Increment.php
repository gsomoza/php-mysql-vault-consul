<?php

namespace App\Vault\Value;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class Increment
{
    /** @var int in asSeconds */
    private $increment;

    /**
     * @param int $increment in asSeconds
     */
    public function __construct(int $increment)
    {
        $this->increment = $increment;
    }

    /**
     * @return static
     */
    public static function oneHour()
    {
        return new static(60*60);
    }

    /**
     * @return int
     */
    public function asSeconds(): int
    {
        return $this->increment;
    }
}
