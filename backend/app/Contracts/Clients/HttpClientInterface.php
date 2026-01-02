<?php

namespace App\Contracts\Clients;

interface HttpClientInterface
{
    public function get(string $url, array $params = []): array;

    public function post(string $url, array $data = []): array;
}
