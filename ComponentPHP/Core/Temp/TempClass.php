<?php

declare(strict_types=1);

namespace Core\Temp;

#[MyAttribute]
class TempClass
{
    #[MyAttribute]
    public function doNothing(): void
    {}
}
