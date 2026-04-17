<?php

namespace App\Filament\Resources\Obats\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ObatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('nama_obat')
                    ->required()
                    ->maxLength(255),
                Select::make('kategori')
                    ->options([
                        'Antibiotic' => 'Antibiotic',
                        'Analgesic' => 'Analgesic',
                        'Vitamin' => 'Vitamin',
                        'Syrup' => 'Syrup',
                    ])
                    ->searchable(),
                TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
