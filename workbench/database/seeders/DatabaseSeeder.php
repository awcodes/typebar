<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Workbench\App\Models\Post;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $date = Carbon::parse('2026-01-01 09:00:00');

        Post::query()->forceCreate([
            'id' => 1,
            'title' => 'Writing on the go',
            'content' => implode("\n", [
                '# Writing on the go',
                '',
                'Phone keyboards hide the **symbols** Markdown needs.',
                '',
                '## Quick reference',
                '',
                '- *Emphasis* with `*` or `_`',
                '- [Links](https://example.com) with `[` and `(`',
                '- Inline `code` with backticks',
                '',
                'Tap a key in the row below to insert it at the cursor.',
            ]),
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
