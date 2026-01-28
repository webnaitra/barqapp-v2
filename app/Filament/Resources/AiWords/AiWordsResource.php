<?php

namespace App\Filament\Resources\AiWords;

use App\Filament\Resources\AiWords\Pages\CreateAiWords;
use App\Filament\Resources\AiWords\Pages\EditAiWords;
use App\Filament\Resources\AiWords\Pages\ListAiWords;
use App\Filament\Resources\AiWords\Schemas\AiWordsForm;
use App\Filament\Resources\AiWords\Tables\AiWordsTable;
use App\Models\AiWord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AiWordsResource extends Resource
{
    protected static ?string $model = AiWord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'word';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.news_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.ai_words');
    }

    public static function getModelLabel(): string
    {
        return __('filament.ai_word');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.ai_words');
    }

    public static function form(Schema $schema): Schema
    {
        return AiWordsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiWordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiWords::route('/'),
            'create' => CreateAiWords::route('/create'),
            'edit' => EditAiWords::route('/{record}/edit'),
        ];
    }
}
