<?php
/**
 * Copyright © 2020. Ivan Kuznetsov
 */

namespace App\Service;


interface CacheInterface
{
    public function get(string $key, $default = null);

    public function set(string $key, $value, ?int $ttl = null);

    public function has(string $key);

    public function expiresAt(string $key): ?int;
}