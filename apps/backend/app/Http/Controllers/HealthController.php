<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * Health check endpoint that returns 200 without requiring a database connection.
     */
    public function check(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
        ], 200);
    }
}
