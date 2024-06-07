<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Deliveries;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeliveriesController extends Controller
{
    public function getAll(Request $request)
    {
        $query = Deliveries::query();

        if ($request->has('from_delivery_date') and $request->input('from_delivery_date')) {
            $query->whereDate('estimated_delivery_date', '>=', $request->input('from_delivery_date'));
        }

        if ($request->has('to_delivery_date') and $request->input('to_delivery_date')) {
            $query->whereDate('estimated_delivery_date', '<=', $request->input('to_delivery_date'));
        }

        if ($request->has('from_dispatch_date') and $request->input('from_dispatch_date')) {
            $query->whereDate('dispatch_date', '>=', $request->input('from_dispatch_date'));
        }

        if ($request->has('to_dispatch_date') and $request->input('to_dispatch_date')) {
            $query->whereDate('dispatch_date', '<=', $request->input('to_dispatch_date'));
        }

        if ($request->has('order_id') and $request->input('order_id')) {
            $query->where('order_id', $request->input('order_id'));
        }

        if ($request->has('customer_id') and $request->input('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->has('city_state_country') and $request->input('city_state_country')) {
            $cityStateCountry = $request->input('city_state_country');
            $query->where(function($q) use ($cityStateCountry) {
                $q->where('city', 'like', "%$cityStateCountry%")
                  ->orWhere('state', 'like', "%$cityStateCountry%")
                  ->orWhere('country', 'like', "%$cityStateCountry%");
            });
        }

        if ($request->has('status') and $request->input('status') != 'All status') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('min_cost') and $request->input('min_cost')) {
            $minCost = floatval(str_replace(['R$', '.', ','], ['', '', '.'], $request->input('min_cost')));
            $query->where('cost', '>=', $minCost);
        }

        if ($request->has('max_cost') and $request->input('max_cost')) {
            $maxCost = floatval(str_replace(['R$', '.', ','], ['', '', '.'], $request->input('max_cost')));
            $query->where('cost', '<=', $maxCost);
        }

        return response()->json($query->paginate());
    }

    public function getStatistics()
    {
        $totalDeliveries = Deliveries::count();
        $completedDeliveries = Deliveries::where(
            'status', 'Delivered'
        )->count();

        $sevenDaysAgo = Carbon::now()->subDays(7);
        $last7DaysDeliveries = Deliveries::where(
            'dispatch_date', '>=', $sevenDaysAgo
        )->count();

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayNewCustomers = Deliveries::whereDate('dispatch_date', $today)
            ->distinct('customer_id')
            ->count('customer_id');
        $yesterdayNewCustomers = Deliveries::whereDate('dispatch_date', $yesterday)
            ->distinct('customer_id')
            ->count('customer_id');

        $newCustomerIncreaseFromYesterday = $yesterdayNewCustomers > 0 
            ? (($todayNewCustomers - $yesterdayNewCustomers) / $yesterdayNewCustomers) * 100
            : $todayNewCustomers * 100;

        $totalCities = Deliveries::distinct('city')->count('city');
        $totalCountries = Deliveries::distinct('country')->count('country');

        $previousWeekDeliveries = Deliveries::whereBetween(
            'dispatch_date', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)]
        )->count();
        $increaseDeliveriesLastWeek = $previousWeekDeliveries > 0 
            ? (($last7DaysDeliveries - $previousWeekDeliveries) / $previousWeekDeliveries) * 100
            : $last7DaysDeliveries * 100;

        return response()->json([
            'total_deliveries' => $totalDeliveries,
            'completed_deliveries' => $completedDeliveries,
            'last_7_days_deliveries' => $last7DaysDeliveries,
            'increase_deliveries_last_week' => number_format($increaseDeliveriesLastWeek, 2),
            'today_new_customers' => $todayNewCustomers,
            'new_customer_increase_from_yesterday' => number_format($newCustomerIncreaseFromYesterday, 2),
            'total_cities' => $totalCities,
            'total_countries' => $totalCountries,
        ]);
    }

    public function getChartOrdersLastWeek()
    {
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek(Carbon::SUNDAY);
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek(Carbon::SATURDAY);
    
        $deliveries = Deliveries::whereBetween('dispatch_date', [$startOfLastWeek, $endOfLastWeek])
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->dispatch_date)->format('w'); // Agrupar por dia da semana (0-6, onde 0 é domingo)
            });
    
        $deliveriesByDay = [];
        foreach ($deliveries as $day => $group) {
            $deliveriesByDay[$day] = $group->count();
        }
    
        $weeklyDeliveries = array_fill(0, 7, 0);
        foreach ($deliveriesByDay as $day => $total) {
            $weeklyDeliveries[$day] = $total;
        }
    
        return response()->json($weeklyDeliveries);
    }

    public function getChartCustomersByMonth()
    {
        $now = Carbon::now();
        $startOfFiveMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
    
        $deliveries = Deliveries::where('dispatch_date', '>=', $startOfFiveMonthsAgo)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->dispatch_date)->format('Y-m'); // Agrupar por ano e mês
            });
    
        $customersByMonth = [];
        foreach ($deliveries as $key => $value) {
            $yearMonth = explode('-', $key);
            $monthName = Carbon::createFromDate($yearMonth[0], $yearMonth[1], 1)->format('M');
            $customersByMonth[$monthName] = $value->unique('customer_id')->count();
        }
    
        // Inicializar arrays para rótulos e valores
        $labels = [];
        $values = [];
    
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i)->format('M');
            $labels[] = $month;
            $values[] = $customersByMonth[$month] ?? 0;
        }
    
        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function getChartAverageDeliveryDaysByMonth()
    {
        $now = Carbon::now();
        $startOfFiveMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
    
        $deliveries = Deliveries::where('dispatch_date', '>=', $startOfFiveMonthsAgo)
            ->where('status', 'Delivered')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->dispatch_date)->format('Y-m'); // Agrupar por ano e mês
            });
        // return $deliveries;

        $averageDeliveryTimeByMonth = [];
        foreach ($deliveries as $key => $value) {
            $yearMonth = explode('-', $key);
            $monthName = Carbon::createFromDate($yearMonth[0], $yearMonth[1], 1)->format('M');
            $totalDays = $value->sum(function($delivery) {
                return Carbon::parse($delivery->estimated_delivery_date)->diffInDays(Carbon::parse($delivery->dispatch_date));
            });
            $averageDays = $totalDays / $value->count();
            $averageDeliveryTimeByMonth[$monthName] = $averageDays;
        }
    
        // Inicializar arrays para rótulos e valores
        $labels = [];
        $values = [];
    
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i)->format('M');
            $labels[] = $month;
            $values[] = abs(round($averageDeliveryTimeByMonth[$month] ?? 0));
        }
    
        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }    

    public function getChartRevenueByMonth()
    {
        $now = Carbon::now();
        $startOfFiveMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
    
        $deliveries = Deliveries::where('dispatch_date', '>=', $startOfFiveMonthsAgo)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->dispatch_date)->format('Y-m'); // Agrupar por ano e mês
            });
    
        $revenueByMonth = [];
        foreach ($deliveries as $key => $value) {
            $yearMonth = explode('-', $key);
            $monthName = Carbon::createFromDate($yearMonth[0], $yearMonth[1], 1)->format('M');
            $totalRevenue = $value->sum('cost');
            $revenueByMonth[$monthName] = $totalRevenue;
        }
    
        // Inicializar arrays para rótulos e valores
        $labels = [];
        $values = [];
    
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i)->format('M');
            $labels[] = $month;
            $values[] = $revenueByMonth[$month] ?? 0;
        }
    
        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }
    

    public function getChartTop5Cities()
    {
        $topCities = Deliveries::selectRaw('city, COUNT(*) as total')
            ->groupBy('city')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->pluck('total', 'city')
            ->toArray();

        $labels = array_keys($topCities);
        $values = array_values($topCities);

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function getChartCostAverage()
    {
        $now = Carbon::now();
        $startOfFiveMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
    
        $deliveries = Deliveries::where('dispatch_date', '>=', $startOfFiveMonthsAgo)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->dispatch_date)->format('Y-m'); // Agrupar por ano e mês
            });
    
        $averageCostByMonth = [];
        foreach ($deliveries as $key => $value) {
            $yearMonth = explode('-', $key);
            $monthName = Carbon::createFromDate($yearMonth[0], $yearMonth[1], 1)->format('M');
            $totalCost = $value->sum('cost');
            $averageCost = $value->count() > 0 ? $totalCost / $value->count() : 0;
            $averageCostByMonth[$monthName] = round($averageCost, 2);
        }
    
        // Inicializar arrays para rótulos e valores
        $labels = [];
        $values = [];
    
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i)->format('M');
            $labels[] = $month;
            $values[] = $averageCostByMonth[$month] ?? 0;
        }
    
        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }
    
    
}
