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
    protected static string | \UnitEnum | null $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'adv_username';
    protected static ?string $modelLabel = 'Frontend User';
    protected static ?string $pluralModelLabel = 'Frontend Users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('adv_first_name')
                    ->maxLength(255)
                    ->label('First Name')
                    ->default(null),
                TextInput::make('adv_last_name')
                    ->maxLength(255)
                    ->label('Last Name')    
                    ->default(null),
                TextInput::make('adv_username')
                    ->required()
                    ->label('Username')
                    ->maxLength(255),
                TextInput::make('adv_email')
                    ->email()
                    ->maxLength(255)
                    ->label('Email')
                    ->default(null),
                TextInput::make('adv_password')
                    ->password()
                    ->maxLength(255)
                    ->label('Password')
                    ->default(null),
                TextInput::make('adv_mobile')
                    ->maxLength(20)
                    ->label('Mobile')
                    ->default(null),
                FileUpload::make('image')
                    ->image()
                    ->default(null),
                Select::make('sources')
                    ->relationship(name: 'sources', titleAttribute: 'arabic_name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Select::make('categories')
                    ->relationship(name: 'categories', titleAttribute: 'arabic_name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Select::make('keywords')
                    ->relationship(name: 'keywords', titleAttribute: 'keyword_name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Toggle::make('email_notifications_enabled')
                    ->label('Email Notifications Enabled')
                    ->required(),
                Toggle::make('push_notifications_enabled')
                    ->label('Push Notifications Enabled')
                    ->required(),
                DateTimePicker::make('last_email_digest_sent')
                    ->label('Last Email Digest Sent')
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adv_first_name')
                    ->label('First Name')
                    ->searchable(),
                TextColumn::make('adv_last_name')
                    ->label('Last Name')
                    ->searchable(),
                TextColumn::make('adv_username')
                    ->label('Username')
                    ->searchable(),
                TextColumn::make('adv_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('adv_age')
                    ->label('Age')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adv_mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adv_lang')
                    ->label('Language')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('email_notifications_enabled')
                    ->label('Email Notifications')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('push_notifications_enabled')
                    ->label('Push Notifications')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_email_digest_sent')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
