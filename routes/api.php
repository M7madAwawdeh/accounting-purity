<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/api/account-details/{type}/{id}', function ($type, $id) {
    $model = 'App\\Models\\' . ucfirst($type === 'cash_box' ? 'CashBox' : $type);
    $account = $model::find($id);
    
    if (!$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    return response()->json($account->currencies);
}); 