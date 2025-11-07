<?php

namespace Database\Seeders;

use App\Models\Trolley;
use App\Services\TrolleyQrCodeService;
use Illuminate\Database\Seeder;

class CompbaseTrolleySeeder extends Seeder
{
    public function run(): void
    {
        $internal = [
            'C004', 'C007', 'C008', 'C012', 'C013', 'C023', 'C024', 'C025', 'C026', 'C028',
            'C030', 'C031', 'C032', 'C033', 'C034', 'C035', 'C039', 'C040', 'C045', 'C046',
            'C047', 'C048', 'C049', 'C050', 'C051', 'C052', 'C053', 'C054',
        ];

        $external = [
            'C001', 'C002', 'C003', 'C005', 'C006', 'C009', 'C010', 'C011', 'C014', 'C015',
            'C016', 'C017', 'C018', 'C019', 'C020', 'C021', 'C022', 'C027', 'C029', 'C036',
            'C037', 'C038', 'C041', 'C042', 'C043', 'C044', 'C055', 'C056', 'C057', 'C058',
            'C059', 'C060', 'C061', 'C062', 'C063', 'C064',
        ];

        /** @var TrolleyQrCodeService $qrService */
        $qrService = app(TrolleyQrCodeService::class);

        $this->seedGroup($external, 'external', $qrService);
        $this->seedGroup($internal, 'internal', $qrService);
    }

    protected function seedGroup(array $codes, string $type, TrolleyQrCodeService $qrService): void
    {
        foreach ($codes as $code) {
            $trolley = Trolley::query()->firstOrCreate(
                ['code' => $code],
                [
                    'type' => $type,
                    'kind' => 'compbase',
                    'status' => 'in',
                ],
            );

            if (! $trolley->qr_code_path) {
                $trolley->forceFill(['qr_code_path' => $qrService->refresh($trolley)])->save();
            }
        }
    }
}
