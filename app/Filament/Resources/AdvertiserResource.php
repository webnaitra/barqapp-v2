<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers;
use App\Filament\Resources\AdvertiserResource\Pages\CreateAdvertiser;
use App\Filament\Resources\AdvertiserResource\Pages\EditAdvertiser;
use App\Filament\Resources\AdvertiserResource\Pages;
use App\Filament\Resources\AdvertiserResource\RelationManagers;
use App\Models\Advertiser;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdvertiserResource extends Resource
{
    protected static ?string $model = Advertiser::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'adv_username';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.administration');
    }
    
    public static function getModelLabel(): string
    {
        return __('filament.frontend_user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.frontend_users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('adv_first_name')->label(__('filament.adv_first_name'))
                    ->maxLength(255)
                    ->label(__('filament.first_name'))
                    ->default(null),
                TextInput::make('adv_last_name')->label(__('filament.adv_last_name'))
                    ->maxLength(255)
                    ->label(__('filament.last_name'))    
                    ->default(null),
                TextInput::make('adv_username')->label(__('filament.adv_username'))
                    ->required()
                    ->label(__('filament.username'))
                    ->maxLength(255),
                TextInput::make('adv_email')->label(__('filament.adv_email'))
                    ->email()
                    ->maxLength(255)
                    ->label(__('filament.email'))
                    ->default(null),
                TextInput::make('adv_password')->label(__('filament.adv_password'))
                    ->password()
                    ->maxLength(255)
                    ->label(__('filament.password'))
                    ->default(null),
                TextInput::make('adv_mobile')->label(__('filament.adv_mobile'))
                    ->maxLength(20)
                    ->label(__('filament.mobile'))
                    ->default(null),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->default(null),
                Select::make('sources')->label(__('filament.sources'))
                    ->relationship(name: 'sources', titleAttribute: 'arabic_name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Select::make('categories')->label(__('filament.categories'))
                    ->relationship(name: 'categories', titleAttribute: 'arabic_name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Select::make('keywords')->label(__('filament.keywords'))
                    ->relationship(name: 'keywords', titleAttribute: 'keyword_name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Toggle::make('email_notifications_enabled')->label(__('filament.email_notifications_enabled'))
                    ->label(__('filament.email_notifications_enabled'))
                    ->required(),
                Toggle::make('push_notifications_enabled')->label(__('filament.push_notifications_enabled'))
                    ->label(__('filament.push_notifications_enabled'))
                    ->required(),
                DateTimePicker::make('last_email_digest_sent')->label(__('filament.last_email_digest_sent'))
                    ->label(__('filament.last_email_digest_sent'))
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adv_first_name')->label(__('filament.adv_first_name'))
                    ->label(__('filament.first_name'))
                    ->searchable(),
                TextColumn::make('adv_last_name')->label(__('filament.adv_last_name'))
                    ->label(__('filament.last_name'))
                    ->searchable(),
                TextColumn::make('adv_username')->label(__('filament.adv_username'))
                    ->label(__('filament.username'))
                    ->searchable(),
                TextColumn::make('adv_email')->label(__('filament.adv_email'))
                    ->label(__('filament.email'))
                    ->searchable(),
                TextColumn::make('adv_age')->label(__('filament.adv_age'))
                    ->label(__('filament.age'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adv_mobile')->label(__('filament.adv_mobile'))
                    ->label(__('filament.mobile'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adv_lang')->label(__('filament.adv_lang'))
                    ->label(__('filament.language'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('email_notifications_enabled')->label(__('filament.email_notifications_enabled'))
                    ->label(__('filament.email_notifications'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('push_notifications_enabled')->label(__('filament.push_notifications_enabled'))
                    ->label(__('filament.push_notifications'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_email_digest_sent')->label(__('filament.last_email_digest_sent'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label(__('filament.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('filament.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->button()->outlined(),
                DeleteAction::make()->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdvertisers::route('/'),
            'create' => CreateAdvertiser::route('/create'),
            'edit' => EditAdvertiser::route('/{record}/edit'),
        ];
    }
}
