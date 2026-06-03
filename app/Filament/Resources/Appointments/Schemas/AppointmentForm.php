<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Poli;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('user_id')
                    ->label('Pasien')
                    ->relationship('user', 'name', modifyQueryUsing: fn ($query) => $query->where('role', 'pasien'))
                    ->searchable()
                    ->required(),
                Select::make('poli_id')
                    ->label('Poli')
                    ->relationship('poli', 'nama')
                    ->searchable()
                    ->required(),
                Select::make('dokter_id')
                    ->label('Dokter')
                    ->relationship('dokter', 'name', modifyQueryUsing: fn ($query) => $query->where('role', 'dokter'))
                    ->searchable()
                    ->nullable(),
                TextInput::make('queue_number')
                    ->label('No. Antrian')
                    ->nullable(),
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required(),
                TimePicker::make('jam')
                    ->label('Jam')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'scheduled' => 'Scheduled',
                        'check_in' => 'Check In',
                        'pemeriksaan' => 'Pemeriksaan',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ])
                    ->default('scheduled')
                    ->required(),
            ]);
    }
}
