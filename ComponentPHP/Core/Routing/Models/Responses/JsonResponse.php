<?php

declare(strict_types=1);

namespace Core\Routing\Models\Responses;

use Core\Routing\Enums\ResponseCode;

readonly class JsonResponse extends Response
{
    public function __construct(
        array $data,
        ResponseCode $responseCode = ResponseCode::Ok,
        string $charset = 'utf-8',
        array $headers = [],
    ) {
        parent::__construct(json_encode($data), $responseCode, 'application/json', $charset, $headers);
    }
}
