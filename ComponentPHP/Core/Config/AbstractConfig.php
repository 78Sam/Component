<?php

declare(strict_types=1);

namespace Core\Config;

use Core\Kernel;
use Core\Routing\Models\Request;

abstract class AbstractConfig
{
    /**
     * Once before entering the worker request cycle, especially useful for FrankenPHP
     */
    public function onKernelBoot(Kernel $kernel): void {}

    public function preRequest(Kernel $kernel, Request $request): void {}

    public function preKernelShutdown(Kernel $kernel): void {}
}
