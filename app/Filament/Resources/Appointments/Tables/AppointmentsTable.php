<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Invoice;
use App\Models\MedicalRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('poli.name')
                    ->label('Poli')
                    ->searchable(),
                TextColumn::make('dokter.name')
                    ->label('Dokter')
                    ->searchable(),
                TextColumn::make('queue_number')
                    ->label('No. Antrian'),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->sortable(),
                TextColumn::make('jam')
                    ->label('Jam'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'scheduled' => 'info',
                        'check_in' => 'warning',
                        'pemeriksaan' => 'primary',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'scheduled' => 'Scheduled',
                        'check_in' => 'Check In',
                        'pemeriksaan' => 'Pemeriksaan',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),

                // =====================================================
                // ACTION: Input Pemeriksaan & Tagihan
                // Modal form untuk admin menginput diagnosis, catatan
                // obat, biaya konsultasi, dan biaya obat sekaligus.
                // =====================================================
                Action::make('input_pemeriksaan')
                    ->label('Input Pemeriksaan & Tagihan')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['check_in', 'pemeriksaan']))
                    ->modalHeading('Input Pemeriksaan & Tagihan')
                    ->modalDescription('Isi hasil pemeriksaan dan tagihan untuk appointment ini.')
                    ->modalSubmitActionLabel('Simpan & Selesaikan')
                    ->form([
                        TextInput::make('diagnosis')
                            ->label('Diagnosis')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Demam Berdarah Dengue'),
                        Textarea::make('tindakan')
                            ->label('Tindakan')
                            ->rows(3)
                            ->placeholder('Contoh: Infus RL, pemeriksaan darah lengkap'),
                        Textarea::make('catatan_obat')
                            ->label('Catatan Obat')
                            ->rows(3)
                            ->placeholder('Contoh: Paracetamol 3x1, Amoxicillin 3x1'),
                        TextInput::make('biaya_konsultasi')
                            ->label('Biaya Konsultasi')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->placeholder('50000'),
                        TextInput::make('biaya_obat')
                            ->label('Biaya Obat')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->placeholder('75000'),
                    ])
                    ->action(function ($record, array $data): void {
                        // 1. Buat Medical Record
                        MedicalRecord::create([
                            'user_id' => $record->user_id,
                            'doctor_id' => $record->dokter_id,
                            'appointment_id' => $record->id,
                            'diagnosis' => $data['diagnosis'],
                            'tindakan' => $data['tindakan'] ?? null,
                            'catatan_obat' => $data['catatan_obat'] ?? null,
                        ]);

                        // 2. Buat Invoice / Tagihan
                        $totalKonsultasi = (float) $data['biaya_konsultasi'];
                        $totalObat = (float) $data['biaya_obat'];

                        Invoice::create([
                            'appointment_id' => $record->id,
                            'user_id' => $record->user_id,
                            'total_consultation' => $totalKonsultasi,
                            'total_medicines' => $totalObat,
                            'grand_total' => $totalKonsultasi + $totalObat,
                            'status' => 'unpaid',
                        ]);

                        // 3. Update status appointment menjadi 'selesai'
                        $record->update(['status' => 'selesai']);

                        // 4. Notifikasi sukses
                        Notification::make()
                            ->title('Pemeriksaan & Tagihan Berhasil Disimpan')
                            ->body("Appointment #{$record->id} telah diselesaikan. Total tagihan: Rp " . number_format($totalKonsultasi + $totalObat, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
