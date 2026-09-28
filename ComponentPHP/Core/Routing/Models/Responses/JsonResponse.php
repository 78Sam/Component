<?php

declare(strict_types=1);

namespace Core\Routing\Models\Responses;

class JsonResponse extends Response
{
    public function __construct(array $data, int $responseCode = 200, string $charset = 'utf-8', array $headers = [])
    {
        parent::__construct(json_encode($data), $responseCode, 'application/json', $charset, $headers);
    }
}
