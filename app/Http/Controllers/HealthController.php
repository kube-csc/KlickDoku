<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Health Check Controller
 * Liefert JSON-Statusinformationen über die Anwendung.
 */
class HealthController extends Controller
{
    /**
     * Überprüft den Status aller kritischen Komponenten.
     */
    public function check(): JsonResponse
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => [],
        ];

        // Datenbank-Check
        try {
            DB::connection()->getPdo();
            $status['checks']['database'] = ['status' => 'healthy', 'message' => 'Verbunden'];
        } catch (\Exception $e) {
            $status['status'] = 'unhealthy';
            $status['checks']['database'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }

        // Cache-Check
        try {
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            Cache::forget('health_check');
            $status['checks']['cache'] = ['status' => 'healthy', 'message' => $value === 'ok' ? 'Funktionsfähig' : 'Fehler'];
        } catch (\Exception $e) {
            $status['status'] = 'unhealthy';
            $status['checks']['cache'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }

        // Storage-Check
        try {
            $disk = Storage::disk('public');
            $disk->put('health_check.txt', 'ok');
            $disk->delete('health_check.txt');
            $status['checks']['storage'] = ['status' => 'healthy', 'message' => 'Schreibbar'];
        } catch (\Exception $e) {
            $status['status'] = 'degraded';
            $status['checks']['storage'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }

        // Queue-Check
        try {
            $count = DB::table('jobs')->count();
            $status['checks']['queue'] = ['status' => 'healthy', 'message' => "Wartende Jobs: {$count}"];
        } catch (\Exception $e) {
            $status['checks']['queue'] = ['status' => 'unknown', 'message' => 'Nicht verfügbar'];
        }

        // Disk usage
        $diskFree = disk_free_space(storage_path());
        $diskTotal = disk_total_space(storage_path());
        $diskUsedPercent = round((1 - $diskFree / $diskTotal) * 100, 2);
        $status['checks']['disk'] = [
            'status' => $diskUsedPercent > 90 ? 'unhealthy' : 'healthy',
            'message' => "Platz: {$diskUsedPercent}% belegt",
            'free_mb' => round($diskFree / 1024 / 1024, 2),
        ];

        $statusCode = $status['status'] === 'healthy' ? 200 : ($status['status'] === 'degraded' ? 207 : 503);

        return response()->json($status, $statusCode);
    }

    /**
     * Einfacher Ping-Check für Load Balancer.
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
        ]);
    }
}
