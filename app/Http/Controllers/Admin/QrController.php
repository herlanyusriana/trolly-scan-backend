<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trolley;
use Illuminate\View\View;

class QrController extends Controller
{
    public function index(): View
    {
        $trolleys = Trolley::query()
            ->orderBy('code')
            ->get()
            ->filter(fn (Trolley $trolley) => filled($trolley->qr_code_path))
            ->values();

        return view('admin.qr.index', [
            'trolleys' => $trolleys,
        ]);
    }
}
