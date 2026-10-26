<?php

declare(strict_types=1);

namespace Core\Routing\Models\Requests;

use AbstractRequest;
use Core\Routing\Enums\RequestMethods;

readonly class HTMXRequest extends AbstractRequest
{
    public function __construct(
        string $host,
        string $scheme,
        string $path,
        int $port,
        string $queryString,
        RequestMethods $method,
        int $requestTime,
        int $serverTime,
        array $get = [],
        array $post = [],
        array $files = [],
        array $cookies = [],
        array $server = [],
        public ?string $hxBoosted = null,
        public ?string $hxCurrentUrl = null,
        public ?bool $hxHistoryRestoreRequest = null,
        public ?string $hxPrompt = null,
        public ?bool $hxRequest = null,
        public ?string $hxTarget = null,
        public ?string $hxTriggerName = null,
        public ?string $hxTrigger = null,
    ) {
        parent::__construct(
            $host,
            $scheme,
            $path,
            $port,
            $queryString,
            $method,
            $requestTime,
            $serverTime,
            $get,
            $post,
            $files,
            $cookies,
            $server,
        );
    }

    public static function fromRequest(Request $request): ?self
    {
        $hxRestoreRequest = $request->server['HTTP_HX_HISTORY_RESTORE_REQUEST'] ?? null;
        if ($hxRestoreRequest !== null) {
            $hxRestoreRequest = $hxRestoreRequest === 'true';
        }

        $hxRequest = $request->server['HTTP_HX_REQUEST'] ?? null;
        if ($hxRequest !== null) {
            $hxRequest = $hxRequest === 'true';
        }

        if ($hxRestoreRequest === null && $hxRequest === null) {
            return null;
        }

        return new self(
            $request->host,
            $request->scheme,
            $request->route,
            $request->port,
            $request->queryString,
            $request->method,
            $request->requestTime,
            $request->serverTime,
            $request->get,
            $request->post,
            $request->files,
            $request->cookies,
            $request->server,
            $request->server['HTTP_HX_BOOSTED'] ?? null,
            $request->server['HTTP_HX_CURRENT_URL'] ?? null,
            $hxRestoreRequest,
            $request->server['HTTP_HX_PROMPT'] ?? null,
            $hxRequest,
            $request->server['HTTP_HX_TARGET'] ?? null,
            $request->server['HTTP_HX_TRIGGER_NAME'] ?? null,
            $request->server['HTTP_HX_TRIGGER'] ?? null,
        );
    }
}
