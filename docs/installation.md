---
title: Installation
description: Install Typebar and publish its config file.
---

# Installation

Install the package via Composer:

```bash
composer require awcodes/typebar
```

Then publish the config file:

```bash
php artisan vendor:publish --tag="typebar-config"
```

Publishing is worth doing up front, since the config is where the default key set lives — see [Configuration](configuration.md).

There is no theme CSS step. Typebar registers its stylesheet and script as Filament assets marked `loadedOnRequest()`, so they are pulled in only where an editor actually uses them.

With that done, enable the row on a field — see [Usage](usage.md).
