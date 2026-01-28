<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AdminNotificationResource\Pages\ListAdminNotifications;
use App\Filament\Resources\AdminNotificationResource\Pages\CreateAdminNotification;
use App\Filament\Resources\AdminNotificationResource\Pages\EditAdminNotification;
use App\Filament\Resources\AdminNotificationResource\Pages;
use App\Filament\Resources\AdminNotificationResource\RelationManagers;
use App\Models\AdminNotification;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminNotificationResource extends Resource
{
    protected static ?string $model = AdminNotification::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paper-airplane';
    protected static string | \UnitEnum | null $navigationGroup = 'Miscellaneous';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('notify_text')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('notify_type')
                    ->maxLength(255),
                TextInput::make('notify_url')
                    ->maxLength(255),
                Toggle::make('notify_read')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('notify_text')
                    ->searchable(),
                TextColumn::make('notify_type')
                    ->searchable(),
                IconColumn::make('notify_read')
                    ->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => ListAdminNotifications::route('/'),
            'create' => CreateAdminNotification::route('/create'),
            'edit' => EditAdminNotification::route('/{record}/edit'),
        ];
    }
}
