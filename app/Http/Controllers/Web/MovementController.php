<?php

namespace App\Http\Controllers\Web;

use App\Infrastructure\Persistence\Eloquent\Models\MovementModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\WarehouseModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Carbon\Carbon;

class MovementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['product_id', 'warehouse_id', 'movement_type', 'date_from', 'date_to', 'date_range']);
        $perPage = (int) $request->get('per_page', 25);
        
        $query = MovementModel::with(['product', 'warehouse']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if (!empty($filters['date_range'])) {
            switch ($filters['date_range']) {
                case 'today':
                    $filters['date_from'] = Carbon::today()->format('Y-m-d');
                    $filters['date_to'] = Carbon::today()->format('Y-m-d');
                    break;
                case 'week':
                    $filters['date_from'] = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $filters['date_to'] = Carbon::today()->format('Y-m-d');
                    break;
                case 'month':
                    $filters['date_from'] = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $filters['date_to'] = Carbon::today()->format('Y-m-d');
                    break;
            }
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        $products = ProductModel::active()->orderBy('name')->get();
        $warehouses = WarehouseModel::active()->orderBy('name')->get();
        
        return view('movements.index', compact('movements', 'products', 'warehouses', 'filters'));
    }
}
