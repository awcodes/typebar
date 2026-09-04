<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Post;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    /** @var class-string<Post> */
    protected $model = Post::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => 'Developing with Typebar',
            'content' => "# Typebar\n\nUse the symbol row to edit this Markdown content.",
        ];
    }
}
