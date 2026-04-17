<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
   public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'dokter' => 'Dokter',
                        'pasien' => 'Pasien',
                    ])
                    ->required()
                    ->live(), // Membuat form interaktif
                TextInput::make('phone')->tel()->maxLength(255),
                Select::make('poli_id')
                    ->relationship('poli', 'name')
                    ->label('Pilih Poli (Khusus Dokter)')
                    ->visible(fn (Get $get) => $get('role') === 'dokter'), // Hanya muncul jika role = dokter
            ]);
    }
}
