---
title: Configuration
description: Set Typebar's defaults panel-wide with the plugin or application-wide in the config file.
---

# Configuration

Field-level methods cover one editor. For defaults across a panel or the whole application, use the plugin or the config file.

## Panel plugin

Register the plugin in a panel provider to set that panel's defaults:

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

The plugin is optional. Anything you leave off it falls through to the config file.

| Method | Effect |
|---|---|
| `keys()` | The symbols shown in the row. |
| `pairs()` | Characters inserted in twos, cursor placed between them. |
| `mobileOnly()` | Restrict the row to touch devices. Defaults to `true`. |
| `collapsible()` | Allow collapsing to a single toggle. Defaults to `false`. |

### Showing the row on desktop

Typebar only renders on coarse-pointer devices unless told otherwise, which is why it can look like nothing happened when testing on a laptop:

```php
TypebarPlugin::make()
    ->mobileOnly(false);
```

## Config file

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

`pairs` ships commented out, so no key is paired until you enable it.

## Resolution order

Each option is resolved independently, from most specific to least:

1. The field method — `->typebar([...])`, `->typebarPairs([...])`, `->typebarCollapsible()`
2. The panel plugin — `keys()`, `pairs()`, `mobileOnly()`, `collapsible()`
3. The config file

So a panel can set the key set while one field overrides just the pairs; the field does not have to restate everything.
