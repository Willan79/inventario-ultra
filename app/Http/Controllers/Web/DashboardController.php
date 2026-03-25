<?php

namespace App\Http\Controllers\Web;

use App\Infrastructure\Persistence\Eloquent\Models\InventoryModel;
use App\Infrastructure\Persistence\Eloquent\Models\MovementModel;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\WarehouseModel;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends BaseController
{
    public function index(): View
    {
        $stats = [
            'totalProducts' => ProductModel::count(),
            'activeProducts' => ProductModel::where('is_active', true)->count(),
            'totalWarehouses' => WarehouseModel::where('is_active', true)->count(),
            'totalInventory' => InventoryModel::sum('quantity_available'),
            'totalValue' => InventoryModel::sum(DB::raw('quantity_available * average_cost')),
            'lowStockItems' => InventoryModel::selectRaw('inventories.*')
                ->join('products', 'inventories.product_id', '=', 'products.id')
                ->whereRaw('(inventories.quantity_available - inventories.quantity_reserved) <= products.min_stock_level')
                ->where('products.is_active', true)
                ->where('products.min_stock_level', '>', 0)
                ->count(),
            'todayMovements' => MovementModel::whereDate('created_at', today())->count(),
        ];

        $recentMovements = MovementModel::with(['product', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $lowStockItems = InventoryModel::with(['product', 'warehouse'])
            ->selectRaw('inventories.*, (inventories.quantity_available - inventories.quantity_reserved) as available')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereRaw('(inventories.quantity_available - inventories.quantity_reserved) <= products.min_stock_level')
            ->where('products.is_active', true)
            ->where('products.min_stock_level', '>', 0)
            ->orderBy('available', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentMovements', 'lowStockItems'));
    }
}
