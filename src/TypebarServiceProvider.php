<?php

declare(strict_types=1);

namespace Awcodes\Typebar;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

class TypebarServiceProvider extends PackageServiceProvider
{
    public static function getPlugin(): ?TypebarPlugin
    {
        try {
            return TypebarPlugin::get();
        } catch (Throwable) {
            return null;
        }
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('typebar')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('typebar', __DIR__ . '/../resources/dist/typebar.js'),
            Css::make('typebar', __DIR__ . '/../resources/css/typebar.css'),
        ], package: 'awcodes/typebar');

        MarkdownEditor::mixin(new MarkdownEditorMixin());
    }
}
