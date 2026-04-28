<?php

namespace Awcodes\Typebar;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\HtmlString;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TypebarServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('typebar')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('typebar', __DIR__.'/../resources/dist/typebar.js'),
            Css::make('typebar', __DIR__.'/../resources/css/typebar.css'),
        ], package: 'awcodes/typebar');

        MarkdownEditor::macro('typebar', function (?array $keys = null) {
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
        });

        MarkdownEditor::macro('typebarPairs', function (array $pairs) {
            return $this->extraAttributes([
                'data-typebar-pairs' => new HtmlString(e(json_encode($pairs))),
            ], merge: true);
        });
    }

    protected static function getPlugin(): ?TypebarPlugin
    {
        try {
            return TypebarPlugin::get();
        } catch (\Throwable) {
            return null;
        }
    }
}
