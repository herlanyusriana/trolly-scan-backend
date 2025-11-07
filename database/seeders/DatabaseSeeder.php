<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Driver;
use App\Models\Trolley;
use App\Models\Vehicle;
use App\Services\TrolleyQrCodeService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = Admin::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
        ]);

        /** @var TrolleyQrCodeService $qrService */
        $qrService = app(TrolleyQrCodeService::class);

        $trolleyA = Trolley::query()->create([
            'code' => 'BC-001',
            'type' => 'internal',
            'kind' => 'reinforce',
            'status' => 'in',
        ]);

        $trolleyA->forceFill(['qr_code_path' => $qrService->refresh($trolleyA)])->save();

        $trolleyB = Trolley::query()->create([
            'code' => 'BC-002',
            'type' => 'external',
            'kind' => 'backplate',
            'status' => 'in',
        ]);

        $trolleyB->forceFill(['qr_code_path' => $qrService->refresh($trolleyB)])->save();

        Vehicle::query()->create([
            'plate_number' => 'B 1234 CD',
            'name' => 'Truk Box 1',
            'category' => 'Truck',
            'status' => 'available',
        ]);

        Driver::query()->create([
            'name' => 'Driver Contoh',
            'phone' => '08123456789',
            'license_number' => 'SIM B 123456',
            'status' => 'active',
        ]);

        $this->command?->info(sprintf('Default admin created: %s / password', $admin->email));
    }
}
