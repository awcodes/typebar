<?php

declare(strict_types=1);

use Filament\Forms\Components\MarkdownEditor;

test('typebar macro is registered on MarkdownEditor', function () {
    expect(MarkdownEditor::hasMacro('typebar'))->toBeTrue();
});

test('typebarPairs macro is registered on MarkdownEditor', function () {
    expect(MarkdownEditor::hasMacro('typebarPairs'))->toBeTrue();
});

test('typebar returns a MarkdownEditor instance', function () {
    $editor = MarkdownEditor::make('content')->typebar();

    expect($editor)->toBeInstanceOf(MarkdownEditor::class);
});

test('typebarPairs returns a MarkdownEditor instance', function () {
    $editor = MarkdownEditor::make('content')->typebarPairs(['(' => ')']);

    expect($editor)->toBeInstanceOf(MarkdownEditor::class);
});

test('typebar sets data-typebar attribute to true', function () {
    $attrs = MarkdownEditor::make('content')->typebar()->getExtraAttributes();

    expect($attrs)->toHaveKey('data-typebar')
        ->and($attrs['data-typebar'])->toBe('true');
});

test('typebar sets data-typebar-mobile to true by default', function () {
    $attrs = MarkdownEditor::make('content')->typebar()->getExtraAttributes();

    expect($attrs)->toHaveKey('data-typebar-mobile')
        ->and($attrs['data-typebar-mobile'])->toBe('true');
});

test('typebar sets data-typebar-mobile to false when config is false', function () {
    config(['typebar.mobile_only' => false]);

    $attrs = MarkdownEditor::make('content')->typebar()->getExtraAttributes();

    expect($attrs['data-typebar-mobile'])->toBe('false');
});

test('typebar encodes config keys as JSON in data-typebar-keys', function () {
    $keys = config('typebar.keys');
    $attrs = MarkdownEditor::make('content')->typebar()->getExtraAttributes();

    expect((string) $attrs['data-typebar-keys'])->toBe(e(json_encode($keys)));
});

test('typebar accepts custom keys override', function () {
    $keys = ['*', '_', '~'];
    $attrs = MarkdownEditor::make('content')->typebar($keys)->getExtraAttributes();

    expect((string) $attrs['data-typebar-keys'])->toBe(e(json_encode($keys)));
});

test('typebar encodes config pairs as JSON in data-typebar-pairs', function () {
    $pairs = config('typebar.pairs');
    $attrs = MarkdownEditor::make('content')->typebar()->getExtraAttributes();

    expect((string) $attrs['data-typebar-pairs'])->toBe(e(json_encode($pairs)));
});

test('typebarPairs encodes pairs as JSON in data-typebar-pairs', function () {
    $pairs = ['(' => ')', '[' => ']'];
    $attrs = MarkdownEditor::make('content')->typebarPairs($pairs)->getExtraAttributes();

    expect((string) $attrs['data-typebar-pairs'])->toBe(e(json_encode($pairs)));
});

test('typebar and typebarPairs can be chained without throwing', function () {
    $editor = MarkdownEditor::make('content')
        ->typebar(['*', '_'])
        ->typebarPairs(['`' => '`']);

    expect($editor)->toBeInstanceOf(MarkdownEditor::class)
        ->and($editor->getExtraAttributes())->toHaveKey('data-typebar');
});

test('typebarPairs resolves the same whichever side of typebar it is chained on', function () {
    $pairs = ['(' => ')'];
    $expected = e(json_encode($pairs));

    $after = MarkdownEditor::make('content')->typebar()->typebarPairs($pairs);
    $before = MarkdownEditor::make('content')->typebarPairs($pairs)->typebar();

    expect((string) $after->getExtraAttributes()['data-typebar-pairs'])->toBe($expected)
        ->and((string) $before->getExtraAttributes()['data-typebar-pairs'])->toBe($expected);
});

test('typebarCollapsible resolves the same whichever side of typebar it is chained on', function () {
    $after = MarkdownEditor::make('content')->typebar()->typebarCollapsible();
    $before = MarkdownEditor::make('content')->typebarCollapsible()->typebar();

    expect($after->getExtraAttributes()['data-typebar-collapsible'])->toBe('true')
        ->and($before->getExtraAttributes()['data-typebar-collapsible'])->toBe('true');
});

test('typebar sets data-typebar-collapsible to false by default', function () {
    $attrs = MarkdownEditor::make('content')->typebar()->getExtraAttributes();

    expect($attrs)->toHaveKey('data-typebar-collapsible')
        ->and($attrs['data-typebar-collapsible'])->toBe('false');
});

test('typebar sets data-typebar-collapsible to true when config is true', function () {
    config(['typebar.collapsible' => true]);

    $attrs = MarkdownEditor::make('content')->typebar()->getExtraAttributes();

    expect($attrs['data-typebar-collapsible'])->toBe('true');
});

test('typebarCollapsible macro is registered on MarkdownEditor', function () {
    expect(MarkdownEditor::hasMacro('typebarCollapsible'))->toBeTrue();
});

test('typebarCollapsible returns a MarkdownEditor instance', function () {
    $editor = MarkdownEditor::make('content')->typebar()->typebarCollapsible();

    expect($editor)->toBeInstanceOf(MarkdownEditor::class);
});

test('typebarCollapsible sets data-typebar-collapsible to true', function () {
    $attrs = MarkdownEditor::make('content')->typebarCollapsible()->getExtraAttributes();

    expect($attrs['data-typebar-collapsible'])->toBe('true');
});

test('typebarCollapsible can be set to false explicitly', function () {
    $attrs = MarkdownEditor::make('content')->typebar()->typebarCollapsible(false)->getExtraAttributes();

    expect($attrs['data-typebar-collapsible'])->toBe('false');
});
