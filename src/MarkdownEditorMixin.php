<?php

declare(strict_types=1);

namespace Awcodes\Typebar;

use Closure;
use Illuminate\Support\HtmlString;

/**
 * @mixin \Filament\Forms\Components\MarkdownEditor
 */
class MarkdownEditorMixin
{
    public function typebar(): Closure
    {
        return /** @phpstan-this \Filament\Forms\Components\MarkdownEditor */ function (?array $keys = null): \Filament\Forms\Components\MarkdownEditor {
            $plugin = TypebarServiceProvider::getPlugin();

            $keys ??= $plugin?->getKeys() ?? config('typebar.keys', []);
            $pairs = $plugin?->getPairs() ?? config('typebar.pairs', []);
            $mobileOnly = $plugin?->isMobileOnly() ?? config('typebar.mobile_only', true);

            return $this->extraAttributes([
                'data-typebar' => 'true',
                'data-typebar-keys' => new HtmlString(e(json_encode($keys))),
                'data-typebar-pairs' => new HtmlString(e(json_encode($pairs))),
                'data-typebar-mobile' => $mobileOnly ? 'true' : 'false',
            ], merge: true);
        };
    }

    public function typebarPairs(): Closure
    {
        return /** @phpstan-this \Filament\Forms\Components\MarkdownEditor */ fn (array $pairs): \Filament\Forms\Components\MarkdownEditor => $this->extraAttributes([
            'data-typebar-pairs' => new HtmlString(e(json_encode($pairs))),
        ], merge: true);
    }
}
