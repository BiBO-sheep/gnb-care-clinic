<?php

namespace App\Filament\Resources\Polis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PoliForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([ // Atau ->components([ jika bawaannya begitu
                TextInput::make('name')
                    ->required()
                    ->label('Nama Poli')
                    ->maxLength(255),
                TextInput::make('ruangan')
                    ->label('Lokasi Ruangan')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
