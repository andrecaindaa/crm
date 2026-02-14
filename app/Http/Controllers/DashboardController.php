<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $totalDeals = Deal::count();

        $inactiveDeals = Deal::get()
            ->filter(fn($deal) => $deal->isInactive())
            ->count();

        $expectedRevenue = Deal::whereNotIn('stage', ['won', 'lost'])
            ->sum('value');

        $closedRevenue = Deal::where('stage', 'won')
            ->sum('value');

        // Pipeline por estágio
        $pipeline = Deal::select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->pluck('total', 'stage');

        // Negócios inativos detalhados
        $inactiveList = Deal::with('owner')
            ->get()
            ->filter(fn($deal) => $deal->isInactive())
            ->sortByDesc('value')
            ->take(10)
            ->values();

            $highRiskDeals = Deal::get()
            ->sortByDesc('risk_score')
            ->take(5)
            ->values();


        // Top produtos
        $topProducts = Product::select(
                'products.id',
                'products.name',
                DB::raw('SUM(deal_products.total) as revenue')
            )
            ->join('deal_products', 'products.id', '=', 'deal_products.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return inertia('Dashboard/Index', [
            'kpis' => [
                'total_deals' => $totalDeals,
                'inactive_deals' => $inactiveDeals,
                'expected_revenue' => $expectedRevenue,
                'closed_revenue' => $closedRevenue,


            ],
            'pipeline' => $pipeline,
            'inactiveList' => $inactiveList,
            'topProducts' => $topProducts,
            'highRiskDeals' => $highRiskDeals,
        ]);
    }
}
