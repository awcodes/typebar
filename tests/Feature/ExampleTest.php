<?php

declare(strict_types=1);

it('can load the typebar config', function () {
    expect(config('typebar'))->toBeArray();
});
