<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check()
    {
        try {
            DB::connection()->getPdo();

            return response()->json([
                'system_name' => 'Samudera Fresh Strawberry',
                'sprint_stage' => 'Sprint 1 - Foundation Ready',
                'database_status' => 'CONNECTED',
                'message' => 'Database terhubung dengan baik',
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'system_name' => 'Samudera Fresh Strawberry',
                'sprint_stage' => 'Sprint 1 - Foundation Ready',
                'database_status' => 'DISCONNECTED',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}