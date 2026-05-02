# Typebar

A mobile-friendly Markdown symbol row for Filament's native `MarkdownEditor`. Typebar behaves like a keyboard accessory row — tapping a key inserts the literal character at the cursor position.

[![Latest Version](https://img.shields.io/github/release/awcodes/typebar.svg?style=flat-square&color=blue&label=Release)](https://github.com/awcodes/typebar/releases)
[![MIT Licensed](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/typebar.svg?style=flat-square&color=blue&label=Downloads)](https://packagist.org/packages/awcodes/typebar)
[![GitHub Repo stars](https://img.shields.io/github/stars/awcodes/typebar?style=flat-square&color=blue&label=Stars)](https://github.com/awcodes/typebar/stargazers)
[![Filament Version](https://img.shields.io/badge/Filament-4.x%2F5.x-d97706.svg?style=flat-square)](https://filamentphp.com/docs/4.x/panels/installation)

> [!WARNING]
> Typebar is currently a work in progress. Do not use in production yet. Please report any issues you encounter to help us stabilize the package.

## Requirements

- PHP 8.2+
- Filament v4 or v5

## Installation

Install the package via Composer:

```bash
composer require awcodes/typebar
```

Publish the config file:

```bash
php artisan vendor:publish --tag="typebar-config"
```

## Usage

### Basic

Add `->typebar()` to any `MarkdownEditor` field to enable the symbol row:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->typebar()
```

### Custom keys

Pass an array of characters to override the default key set for a specific field:

```php
MarkdownEditor::make('content')
    ->typebar(['*', '_', '[', ']', '(', ')', '`'])
```

### Pairs

Use `->typebarPairs()` to define character pairs. When a paired key is tapped, both characters are inserted and the cursor is placed between them:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarPairs([
        '(' => ')',
        '[' => ']',
        '`' => '`',
    ])
```

### Collapsible

Use `->typebarCollapsible()` to let users collapse the symbol row down to a single toggle button. The collapsed/expanded state is saved to `localStorage` so it persists across page loads:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarCollapsible()
```

Pass `false` to explicitly disable collapsing on a field when it is enabled at the plugin or config level:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarCollapsible(false)
```

> [!NOTE]
> `->typebarCollapsible()` must be called before `->typebar()` when chaining both on the same field, because Filament resolves the first-registered attribute value. Alternatively, enable collapsible at the plugin or config level and it will apply automatically whenever `->typebar()` is called.

## Panel Plugin

Register the plugin in your panel provider to set panel-level defaults:

```php
use Awcodes\Typebar\TypebarPlugin;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            TypebarPlugin::make()
                ->keys(['*', '_', '[', ']', '(', ')', '`'])
                ->pairs([
                    '(' => ')',
                    '[' => ']',
                    '`' => '`',
                ])
                ->mobileOnly()
                ->collapsible()
        );
}
```

The plugin is optional. Without it, the package falls back to the published config values.

### `mobileOnly()`

By default Typebar only appears on coarse-pointer (touch) devices. Pass `false` to show it on all devices:

```php
TypebarPlugin::make()
    ->mobileOnly(false)
```

### `collapsible()`

Allow users to collapse the symbol row to a single toggle button. The preference is saved in `localStorage`:

```php
TypebarPlugin::make()
    ->collapsible()
```

## Configuration Priority

Options resolve in this order, from highest to lowest priority:

1. Field-level methods (`->typebar([...])`, `->typebarPairs([...])`, `->typebarCollapsible()`)
2. Plugin fluent options (`TypebarPlugin::make()->keys([...])->pairs([...])->collapsible()`)
3. Published config values (`config/typebar.php`)

## Configuration

```php
// config/typebar.php

return [
    'keys' => [
        '#', '*', '_', '!', '`', '[', ']', '(', ')', '{', '}',
        '<', '>', '-', '|', '~', '@', '$', ':', '=', '/', '"', "'",
    ],

    'pairs' => [
        // '(' => ')',
        // '[' => ']',
        // '`' => '`',
    ],

    'mobile_only' => true,

    'collapsible' => false,
];
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Adam Weston](https://github.com/awcodes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
