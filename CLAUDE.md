# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Typebar is a Laravel/Filament package that adds a mobile-friendly Markdown symbol row to Filament's `MarkdownEditor`. It is distributed as a Composer package and uses `spatie/laravel-package-tools` as the service provider base.

## Commands

### PHP

```bash
composer test           # rector dry-run + pint --test + phpstan + pest
composer test:unit      # pest only
composer test:lint      # pint --test (lint check)
composer test:refactor  # rector --dry-run
composer test:types     # phpstan analyse
composer lint           # pint (auto-fix formatting)
composer refactor       # rector (auto-fix)
composer serve          # build workbench then serve it at localhost
```

Run a single Pest test by file or filter:

```bash
vendor/bin/pest tests/Feature/MarkdownEditorMixinTest.php
vendor/bin/pest --filter "typebar sets data-typebar attribute"
```

### JavaScript

```bash
npm run build   # production build → resources/dist/typebar.js
npm run dev     # watch mode
npm run format  # prettier auto-fix
npm run format:check  # prettier check only
```

The JS entry point is `resources/js/typebar.js`; esbuild outputs to `resources/dist/typebar.js`.

## Architecture

### PHP side

| File | Role |
|------|------|
| `src/TypebarServiceProvider.php` | Registers Filament assets (JS + CSS) and applies the mixin to `MarkdownEditor` |
| `src/TypebarPlugin.php` | Optional Filament panel plugin — stores panel-level `keys`, `pairs`, `mobileOnly` overrides |
| `src/MarkdownEditorMixin.php` | Adds `typebar(?array $keys)` and `typebarPairs(array $pairs)` to `MarkdownEditor` via `mixin()` |
| `config/typebar.php` | Default keys, pairs, and `mobile_only` flag |

Configuration priority (highest → lowest): field-level call → `TypebarPlugin` fluent options → published config.

The mixin writes configuration into HTML `data-*` attributes (`data-typebar`, `data-typebar-keys`, `data-typebar-pairs`, `data-typebar-mobile`) on the editor wrapper element.

### JavaScript side

`resources/js/typebar.js` is a single vanilla-JS file with no framework dependency. It:

1. Listens for `focusin` on any `[data-typebar]` element.
2. Reads `data-typebar-keys` / `data-typebar-pairs` (JSON) and renders a floating `.tb-row` toolbar appended to `<body>`.
3. On button `pointerdown`, calls `insertIntoInput` (textarea/input via `setRangeText`) or `insertIntoEditable` (contenteditable via Selection API).
4. For pair keys, inserts both characters and moves the cursor between them.
5. Destroys the toolbar on `focusout` (with a 150 ms guard so clicks on toolbar buttons don't prematurely remove it).

### Testing

Tests live in `tests/Feature/` and `tests/Unit/` and use Pest + Orchestra Testbench. The `TestCase` is configured in `tests/Pest.php` and `tests/TestCase.php`. PHPStan runs at level 5 against `src/` and `config/`.
