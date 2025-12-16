<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ConsumesUserCredits
{
    public function __construct(
        public int $cost,
        public string $feature,
    ) {}
}