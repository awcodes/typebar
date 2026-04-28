<?php

declare(strict_types=1);

use Awcodes\Typebar\TypebarServiceProvider;

test('TypebarServiceProvider getPlugin returns null outside panel context', function () {
    expect(TypebarServiceProvider::getPlugin())->toBeNull();
});
