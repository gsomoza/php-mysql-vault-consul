<?php

namespace App\Vault;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class Token implements \JsonSerializable
{
    /** @var array */
    private $config;

    /**
     * Token constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $tokenPath
     * @return Token
     */
    public static function fromPath(string $tokenPath): Token
    {
        $contents = \file_get_contents($tokenPath);
        $json = \json_decode($contents, true);

        return new static($json);
    }

    public function jsonSerialize()
    {
        return $this->config;
    }

    public function toString(): string
    {
        return (string) $this->config['auth']['client_token'];
    }
}
