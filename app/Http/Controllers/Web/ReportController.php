<?php

namespace App\Http\Controllers\Web;

use App\Exports\InventoryExport;
use App\Exports\MovementExport;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\WarehouseModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function inventory()
    {
        return Excel::download(new InventoryExport(), 'inventario_' . date('Y-m-d') . '.xlsx');
    }

    public function movements(Request $request)
    {
        $filters = $request->only(['product_id', 'warehouse_id', 'movement_type', 'date_from', 'date_to']);
        
        $filename = 'movimientos';
        if (!empty($filters['date_from'])) {
            $filename .= '_desde_' . $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $filename .= '_hasta_' . $filters['date_to'];
        }
        $filename .= '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new MovementExport($filters), $filename);
    }

    public function products()
    {
        $products = ProductModel::with('category')->get()->map(function ($product) {
            return [
                $product->sku,
                $product->name,
                $product->category?->name ?? 'Sin categoría',
                $product->unit_of_measure,
                $product->min_stock_level,
                $product->is_active ? 'Activo' : 'Inactivo',
            ];
        });

        return Excel::download(new class($products) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return $this->data; }
            public function headings(): array {
                return ['SKU', 'Nombre', 'Categoría', 'Und. Medida', 'Stock Mínimo', 'Estado'];
            }
        }, 'productos_' . date('Y-m-d') . '.xlsx');
    }

    public function warehouses()
    {
        $warehouses = WarehouseModel::withCount('inventories')->get()->map(function ($warehouse) {
            return [
                $warehouse->code,
                $warehouse->name,
                $warehouse->location ?? '-',
                $warehouse->inventories_count,
                $warehouse->is_active ? 'Activo' : 'Inactivo',
            ];
        });

        return Excel::download(new class($warehouses) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return $this->data; }
            public function headings(): array {
                return ['Código', 'Nombre', 'Ubicación', 'Total Productos', 'Estado'];
            }
        }, 'almacenes_' . date('Y-m-d') . '.xlsx');
    }
}
