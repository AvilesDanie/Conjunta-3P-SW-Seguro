<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        
        $payments = Payment::with('invoice.client')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments', 'status'));
    }

    public function accept(Payment $payment)
    {
        $payment->update([
            'status' => 'pagado',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Pago aceptado correctamente.');
    }

    public function reject(Payment $payment)
    {
        $payment->update([
            'status' => 'rechazado',
        ]);

        return back()->with('success', 'Pago rechazado correctamente.');
    }
}
