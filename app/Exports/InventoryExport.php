<?php

namespace App\Exports;

use App\Infrastructure\Persistence\Eloquent\Models\InventoryModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InventoryExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return InventoryModel::with(['product', 'warehouse'])
            ->get()
            ->map(function ($item) {
                return [
                    $item->product?->sku ?? 'N/A',
                    $item->product?->name ?? 'N/A',
                    $item->warehouse?->name ?? 'N/A',
                    $item->quantity_available,
                    number_format($item->average_cost, 2),
                    number_format($item->quantity_available * $item->average_cost, 2),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Producto',
            'Almacén',
            'Cantidad',
            'Costo Promedio',
            'Valor Total',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
