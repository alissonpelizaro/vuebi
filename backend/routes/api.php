<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveriesController;

Route::prefix('/v1')->group(function () {
    Route::get('/deliveries', [DeliveriesController::class, 'getAll']);

    Route::prefix('/dashboard')->group(function () {
        Route::get('/statistics', [DeliveriesController::class, 'getStatistics']);

        Route::prefix('/charts')->group(function () {
            Route::get('/orders-last-week', [DeliveriesController::class, 'getChartOrdersLastWeek']);
            Route::get('/customers-by-month', [DeliveriesController::class, 'getChartCustomersByMonth']);
            Route::get('/average-delivery-days-by-month', [DeliveriesController::class, 'getChartAverageDeliveryDaysByMonth']);
            Route::get('/revenue-by-month', [DeliveriesController::class, 'getChartRevenueByMonth']);
            Route::get('/top-5-cities', [DeliveriesController::class, 'getChartTop5Cities']);
            Route::get('/cost-average', [DeliveriesController::class, 'getChartCostAverage']);

        });
    });
});

