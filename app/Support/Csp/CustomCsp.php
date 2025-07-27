<?php

namespace App\Support\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

class CustomCsp implements Preset
{
    public function configure(Policy $policy): void
    {
        $policy
            ->add(Directive::IMG, ['robohash.org', 'cdn.jsdelivr.net'])
            ->add(Directive::IMG, 'data:');
    }
}
