<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrolleyMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class MovementHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validateFilters($request);

        $query = $this->buildQuery($filters);

        $activeFilters = array_filter(
            $filters,
            static fn ($value) => $value !== null && $value !== ''
        );

        $movements = (clone $query)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC')
            ->paginate(15)
            ->appends($activeFilters);

        $stats = [
            'total' => (clone $query)->count(),
            'out' => (clone $query)->where('status', 'out')->count(),
            'in' => (clone $query)->where('status', 'in')->count(),
        ];

        return view('admin.history.index', [
            'filters' => $filters,
            'movements' => $movements,
            'stats' => $stats,
            'activeFilters' => $activeFilters,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validateFilters($request);
        $query = $this->buildQuery($filters)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC');

        $filename = 'trolley-history-' . now()->format('Ymd_His') . '.csv';

        return Response::streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Sequence',
                'Trolley',
                'Status',
                'Destination / Location',
                'Time',
                'Operator',
                'Vehicle',
                'Driver',
                'Notes',
            ]);

            $query->chunkById(500, function ($chunk) use ($handle): void {
                foreach ($chunk as $movement) {
                    $time = optional($movement->checked_out_at ?? $movement->created_at)->format('Y-m-d H:i:s') ?: '-';
                    $location = $movement->status === 'out'
                        ? ($movement->destination ?? '-')
                        : ($movement->return_location ?? $movement->destination ?? '-');

                    fputcsv($handle, [
                        $movement->sequence_number ?? '-',
                        $movement->trolley?->code ?? '-',
                        strtoupper($movement->status),
                        $location,
                        $time,
                        $movement->mobileUser?->name ?? '-',
                        $movement->vehicle?->plate_number ?? $movement->vehicle_snapshot ?? '-',
                        $movement->driver?->name ?? $movement->driver_snapshot ?? '-',
                        $movement->notes ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $filters = $this->validateFilters($request);
        $query = $this->buildQuery($filters);

        $activeFilters = array_filter(
            $filters,
            static fn ($value) => $value !== null && $value !== ''
        );

        $movements = (clone $query)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC')
            ->paginate(15)
            ->appends($activeFilters);

        $stats = [
            'total' => (clone $query)->count(),
            'out' => (clone $query)->where('status', 'out')->count(),
            'in' => (clone $query)->where('status', 'in')->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'table' => view('admin.history.partials.table-body', ['movements' => $movements])->render(),
            'pagination' => $movements->links()->render(),
        ]);
    }

    public function exportXlsx(Request $request): StreamedResponse
    {
        $filters = $this->validateFilters($request);
        $query = $this->buildQuery($filters)
            ->with(['trolley', 'mobileUser', 'vehicle', 'driver'])
            ->orderByRaw('COALESCE(checked_in_at, checked_out_at, created_at) DESC');

        $rows = [];
        $rows[] = [
            'Sequence',
            'Trolley',
            'Status',
            'Destination / Location',
            'Time',
            'Operator',
            'Vehicle',
            'Driver',
            'Notes',
        ];

        $query->chunkById(500, function ($chunk) use (&$rows): void {
            foreach ($chunk as $movement) {
                $time = optional($movement->checked_out_at ?? $movement->created_at)->format('Y-m-d H:i:s') ?: '-';
                $location = $movement->status === 'out'
                    ? ($movement->destination ?? '-')
                    : ($movement->return_location ?? $movement->destination ?? '-');

                $rows[] = [
                    $movement->sequence_number ?? '-',
                    $movement->trolley?->code ?? '-',
                    strtoupper($movement->status),
                    $location,
                    $time,
                    $movement->mobileUser?->name ?? '-',
                    $movement->vehicle?->plate_number ?? $movement->vehicle_snapshot ?? '-',
                    $movement->driver?->name ?? $movement->driver_snapshot ?? '-',
                    $movement->notes ?? '-',
                ];
            }
        });

        $filename = 'trolley-history-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($rows): void {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
            $zip = new ZipArchive();
            $zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE);

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

    /**
     * @return array<string, mixed>
     */
    protected function validateFilters(Request $request): array
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sequence_number' => ['nullable', 'integer', 'min:1'],
        ]);

        if (! filled($filters['date_from'] ?? null) && ! filled($filters['date_to'] ?? null)) {
            $filters['date_from'] = now()->subDays(7)->toDateString();
        }

        return $filters;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function buildQuery(array $filters): Builder
    {
        $query = TrolleyMovement::query();

        if ($from = Arr::get($filters, 'date_from')) {
            $query->whereDate('checked_out_at', '>=', $from);
        }

        if ($to = Arr::get($filters, 'date_to')) {
            $query->whereDate('checked_out_at', '<=', $to);
        }

        if ($sequence = Arr::get($filters, 'sequence_number')) {
            $query->where('sequence_number', (int) $sequence);
        }

        return $query;
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
        <sheet name="Movement History" sheetId="1" r:id="rId1"/>
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
            <vt:lpstr>Movement History</vt:lpstr>
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
