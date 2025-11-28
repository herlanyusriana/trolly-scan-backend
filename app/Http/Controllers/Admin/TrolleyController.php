<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trolley;
use App\Services\TrolleyQrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
            ->with('latestMovement')
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

    public function export(Request $request): StreamedResponse
    {
        $search = trim((string) $request->query('q'));
        $status = $request->query('status');

        if (! in_array($status, ['in', 'out', null], true)) {
            $status = null;
        }

        $trolleys = Trolley::query()
            ->with('latestMovement')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('code', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('kind', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('code')
            ->get();

        $filename = 'trolleys-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($trolleys): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Code',
                'Type',
                'Kind',
                'Status',
                'Capacity',
                'Location',
                'Notes',
                'Last Movement Status',
                'Last Movement At',
                'Duration (since status)',
            ]);

            foreach ($trolleys as $trolley) {
                $lastStatus = $trolley->last_movement_status ? strtoupper($trolley->last_movement_status) : '-';
                $lastAt = $trolley->last_movement_at?->format('Y-m-d H:i:s') ?? '-';

                fputcsv($handle, [
                    $trolley->id,
                    $trolley->code,
                    $trolley->type,
                    $trolley->kind,
                    $trolley->status,
                    $trolley->capacity,
                    $trolley->location,
                    $trolley->notes,
                    $lastStatus,
                    $lastAt,
                    $trolley->status_duration_label ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }

    public function exportXlsx(Request $request): StreamedResponse
    {
        $search = trim((string) $request->query('q'));
        $status = $request->query('status');

        if (! in_array($status, ['in', 'out', null], true)) {
            $status = null;
        }

        $trolleys = Trolley::query()
            ->with('latestMovement')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('code', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('kind', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('code')
            ->get();

        $rows = [];
        $rows[] = [
            'ID',
            'Code',
            'Type',
            'Kind',
            'Status',
            'Capacity',
            'Location',
            'Notes',
            'Last Movement Status',
            'Last Movement At',
            'Duration (since status)',
        ];

        foreach ($trolleys as $trolley) {
            $rows[] = [
                $trolley->id,
                $trolley->code,
                $trolley->type,
                $trolley->kind,
                $trolley->status,
                $trolley->capacity,
                $trolley->location,
                $trolley->notes,
                $trolley->last_movement_status ? strtoupper($trolley->last_movement_status) : '-',
                $trolley->last_movement_at?->format('Y-m-d H:i:s') ?? '-',
                $trolley->status_duration_label ?? '-',
            ];
        }

        $filename = 'trolleys-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($rows): void {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
            $zip = new \ZipArchive();
            $zip->open($tmp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
            $zip->addFromString('_rels/.rels', $this->relsXml());
            $zip->addFromString('docProps/app.xml', $this->appXml());
            $zip->addFromString('docProps/core.xml', $this->coreXml());
            $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
            $zip->addFromString('xl/workbook.xml', $this->workbookXml());
            $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($rows));

            $zip->close();

            $out = fopen('php://output', 'wb');
            $file = fopen($tmp, 'rb');
            stream_copy_to_stream($file, $out);
            fclose($file);
            fclose($out);
            @unlink($tmp);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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

    public function print(Request $request): View|RedirectResponse
    {
        $ids = $this->resolvePrintableIds($request);

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

    private function resolvePrintableIds(Request $request): Collection
    {
        $rawIds = collect();

        $idsParam = $request->query('ids');
        $singleIdParam = $request->query('id');

        if ($idsParam !== null) {
            $rawIds = $rawIds->merge(is_array($idsParam) ? $idsParam : explode(',', (string) $idsParam));
        }

        if ($singleIdParam !== null) {
            $rawIds = $rawIds->merge(is_array($singleIdParam) ? $singleIdParam : [$singleIdParam]);
        }

        return $rawIds
            ->map(fn ($value) => (int) $value)
            ->filter(fn (int $value) => $value > 0)
            ->unique()
            ->values();
    }

    private function colLetter(int $index): string
    {
        $dividend = $index + 1;
        $columnName = '';

        while ($dividend > 0) {
            $modulo = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend = (int) (($dividend - $modulo) / 26);
        }

        return $columnName;
    }

    private function sheetXml(array $rows): string
    {
        $rowXml = [];

        foreach ($rows as $rowIndex => $row) {
            $cells = [];
            foreach ($row as $colIndex => $value) {
                $cellRef = $this->colLetter($colIndex) . ($rowIndex + 1);
                $escaped = htmlspecialchars((string) $value, ENT_XML1);
                $cells[] = "<c r=\"{$cellRef}\" t=\"inlineStr\"><is><t>{$escaped}</t></is></c>";
            }
            $rowNumber = $rowIndex + 1;
            $rowXml[] = "<row r=\"{$rowNumber}\">" . implode('', $cells) . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . implode('', $rowXml) . '</sheetData>'
            . '</worksheet>';
    }

    private function contentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
    }

    private function relsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    private function workbookRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML;
    }

    private function workbookXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Trolleys" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML;
    }

    private function appXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>In-Out Trolley</Application>
    <DocSecurity>0</DocSecurity>
    <ScaleCrop>false</ScaleCrop>
    <HeadingPairs>
        <vt:vector size="2" baseType="variant">
            <vt:variant>
                <vt:lpstr>Worksheets</vt:lpstr>
            </vt:variant>
            <vt:variant>
                <vt:i4>1</vt:i4>
            </vt:variant>
        </vt:vector>
    </HeadingPairs>
    <TitlesOfParts>
        <vt:vector size="1" baseType="lpstr">
            <vt:lpstr>Trolleys</vt:lpstr>
        </vt:vector>
    </TitlesOfParts>
    <Company></Company>
    <LinksUpToDate>false</LinksUpToDate>
    <SharedDoc>false</SharedDoc>
    <HyperlinksChanged>false</HyperlinksChanged>
    <AppVersion>1.0</AppVersion>
</Properties>
XML;
    }

    private function coreXml(): string
    {
        $now = now()->toIso8601String();

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>In-Out Trolley</dc:creator>
    <cp:lastModifiedBy>In-Out Trolley</cp:lastModifiedBy>
    <dcterms:created xsi:type="dcterms:W3CDTF">{$now}</dcterms:created>
    <dcterms:modified xsi:type="dcterms:W3CDTF">{$now}</dcterms:modified>
</cp:coreProperties>
XML;
    }
}
