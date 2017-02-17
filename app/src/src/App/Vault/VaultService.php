<?php

namespace App\Vault;

use Jippi\Vault\Services\Data;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class VaultService
{
    const VAULT_PREFIX = 'secret/app';

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
     * getMySQLPassword
     * @return string
     */
    public function getMySQLPassword(): string
    {
        $result = $this->getData('mysql/password');

        return json_decode((string) $result->getBody(), true)['data']['value'];
    }

    /**
     * @param string $path
     * @return ResponseInterface
     */
    private function getData(string $path): ResponseInterface
    {
        return $this->data->get(self::VAULT_PREFIX . '/' . ltrim($path, '/'));
    }
}
