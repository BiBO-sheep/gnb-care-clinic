<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrescriptionResource\Pages;
use App\Models\Prescription;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Rekap Obat Terjual';
    protected static ?string $modelLabel = 'Obat Terjual';
    protected static ?string $pluralModelLabel = 'Rekap Obat Terjual';
    protected static ?int $navigationSort = 4;

    // Menonaktifkan fitur Create agar Read-Only
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Hanya tampilkan obat dari tagihan yang sudah LUNAS (paid)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('medical_record.appointment.invoice', function($q) {
                $q->where('status', 'paid');
            })->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Terjual')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('medicine_name')
                    ->label('Nama Obat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dosage')
                    ->label('Jumlah / Dosis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rules')
                    ->label('Aturan Pakai'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga/Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('medical_record.appointment.user.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrescriptions::route('/'),
        ];
    }
}
