<?php

declare(strict_types=1);

use Awcodes\Focus\Card;
use Awcodes\Focus\Enums\Size;
use Awcodes\Focus\Enums\Viewport;
use Awcodes\Focus\Screenshot;
use Awcodes\Focus\ScreenshotSuite;
use Playwright\Page\PageInterface;

/*
 * Documentation screenshots for Typebar, generated with awcodes/focus from the Workbench
 * (run `composer build` first). The Workbench seeds one fixed post, and its panel turns
 * mobileOnly() off: Focus's viewport presets set dimensions only, not touch input, so a
 * mobile-only row would stay hidden at a phone width.
 */

// The row is rendered on focus and removed on blur, so the editor keeps focus through the capture.
$editor = '[data-focus="content-editor"] .CodeMirror';

// A click lands the cursor wherever the editor's centre falls, so place it at the end of the first paragraph.
// CodeMirror also blinks its own caret on a timer, which Focus's caret hiding doesn't reach: pin it visible, so
// the cursor the row inserts at is shown the same on every run.
$steadyCaret = function (PageInterface $page): void {
    $page->evaluate(<<<'JS'
        () => {
            const cm = document.querySelector('[data-focus="content-editor"] .CodeMirror').CodeMirror;
            cm.setCursor({ line: 2, ch: cm.getLine(2).length });
            const style = document.createElement('style');
            style.textContent = '.CodeMirror-cursors { visibility: visible !important; }';
            document.head.appendChild(style);
        }
        JS);
};

// Clicking scrolls the editor partway under the top bar in the short card viewport. Scroll so its label sits just
// below the top bar instead.
$editorUnderTopbar = function (PageInterface $page): void {
    $page->evaluate(<<<'JS'
        () => {
            const field = document.querySelector('[data-focus="content-editor"]');
            const topbar = document.querySelector('.fi-topbar');
            const label = 44;
            window.scrollTo(0, field.getBoundingClientRect().top + window.scrollY - topbar.offsetHeight - label);
        }
        JS);
};

// The awcodes card templates frame each screenshot at 1400x816. A half-size viewport in that shape keeps the
// editor narrow and the symbol row large; at scale 2 it still fills the slot at full resolution.
$card = [700, 408];

return ScreenshotSuite::make()
    ->screenshots([
        Screenshot::make('mobile')
            ->viewportSize(Viewport::Mobile)
            ->visit('/admin/posts/1/edit')
            ->click($editor)
            ->waitFor('.tb-row')
            ->ready($steadyCaret)
            ->keepInteractionState()
            ->viewport(),

        Screenshot::make('collapsed')
            ->viewportSize(Viewport::Mobile)
            ->visit('/admin/posts/1/edit')
            ->click($editor)
            ->waitFor('.tb-row')
            ->ready($steadyCaret)
            ->click('.tb-toggle-btn')
            ->waitFor('.tb-row.tb-collapsed')
            ->keepInteractionState()
            ->viewport(),

        Screenshot::make('desktop')
            ->viewportSize(1280, 720)
            ->visit('/admin/posts/1/edit')
            ->click($editor)
            ->waitFor('.tb-row')
            ->ready($steadyCaret)
            ->keepInteractionState()
            ->viewport(),

        // The share-image source. The two-up templates show it dark in slot 1 and light in slot 2, so it is
        // captured in both themes.
        Screenshot::make('card-editor')
            ->viewportSize(...$card)
            ->visit('/admin/posts/1/edit')
            ->click($editor)
            ->waitFor('.tb-row')
            ->ready($steadyCaret)
            ->ready($editorUnderTopbar)
            ->keepInteractionState()
            ->viewport(),
    ])
    ->cardTemplates('https://github.com/awcodes/focus-templates/tree/v1.1.1/dist')
    ->cards([
        // Open Graph and the GitHub social preview share one 2400x1260 template; GitHub crops 30px top and bottom.
        Card::make('social')
            ->template('two-up-wide')
            ->title('Typebar')
            ->screenshots(['card-editor', 'card-editor'])
            ->sizes([Size::OpenGraph, Size::GitHubSocial]),

        // The Filament plugin directory's 2560x1440 thumbnail.
        Card::make('thumbnail')
            ->template('two-up')
            ->title('Typebar')
            ->screenshots(['card-editor', 'card-editor'])
            ->sizes([Size::Filament]),
    ]);
