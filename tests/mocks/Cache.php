<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace Mocks;


use App\Service\CacheInterface;

class Cache implements CacheInterface
{
    private $data = [];

    public function get(string $key, $default = null)
    {
        $item = $this->data[$key] ?? [];

        return $item['value'] ?? $default;
    }

    public function set(string $key, $value, ?int $ttl = null)
    {
        $item = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];
        $this->data[$key] = $item;
    }

    public function expiresAt(string $key): int
    {
        $item = $this->data[$key] ?? [];

        return $item['expires_at'] ?? 0;
    }

}