<?php

namespace App\Services;

use App\Models\Trolley;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TrolleyQrCodeService
{
    /**
     * Generate a QR code for the given trolley and return the stored path.
     */
    public function refresh(Trolley $trolley): string
    {
        $disk = Storage::disk('public');

        if ($trolley->qr_code_path && $disk->exists($trolley->qr_code_path)) {
            $disk->delete($trolley->qr_code_path);
        }

        $directory = 'trolleys';
        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $filename = sprintf('%s.png', Str::slug($trolley->code));
        $path = $directory.'/'.$filename;

        $payload = json_encode([
            'code' => $trolley->code,
            'type' => $trolley->type,
            'status' => $trolley->status,
            'generated_at' => now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE);

        $qrImage = QrCode::format('png')
            ->size(320)
            ->margin(2)
            ->generate($payload);

        $disk->put($path, $qrImage);

        return $path;
    }
}
