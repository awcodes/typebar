---
title: Typebar
description: A mobile-friendly Markdown symbol row for Filament's MarkdownEditor field.
---

# Typebar

Typebar adds a row of Markdown symbols above Filament's native `MarkdownEditor`. It behaves like a keyboard accessory row: tapping a key inserts that literal character at the cursor.

It exists because the characters Markdown leans on — `#`, `*`, `_`, backticks, brackets — are buried behind modifier layers on a phone keyboard. Typebar puts them one tap away.

> [!WARNING]
> Typebar is a work in progress and is not ready for production use. Please report any issues you hit.

## Requirements

- PHP 8.2 or later
- Filament v4 or v5

## How it is configured

Every option can be set in three places, and they resolve from most to least specific:

1. **On the field** — `->typebar([...])`, `->typebarPairs([...])`, `->typebarCollapsible()`
2. **On the panel plugin** — `TypebarPlugin::make()->keys([...])->pairs([...])->collapsible()`
3. **In the published config** — `config/typebar.php`

The plugin is optional; without it the package reads the config directly.

## Where to go next

- [Installation](installation.md) — install the package and publish its config.
- [Usage](usage.md) — enable the row on a field and customise its keys.
- [Configuration](configuration.md) — panel-wide defaults and the config file.
