<?php

namespace App\Filament\Resources\Polis;

use App\Filament\Resources\Polis\Pages\CreatePoli;
use App\Filament\Resources\Polis\Pages\EditPoli;
use App\Filament\Resources\Polis\Pages\ListPolis;
use App\Filament\Resources\Polis\Schemas\PoliForm;
use App\Filament\Resources\Polis\Tables\PolisTable;
use App\Models\Poli;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PoliResource extends Resource
{
    protected static ?string $model = Poli::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PoliForm::configure($schema); // <-- Ini melempar form ke file PoliForm
    }

    public static function table(Table $table): Table
    {
        return PolisTable::configure($table); // <-- Ini melempar tabel ke file PolisTable
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPolis::route('/'),
            'create' => CreatePoli::route('/create'),
            'edit' => EditPoli::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
