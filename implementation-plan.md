# Typebar Implementation Plan

Build a Filament v4/v5 plugin from an empty directory.

## Goal

Create `awcodes/typebar`, a package that adds a mobile-friendly Markdown symbol row to Filament’s native `MarkdownEditor`.

Typebar behaves like a keyboard accessory row, not a formatting toolbar.

Tapping `*` inserts a literal `*`.

## Requirements

- Support Filament v4 and v5.
- Package name: `awcodes/typebar`.
- Namespace: `Awcodes\Typebar`.
- Do not use `Filament` in package-specific namespaces, class names, method names, config names, asset names, CSS class names, or package-specific naming.
- Use `TypebarPlugin` for the plugin class name.
- Use native JavaScript, not Alpine.
- Use esbuild to bundle/minify JavaScript.
- Register the compiled production JS file from `resources/dist/typebar.js`.
- Keep the feature opt-in per field.
- Support panel-level defaults through fluent methods on the plugin class.
- Fall back to config values when plugin fluent options are not set.
- Allow field-level overrides.
- Do not replace the native Markdown editor.
- Do not add toolbar behavior.
- Do not add Mason, Curator, or app-specific assumptions.
- Tests use Pest and should be set up using Testbench

## Configuration Priority

Configuration should resolve in this order:

1. Field-level methods
2. Plugin fluent options
3. Config defaults

Example:

```php
MarkdownEditor::make('content')
    ->typebar(['*', '_', '`'])
    ->typebarPairs([
        '`' => '`',
    ]);
```

This should override panel/plugin defaults for that field.

## Desired Usage

Basic field usage:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->typebar();
```

Custom field keys:

```php
MarkdownEditor::make('content')
    ->typebar(['*', '_', '[', ']', '(', ')', '`']);
```

Optional field pairs:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarPairs([
        '(' => ')',
        '[' => ']',
        '`' => '`',
    ]);
```

Panel-level defaults:

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
        );
}
```

Disable mobile-only behavior at the plugin level:

```php
TypebarPlugin::make()
    ->mobileOnly(false);
```

## Directory Structure

Create this structure:

```txt
typebar/
├── bin/
│   └── build.js
├── composer.json
├── package.json
├── config/
│   └── typebar.php
├── resources/
│   ├── css/
│   │   └── typebar.css
│   ├── dist/
│   │   └── typebar.js
│   └── js/
│       └── typebar.js
└── src/
    ├── TypebarPlugin.php
    └── TypebarServiceProvider.php
```

## composer.json

```json
{
  "name": "awcodes/typebar",
  "description": "Mobile Markdown symbol row for the Filament Markdown editor.",
  "type": "library",
  "keywords": [
        "awcodes",
        "laravel",
        "filament",
        "markdown",
    ],
    "homepage": "https://github.com/awcodes/typebar",
    "support": {
        "issues": "https://github.com/awcodes/typebar/issues",
        "source": "https://github.com/awcodes/typebar"
    },
    "license": "MIT",
    "authors": [
        {
            "name": "Adam Weston",
            "email": "adam@aw.codes",
            "role": "Developer"
        }
    ],
  "autoload": {
    "psr-4": {
      "Awcodes\\Typebar\\": "src/"
    }
  },
  "extra": {
    "laravel": {
      "providers": [
        "Awcodes\\Typebar\\TypebarServiceProvider"
      ]
    }
  },
  "require": {
    "php": "^8.2",
    "filament/filament": "^4.0|^5.0",
    "spatie/laravel-package-tools": "^1.16"
  },
  "require-dev": {
    "larastan/larastan": "^3.0",
    "laravel/pint": "^1.0",
    "nunomaduro/collision": "^8.0",
    "orchestra/testbench": "^9.0|^10.0|^11.0",
    "pestphp/pest": "^3.0|^4.0",
    "pestphp/pest-plugin-arch": "^3.0|^4.0",
    "pestphp/pest-plugin-laravel": "^3.0|^4.0",
    "pestphp/pest-plugin-livewire": "^3.0|^4.0",
    "rector/rector": "^2.0",
    "spatie/laravel-ray": "^1.26"
  },
  "scripts": {
    "post-autoload-dump": "@php ./vendor/bin/testbench package:discover --ansi",
    "lint": "pint",
    "refactor": "rector",
    "test:lint": "pint --test",
    "test:refactor": "rector --dry-run",
    "test:types": "phpstan analyse",
    "test:unit": "pest",
    "test": [
        "@test:refactor",
        "@test:lint",
        "@test:types",
        "@test:unit"
    ]
  },
    "config": {
    "sort-packages": true,
    "allow-plugins": {
        "composer/package-versions-deprecated": true,
        "pestphp/pest-plugin": true,
        "phpstan/extension-installer": true
    }
    },
  "minimum-stability": "stable",
  "prefer-stable": true
}
```

## package.json

```json
{
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "node bin/build.js --dev",
    "build": "node bin/build.js",
    "format": "prettier --write 'resources/**/*.blade.php' 'resources/**/*.css' 'resources/**/*.js'",
    "format:check": "prettier --check 'resources/**/*.blade.php' 'resources/**/*.css' 'resources/**/*.js'"
  },
  "devDependencies": {
    "esbuild": "^0.25.0",
    "prettier": "^3.6.0",
    "prettier-plugin-blade": "^2.1.21",
    "prettier-plugin-tailwindcss": "^0.6.11"
  }
}
```

## bin/build.js

```js
import * as esbuild from 'esbuild'

const isDev = process.argv.includes('--dev')

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    plugins: [
        {
            name: 'watchPlugin',
            setup(build) {
                build.onStart(() => {
                    console.log(
                        `Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`,
                    )
                })

                build.onEnd((result) => {
                    if (result.errors.length > 0) {
                        console.log(
                            `Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`,
                            result.errors,
                        )
                    } else {
                        console.log(
                            `Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`,
                        )
                    }
                })
            },
        },
    ],
}

compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/typebar.js'],
    outfile: './resources/dist/typebar.js',
}).then(() => {
    console.log('Build completed for typebar.js')
})
```

## config/typebar.php

```php
<?php

return [
    'keys' => [
        '*',
        '_',
        '#',
        '[',
        ']',
        '(',
        ')',
        '`',
        '>',
        '-',
        '|',
        '~',
    ],

    'pairs' => [
        // '(' => ')',
        // '[' => ']',
        // '`' => '`',
    ],

    'mobile_only' => true,
];
```

## src/TypebarPlugin.php

```php
<?php

namespace Awcodes\Typebar;

use Filament\Contracts\Plugin;
use Filament\Panel;

class TypebarPlugin implements Plugin
{
    protected ?array $keys = null;

    protected ?array $pairs = null;

    protected ?bool $mobileOnly = null;

    public function getId(): string
    {
        return 'typebar';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function keys(array $keys): static
    {
        $this->keys = $keys;

        return $this;
    }

    public function pairs(array $pairs): static
    {
        $this->pairs = $pairs;

        return $this;
    }

    public function mobileOnly(bool $condition = true): static
    {
        $this->mobileOnly = $condition;

        return $this;
    }

    public function getKeys(): array
    {
        return $this->keys ?? config('typebar.keys', []);
    }

    public function getPairs(): array
    {
        return $this->pairs ?? config('typebar.pairs', []);
    }

    public function isMobileOnly(): bool
    {
        return $this->mobileOnly ?? config('typebar.mobile_only', true);
    }
}
```

## src/TypebarServiceProvider.php

```php
<?php

namespace Awcodes\Typebar;

use Filament\Facades\Filament;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\HtmlString;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

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
}
```

## resources/js/typebar.js

```js
let row = null
let input = null

document.addEventListener('focusin', (event) => {
    const wrapper = event.target.closest('[data-typebar]')

    if (!wrapper) {
        return
    }

    if (wrapper.dataset.typebarMobile === 'true' && !isMobile()) {
        return
    }

    const element = findInput(wrapper)

    if (!element) {
        return
    }

    render(wrapper, element)
})

document.addEventListener('focusout', () => {
    setTimeout(() => {
        if (
            document.activeElement === input ||
            document.activeElement?.closest('.tb-row')
        ) {
            return
        }

        destroy()
    }, 150)
})

function isMobile() {
    return window.matchMedia('(pointer: coarse)').matches
}

function findInput(wrapper) {
    return wrapper.querySelector('textarea')
        || wrapper.querySelector('[contenteditable]')
        || wrapper.querySelector('[role="textbox"]')
}

function render(wrapper, element) {
    destroy()

    const keys = parse(wrapper.dataset.typebarKeys, [])
    const pairs = parse(wrapper.dataset.typebarPairs, {})

    if (!keys.length) {
        return
    }

    const container = document.createElement('div')

    container.className = 'tb-row'
    container.setAttribute('role', 'toolbar')
    container.setAttribute('aria-label', 'Typebar')

    keys.forEach((key) => {
        const button = document.createElement('button')

        button.type = 'button'
        button.textContent = key
        button.setAttribute('aria-label', `Insert ${key}`)

        button.addEventListener('pointerdown', (event) => {
            event.preventDefault()

            insert(element, key, pairs[key])
        })

        container.appendChild(button)
    })

    document.body.appendChild(container)

    row = container
    input = element
}

function insert(element, key, pair = null) {
    element.focus()

    if (element.tagName === 'TEXTAREA' || element.tagName === 'INPUT') {
        insertIntoInput(element, key, pair)

        return
    }

    insertIntoEditable(element, key)
}

function insertIntoInput(element, key, pair = null) {
    const start = element.selectionStart
    const end = element.selectionEnd
    const value = pair ? key + pair : key

    element.setRangeText(value, start, end, pair ? 'start' : 'end')

    if (pair) {
        element.selectionStart = start + key.length
        element.selectionEnd = start + key.length
    }

    element.dispatchEvent(new Event('input', { bubbles: true }))
}

function insertIntoEditable(element, key) {
    const selection = window.getSelection()

    if (!selection || selection.rangeCount === 0) {
        return
    }

    const range = selection.getRangeAt(0)

    range.deleteContents()
    range.insertNode(document.createTextNode(key))
    range.collapse(false)

    selection.removeAllRanges()
    selection.addRange(range)

    element.dispatchEvent(new Event('input', { bubbles: true }))
}

function destroy() {
    row?.remove()

    row = null
    input = null
}

function parse(value, fallback) {
    if (!value) {
        return fallback
    }

    try {
        return JSON.parse(value)
    } catch {
        return fallback
    }
}
```

## resources/css/typebar.css

```css
.tb-row {
    position: fixed;
    right: 0;
    bottom: env(keyboard-inset-height, 0px);
    left: 0;
    z-index: 9999;
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    padding: 0.375rem;
    padding-bottom: calc(0.375rem + env(safe-area-inset-bottom, 0px));
    border-top: 1px solid var(--gray-200, #e5e7eb);
    background: var(--gray-50, #f9fafb);
    -webkit-overflow-scrolling: touch;
}

.tb-row button {
    min-width: 2.25rem;
    height: 2.25rem;
    flex: 0 0 auto;
    border: 1px solid var(--gray-300, #d1d5db);
    border-radius: 0.5rem;
    background: #ffffff;
    color: inherit;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1;
}

.tb-row button:active {
    transform: translateY(1px);
}

@media (pointer: fine) {
    .tb-row {
        display: none;
    }
}
```

## Build Step

Run:

```bash
npm install
npm run build
```

This must create:

```txt
resources/dist/typebar.js
```

The service provider must register:

```php
Js::make('typebar', __DIR__.'/../resources/dist/typebar.js')
```

Do not register `resources/js/typebar.js` directly.

## Optional Panel Registration

The package should work without this, but consuming apps may register the plugin to customize panel-level defaults:

```php
use Awcodes\Typebar\TypebarPlugin;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            TypebarPlugin::make()
                ->keys([
                    '*',
                    '_',
                    '[',
                    ']',
                    '(',
                    ')',
                    '`',
                ])
                ->pairs([
                    '(' => ')',
                    '[' => ']',
                    '`' => '`',
                ])
                ->mobileOnly()
        );
}
```

## Field Usage

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->typebar();
```

Custom keys:

```php
MarkdownEditor::make('content')
    ->typebar([
        '*',
        '_',
        '[',
        ']',
        '(',
        ')',
        '`',
    ]);
```

Field-level pairs:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarPairs([
        '(' => ')',
        '[' => ']',
        '`' => '`',
    ]);
```

## Design Rules

- Native JavaScript only.
- No Alpine component.
- Data attributes are the bridge between PHP and JavaScript.
- Single global floating row.
- Literal key insertion only.
- No formatting actions.
- No Markdown toolbar behavior.
- No dependency on internal editor class names.
- No Mason, Curator, or app-specific behavior.
- `resources/js/typebar.js` is source.
- `resources/dist/typebar.js` is registered and production-ready.
- Plugin fluent options provide panel-level defaults.
- Field-level methods override plugin fluent options.
- Plugin fluent options override config values.
- Config values are the final fallback.

## Acceptance Criteria

The implementation is complete when:

- The package installs in a Filament v4 app.
- The package installs in a Filament v5 app.
- `MarkdownEditor::make('content')->typebar()` enables Typebar.
- Typebar appears when the editor receives focus.
- Typebar only appears on coarse-pointer devices when `mobile_only` is true.
- Tapping a key inserts the literal character at the cursor position.
- Focus remains in the editor after tapping a key.
- Livewire state updates after insertion.
- Custom field key arrays work.
- Field-level pairs work.
- Plugin-level `keys()` defaults work.
- Plugin-level `pairs()` defaults work.
- Plugin-level `mobileOnly()` defaults work.
- Config fallback works when the plugin is not registered.
- `npm run build` creates a minified `resources/dist/typebar.js`.
- The service provider registers `resources/dist/typebar.js`.
- The package uses `TypebarPlugin` for the plugin class name.
- The package does not use `Filament` in package-specific namespaces, class names, method names, config names, asset names, or CSS class names.