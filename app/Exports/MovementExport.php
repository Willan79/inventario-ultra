<?php

namespace App\Exports;

use App\Infrastructure\Persistence\Eloquent\Models\MovementModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MovementExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = MovementModel::with(['product', 'warehouse']);

        if (!empty($this->filters['product_id'])) {
            $query->where('product_id', $this->filters['product_id']);
        }

        if (!empty($this->filters['warehouse_id'])) {
            $query->where('warehouse_id', $this->filters['warehouse_id']);
        }

        if (!empty($this->filters['movement_type'])) {
            $query->where('movement_type', $this->filters['movement_type']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from'] . ' 00:00:00');
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        $typeLabels = [
            'in' => 'Entrada',
            'out' => 'Salida',
            'transfer' => 'Transferencia',
            'adjustment' => 'Ajuste',
            'return' => 'Devolución',
        ];

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) use ($typeLabels) {
                return [
                    $item->created_at->format('d/m/Y H:i'),
                    $item->product?->name ?? 'N/A',
                    $item->warehouse?->name ?? 'N/A',
                    $typeLabels[$item->movement_type] ?? $item->movement_type,
                    $item->quantity,
                    $item->previous_quantity,
                    $item->new_quantity,
                    $item->notes ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Producto',
            'Almacén',
            'Tipo',
            'Cantidad',
            'Stock Anterior',
            'Stock Nuevo',
            'Notas',
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
