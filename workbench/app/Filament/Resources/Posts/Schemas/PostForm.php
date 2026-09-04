<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required(),
                MarkdownEditor::make('content')
                    ->typebar(['#', '*', '_', '[', ']', '(', ')', '`'])
                    ->typebarPairs(['(' => ')', '[' => ']', '`' => '`'])
                    ->typebarCollapsible()
                    ->columnSpanFull(),
            ]);
    }
}
