<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return [
            new TwigFunction('upload_asset', [AppUploadedAsset::class, 'asset']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('cached_markdown', [AppExtensionRuntime::class, 'parser'], ['is_safe' => ['html']]),
        ];
    }
}
