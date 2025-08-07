<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;

Route::get('/user', function (Request $request) {
    $user = Auth::guard('api')->user();
    if (!$user) {
        abort(401);
    }
    return User::all();
})->middleware('auth:sanctum');

Route::get('/invoice', function (Request $request) {
    $client = Auth::guard('client')->user();
    if (!$client) {
        abort(401);
    }
    return Invoice::where('client_id', $client->id)
        ->latest()
        ->get();
})->middleware('auth:sanctum');

Route::post('/payments', function (Request $request) {
    $client = $request->user(); // MÁS FIABLE

    $validated = $request->validate([
        'invoice_id' => 'required|integer',
        'payment_method' => 'required|in:efectivo,tarjeta,transferencia,cheque',
        'transaction_number' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'observations' => 'nullable|string',
    ]);

    $invoice = Invoice::where('id', $validated['invoice_id'])
        ->where('client_id', $client->id)
        ->first();

    if (!$invoice) {
        return response()->json([
            'message' => 'Factura no encontrada o no pertenece al cliente autenticado.',
        ], 404);
    }

    if ($invoice->payment && $invoice->payment->status === 'pagado') {
        return response()->json([
            'message' => 'La factura ya ha sido pagada.',
        ], 400);
    }

    if ((float) $validated['amount'] !== (float) $invoice->total) {
        return response()->json([
            'message' => 'El monto ingresado no coincide con el total de la factura.',
        ], 422);
    }

    $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'status' => 'pendiente',
        'payment_method' => $validated['payment_method'],
        'transaction_number' => $validated['transaction_number'],
        'amount' => $validated['amount'],
        'observations' => $validated['observations'],
    ]);

    return response()->json([
        'message' => 'Pago registrado correctamente. Será validado por un administrador.',
        'payment' => $payment,
    ], 201);
})->middleware('auth:sanctum');
