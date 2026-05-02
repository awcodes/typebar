<?php

declare(strict_types=1);

namespace Awcodes\Typebar;

use Filament\Contracts\Plugin;
use Filament\Panel;

class TypebarPlugin implements Plugin
{
    protected ?array $keys = null;

    protected ?array $pairs = null;

    protected ?bool $mobileOnly = null;

    protected ?bool $collapsible = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'typebar';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function keys(array $keys): static
    {
        $this->keys = $keys;

        return $this;
    }

    public function pairs(array $pairs): static
    {
        $this->pairs = $pairs;

        return $this;
    }

    public function mobileOnly(bool $condition = true): static
    {
        $this->mobileOnly = $condition;

        return $this;
    }

    public function collapsible(bool $condition = true): static
    {
        $this->collapsible = $condition;

        return $this;
    }

    public function getKeys(): array
    {
        return $this->keys ?? config('typebar.keys', []);
    }

    public function getPairs(): array
    {
        return $this->pairs ?? config('typebar.pairs', []);
    }

    public function isMobileOnly(): bool
    {
        return $this->mobileOnly ?? config('typebar.mobile_only', true);
    }

    public function isCollapsible(): bool
    {
        return $this->collapsible ?? config('typebar.collapsible', false);
    }
}
