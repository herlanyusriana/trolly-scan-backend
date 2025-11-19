<?php

namespace App\Services;

use App\Models\Trolley;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 12,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'eccLevel' => QRCode::ECC_L,
        ]);

        $qrGenerator = new QRCode($options);
        $qrImage = $qrGenerator->render($payload);

        $disk->put($path, $qrImage);

        return $path;
    }
}
