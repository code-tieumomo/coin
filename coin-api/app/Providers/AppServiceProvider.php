<?php

namespace App\Providers;

use DB;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('custom-script', 'https://cdn.jsdelivr.net/npm/iconify-icon@2.1.0/dist/iconify-icon.min.js'),
        ]);

        if (config('logging.log_sql')) {
            DB::listen(function (QueryExecuted $query) {
                $sqlWithPlaceholders = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
                $bindings = $query->connection->prepareBindings($query->bindings);
                $pdo = $query->connection->getPdo();

                $realSql = $sqlWithPlaceholders;
                if (count($bindings) > 0) {
                    $realSql = vsprintf($sqlWithPlaceholders, array_map(function ($binding) use ($pdo) {
                        if ($binding === null) {
                            return 'null';
                        }
                        if (is_string($binding)) {
                            return $pdo->quote($binding);
                        }
                        if ($binding instanceof \DateTime) {
                            return $pdo->quote($binding->format('Y-m-d H:i:s'));
                        }
                        return $binding;
                    }, $bindings));
                }

                $logMessage = [
                    'query' => $sqlWithPlaceholders,
                    'time' => "{$query->time}ms",
                    'connection' => $query->connection->getName(),
                    'database' => $query->connection->getDatabaseName(),
                    'timestamp' => now()->toDateTimeString(),
                ];

                Log::channel('sql')->info('Database Query', $logMessage);
            });
        }
    }
}
