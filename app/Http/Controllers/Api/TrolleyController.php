<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrolleyResource;
use App\Models\Trolley;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TrolleyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TrolleyResource::collection(
            Trolley::query()->orderBy('code')->get()
        );
    }
}
