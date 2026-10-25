<?php

declare(strict_types=1);

namespace Core\Routing\Models\Responses;

use Core\Components\Models\ComponentInterface;
use Core\Routing\Enums\ResponseCode;

final readonly class HTMXResponse extends Response
{
    public function __construct(
        string|ComponentInterface $content,
        ResponseCode $responseCode = ResponseCode::Ok,
        array $headers = [],
        // ! HTMX headers
        public readonly ?string $hxLocation = null,
        public readonly ?string $hxPushUrl = null,
        public readonly ?string $hxRedirect = null,
        public readonly ?string $hxRefresh = null,
        public readonly ?string $hxReplaceUrl = null,
        public readonly ?string $hxReswap = null,
        public readonly ?string $hxRetarget = null,
        public readonly ?string $hxReselect = null,
        public readonly ?string $hxTrigger = null,
        public readonly ?string $hxTriggerAfterSettle = null,
        public readonly ?string $hxTriggerAfterSwap = null,
    ) {
        foreach ($this->getHTMXHeaders() as $header => $value) {
            $headers[$header] = $value;
        }

        parent::__construct($content, $responseCode, 'text/html', 'utf-8', $headers);
    }

    /**
     * @return array<string, string>
     */
    public function getHTMXHeaders(): array
    {
        $htmxHeaders = array_filter(
            [
                'HX-Location' => $this->hxLocation,
                'HX-Push-Url' => $this->hxPushUrl,
                'HX-Redirect' => $this->hxRedirect,
                'HX-Refresh' => $this->hxRefresh,
                'HX-Replace-Url' => $this->hxReplaceUrl,
                'HX-Reswap' => $this->hxReswap,
                'HX-Retarget' => $this->hxRetarget,
                'HX-Reselect' => $this->hxReselect,
                'HX-Trigger' => $this->hxTrigger,
                'HX-Trigger-After-Settle' => $this->hxTriggerAfterSettle,
                'HX-Trigger-After-Swap' => $this->hxTriggerAfterSwap,
            ],
            fn(?string $value): bool => $value !== null,
        );

        return $htmxHeaders;
    }
}
