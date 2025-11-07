<?php

namespace Database\Seeders;

use App\Models\Trolley;
use App\Services\TrolleyQrCodeService;
use Illuminate\Database\Seeder;

class ReinforceTrolleySeeder extends Seeder
{
    public function run(): void
    {
        $internal = [
            'R027', 'R028', 'R029', 'R030', 'R031', 'R032', 'R033', 'R034',
            'R036', 'R037', 'R038', 'R039', 'R041', 'R043',
        ];

        $external = [
            'R001', 'R002', 'R003', 'R004', 'R005', 'R006', 'R007', 'R008',
            'R009', 'R010', 'R011', 'R012', 'R013', 'R014', 'R015', 'R016',
            'R017', 'R018', 'R019', 'R020', 'R021', 'R022', 'R023', 'R024',
            'R025', 'R026', 'R035', 'R040', 'R042', 'R044', 'R045',
        ];

        /** @var TrolleyQrCodeService $qrService */
        $qrService = app(TrolleyQrCodeService::class);

        $this->seedGroup($external, 'external', $qrService);
        $this->seedGroup($internal, 'internal', $qrService);
    }

    protected function seedGroup(array $codes, string $type, TrolleyQrCodeService $qrService): void
    {
        foreach ($codes as $code) {
            $formattedCode = str_starts_with($code, 'R')
                ? 'RF' . substr($code, 1)
                : $code;

            $trolley = Trolley::query()->firstOrCreate(
                ['code' => $formattedCode],
                [
                    'type' => $type,
                    'kind' => 'reinforce',
                    'status' => 'in',
                ],
            );

            if (! $trolley->qr_code_path) {
                $trolley->forceFill(['qr_code_path' => $qrService->refresh($trolley)])->save();
            }
        }
    }
}
