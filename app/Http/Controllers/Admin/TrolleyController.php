<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trolley;
use App\Services\TrolleyQrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class TrolleyController extends Controller
{
    public function __construct(private readonly TrolleyQrCodeService $qrCodeService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = $request->query('status');

        if (! in_array($status, ['in', 'out', null], true)) {
            $status = null;
        }

        $trolleys = Trolley::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('code', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('kind', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('admin.trolleys.index', [
            'trolleys' => $trolleys,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.trolleys.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:trolleys,code'],
            'type' => ['required', Rule::in(Trolley::TYPES)],
            'kind' => ['required', Rule::in(Trolley::KINDS)],
            'status' => ['required', Rule::in(['in', 'out'])],
            'notes' => ['nullable', 'string'],
        ]);

        $trolley = Trolley::query()->create($data);
        $qrPath = $this->qrCodeService->refresh($trolley);
        $trolley->forceFill(['qr_code_path' => $qrPath])->save();

        return redirect()
            ->route('trolleys.index')
            ->with('status', 'Troli berhasil ditambahkan.');
    }

    public function edit(Trolley $trolley): View
    {
        return view('admin.trolleys.edit', [
            'trolley' => $trolley,
        ]);
    }

    public function update(Request $request, Trolley $trolley): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('trolleys', 'code')->ignore($trolley->id)],
            'type' => ['required', Rule::in(Trolley::TYPES)],
            'kind' => ['required', Rule::in(Trolley::KINDS)],
            'status' => ['required', Rule::in(['in', 'out'])],
            'notes' => ['nullable', 'string'],
        ]);

        $codeChanged = $trolley->code !== $data['code'];

        $trolley->update($data);
        $trolley->refresh();

        if ($codeChanged || ! $trolley->qr_code_path) {
            $qrPath = $this->qrCodeService->refresh($trolley);
            $trolley->forceFill(['qr_code_path' => $qrPath])->save();
        }

        return redirect()
            ->route('trolleys.index')
            ->with('status', 'Troli berhasil diperbarui.');
    }

    public function destroy(Trolley $trolley): RedirectResponse
    {
        if ($trolley->qr_code_path) {
            Storage::disk('public')->delete($trolley->qr_code_path);
        }

        $trolley->delete();

        return redirect()
            ->route('trolleys.index')
            ->with('status', 'Troli berhasil dihapus.');
    }

    public function print(Request $request): View|Response
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->filter()
            ->map(fn (string $value) => (int) $value)
            ->filter()
            ->unique()
            ->values();

        $query = Trolley::query()->orderBy('code');

        if ($ids->isNotEmpty()) {
            $query->whereIn('id', $ids);
        }

        $trolleys = $query->get()
            ->filter(fn (Trolley $trolley) => filled($trolley->qr_code_path))
            ->values();

        if ($trolleys->isEmpty()) {
            return redirect()
                ->route('trolleys.qr.index')
                ->with('status', 'Troli belum memiliki QR code yang dapat dicetak.');
        }

        return view('admin.trolleys.print', [
            'trolleys' => $trolleys,
            'selectedCount' => $ids->isNotEmpty() ? $trolleys->count() : null,
        ]);
    }
}
