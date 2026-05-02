<?php

declare(strict_types=1);

test('typebar config loads as an array', function () {
    expect(config('typebar'))->toBeArray();
});

test('typebar config has a keys array', function () {
    expect(config('typebar.keys'))->toBeArray();
});

test('typebar config keys contain expected defaults', function () {
    expect(config('typebar.keys'))
        ->toContain('#')
        ->toContain('*')
        ->toContain('_')
        ->toContain('!')
        ->toContain('`')
        ->toContain('[')
        ->toContain(']')
        ->toContain('(')
        ->toContain(')')
        ->toContain('{')
        ->toContain('}')
        ->toContain('<')
        ->toContain('>')
        ->toContain('-')
        ->toContain('|')
        ->toContain('~')
        ->toContain('@')
        ->toContain('$')
        ->toContain(':')
        ->toContain('=')
        ->toContain('/')
        ->toContain('"')
        ->toContain("'");
});

test('typebar config has a pairs array', function () {
    expect(config('typebar.pairs'))->toBeArray();
});

test('typebar config pairs is empty by default', function () {
    expect(config('typebar.pairs'))->toBeEmpty();
});

test('typebar config mobile_only is true by default', function () {
    expect(config('typebar.mobile_only'))->toBeTrue();
});

test('typebar config collapsible is false by default', function () {
    expect(config('typebar.collapsible'))->toBeFalse();
});
