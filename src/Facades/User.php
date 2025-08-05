<?php

namespace Ebilet\Common\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response get(string $endpoint, array $headers = [])
 * @method static \Illuminate\Http\Client\Response post(string $endpoint, array $data = [], array $headers = [])
 * @method static \Illuminate\Http\Client\Response put(string $endpoint, array $data = [], array $headers = [])
 * @method static \Illuminate\Http\Client\Response patch(string $endpoint, array $data = [], array $headers = [])
 * @method static \Illuminate\Http\Client\Response delete(string $endpoint, array $headers = [])
 * @method static \Illuminate\Http\Client\Response request(string $method, string $endpoint, array $data = [], array $headers = [])
 * @method static self withToken(string $token)
 * @method static self withHeaders(array $headers)
 * @method static self timeout(int $seconds)
 */
class User extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ebilet.user';
    }
} 