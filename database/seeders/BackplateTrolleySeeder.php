<?php

namespace Database\Seeders;

use App\Models\Trolley;
use App\Services\TrolleyQrCodeService;
use Illuminate\Database\Seeder;

class BackplateTrolleySeeder extends Seeder
{
    public function run(): void
    {
        $internalCodes = [
            'B002', 'B005', 'B014', 'B015', 'B032', 'B033', 'B035', 'B038', 'B039', 'B040',
            'B041', 'B043', 'B048', 'B052', 'B055', 'B059', 'B062', 'B064', 'B065', 'B069',
            'B070', 'B071', 'B072', 'B073', 'B082',
        ];

        $externalCodes = [
            'B001', 'B003', 'B004', 'B006', 'B007', 'B008', 'B009', 'B010', 'B011', 'B012',
            'B016', 'B017', 'B018', 'B019', 'B020', 'B021', 'B022', 'B023', 'B024', 'B025',
            'B026', 'B027', 'B028', 'B029', 'B030', 'B031', 'B034', 'B036', 'B037', 'B042',
            'B044', 'B045', 'B046', 'B047', 'B049', 'B050', 'B051', 'B053', 'B054', 'B056',
            'B057', 'B058', 'B060', 'B061', 'B063', 'B066', 'B067', 'B068', 'B074', 'B075',
            'B076', 'B077', 'B078', 'B079', 'B080', 'B081', 'B083', 'B084', 'B085', 'B086',
            'B087', 'B088', 'B089', 'B090', 'B091', 'B092', 'B093',
        ];

        /** @var TrolleyQrCodeService $qrService */
        $qrService = app(TrolleyQrCodeService::class);

        $this->seedGroup($externalCodes, 'external', $qrService);
        $this->seedGroup($internalCodes, 'internal', $qrService);
    }

    protected function seedGroup(array $codes, string $type, TrolleyQrCodeService $qrService): void
    {
        foreach ($codes as $code) {
            $trolley = Trolley::query()->firstOrCreate(
                ['code' => $code],
                [
                    'type' => $type,
                    'kind' => 'backplate',
                    'status' => 'in',
                ],
            );

            if (! $trolley->qr_code_path) {
                $trolley->forceFill(['qr_code_path' => $qrService->refresh($trolley)])->save();
            }
        }
    }
}
