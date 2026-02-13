<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductStatsController extends Controller
{


    public function index()
    {
        $stats = Product::select(
        'products.id',
        'products.name',
        DB::raw('COALESCE(SUM(deal_products.quantity),0) as total_quantity'),
        DB::raw('COALESCE(SUM(deal_products.total),0) as total_revenue'),
        DB::raw('COUNT(DISTINCT deal_products.deal_id) as total_deals')
    )
    ->leftJoin('deal_products', 'products.id', '=', 'deal_products.product_id')
    ->groupBy('products.id', 'products.name')
    ->orderByDesc('total_revenue')
    ->get();


        return inertia('CRM/ProductStats', [
            'stats' => $stats
        ]);
    }

}
