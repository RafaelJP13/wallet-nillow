<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $wallet = $request
            ->user()
            ->wallet()
            ->first();

        return response()->json([
            'data' => $wallet,
        ]);
    }
}