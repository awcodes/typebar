---
title: Usage
description: Enable the Typebar symbol row on a MarkdownEditor field and customise its keys.
---

# Usage

## Enabling the row

Add `->typebar()` to any `MarkdownEditor`:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->typebar();
```

With no argument the field uses the key set from the panel plugin, falling back to the config. See [Configuration](configuration.md).

> [!NOTE]
> By default the row only appears on coarse-pointer (touch) devices, so you will not see it on a desktop browser unless you turn that off with `mobileOnly(false)` on the plugin or `mobile_only` in the config.

## Custom keys

Pass an array to override the key set for one field:

```php
MarkdownEditor::make('content')
    ->typebar(['*', '_', '[', ']', '(', ')', '`']);
```

Keys are inserted literally at the cursor, so any character works — these are not commands.

## Pairs

`typebarPairs()` defines characters that come in twos. Tapping a paired key inserts both and places the cursor between them:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarPairs([
        '(' => ')',
        '[' => ']',
        '`' => '`',
    ]);
```

A key only needs to appear in `typebar()` to be shown; `typebarPairs()` decides what happens when it is tapped.

## Collapsible

`typebarCollapsible()` lets the reader collapse the row down to a single toggle button. The choice is stored in `localStorage`, so it survives page loads:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarCollapsible();
```

Pass `false` to turn collapsing off for one field when it is enabled at the plugin or config level:

```php
MarkdownEditor::make('content')
    ->typebar()
    ->typebarCollapsible(false);
```

Both modifiers refine what `->typebar()` set up, so reading them in that order matches what they do.
