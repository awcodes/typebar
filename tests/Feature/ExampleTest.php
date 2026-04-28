<?php

it('can load the typebar config', function () {
    expect(config('typebar'))->toBeArray();
});
