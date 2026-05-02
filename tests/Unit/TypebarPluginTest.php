<?php

declare(strict_types=1);

use Awcodes\Typebar\TypebarPlugin;

test('getId returns typebar', function () {
    expect(app(TypebarPlugin::class)->getId())->toBe('typebar');
});

test('make returns a TypebarPlugin instance', function () {
    expect(TypebarPlugin::make())->toBeInstanceOf(TypebarPlugin::class);
});

test('keys is fluent', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->keys(['*']))->toBe($plugin);
});

test('pairs is fluent', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->pairs(['(' => ')']))->toBe($plugin);
});

test('mobileOnly is fluent', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->mobileOnly(false))->toBe($plugin);
});

test('getKeys falls back to config when not set', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->getKeys())->toBe(config('typebar.keys'));
});

test('getPairs falls back to config when not set', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->getPairs())->toBe(config('typebar.pairs'));
});

test('isMobileOnly falls back to config when not set', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->isMobileOnly())->toBe(config('typebar.mobile_only'));
});

test('getKeys returns plugin keys when set', function () {
    $plugin = app(TypebarPlugin::class)->keys(['*', '~']);

    expect($plugin->getKeys())->toBe(['*', '~']);
});

test('getPairs returns plugin pairs when set', function () {
    $plugin = app(TypebarPlugin::class)->pairs(['[' => ']']);

    expect($plugin->getPairs())->toBe(['[' => ']']);
});

test('isMobileOnly returns false when set to false', function () {
    $plugin = app(TypebarPlugin::class)->mobileOnly(false);

    expect($plugin->isMobileOnly())->toBeFalse();
});

test('isMobileOnly returns true when set to true', function () {
    $plugin = app(TypebarPlugin::class)->mobileOnly(true);

    expect($plugin->isMobileOnly())->toBeTrue();
});

test('mobileOnly defaults to true', function () {
    expect(app(TypebarPlugin::class)->mobileOnly()->isMobileOnly())->toBeTrue();
});

test('collapsible is fluent', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->collapsible(true))->toBe($plugin);
});

test('isCollapsible falls back to config when not set', function () {
    $plugin = app(TypebarPlugin::class);

    expect($plugin->isCollapsible())->toBe(config('typebar.collapsible'));
});

test('isCollapsible returns true when set to true', function () {
    $plugin = app(TypebarPlugin::class)->collapsible(true);

    expect($plugin->isCollapsible())->toBeTrue();
});

test('isCollapsible returns false when set to false', function () {
    $plugin = app(TypebarPlugin::class)->collapsible(false);

    expect($plugin->isCollapsible())->toBeFalse();
});

test('collapsible defaults to false', function () {
    expect(app(TypebarPlugin::class)->collapsible()->isCollapsible())->toBeTrue();
});
