<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        $drivers = Driver::query()
            ->orderBy('name')
            ->paginate(15);

        return view('admin.drivers.index', [
            'drivers' => $drivers,
        ]);
    }

    public function create(): View
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'license_number' => ['nullable', 'string', 'max:60'],
            'status' => ['required', Rule::in(Driver::STATUSES)],
            'notes' => ['nullable', 'string'],
        ]);

        Driver::query()->create($data);

        return redirect()
            ->route('drivers.index')
            ->with('status', 'Driver berhasil ditambahkan.');
    }

    public function edit(Driver $driver): View
    {
        return view('admin.drivers.edit', [
            'driver' => $driver,
        ]);
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'license_number' => ['nullable', 'string', 'max:60'],
            'status' => ['required', Rule::in(Driver::STATUSES)],
            'notes' => ['nullable', 'string'],
        ]);

        $driver->update($data);

        return redirect()
            ->route('drivers.index')
            ->with('status', 'Driver berhasil diperbarui.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->delete();

        return redirect()
            ->route('drivers.index')
            ->with('status', 'Driver berhasil dihapus.');
    }
}
