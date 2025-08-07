<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Número
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vendedor
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Estado
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Fecha
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($invoices as $invoice)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $invoice->invoice_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $invoice->client?->name ?? 'Cliente no disponible' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $invoice->user?->name ?? 'Usuario no disponible' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    S/ {{ number_format($invoice->total, 2) }}
                </td>
                <td>
    @if($invoice->payment && $invoice->payment->status === 'pagado')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <i class="fas fa-check-circle mr-1"></i> Pagada
        </span>
    @elseif($invoice->payment && $invoice->payment->status === 'rechazado')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <i class="fas fa-times-circle mr-1"></i> Rechazada
        </span>
    @elseif($invoice->payment && $invoice->payment->status === 'pendiente')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <i class="fas fa-clock mr-1"></i> Pendiente
        </span>
    @elseif($invoice->status === 'cancelled')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
            <i class="fas fa-ban mr-1"></i> Cancelada
        </span>
    @else
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
            <i class="fas fa-question-circle mr-1"></i> Sin pago
        </span>
    @endif
</td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $invoice->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($invoice->isActive() && $invoice->canBeCancelledBy(auth()->user()))
                        <a href="{{ route('invoices.cancel', $invoice) }}" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-times-circle"></i>
                        </a>
                        @endif

                        <a href="{{ route('invoices.pdf', $invoice) }}" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-file-pdf"></i>
                        </a>

                        <form method="POST" action="{{ route('invoices.send-email', $invoice) }}" class="inline email-form">
                            @csrf
                            <button type="submit" class="text-purple-600 hover:text-purple-900 bg-transparent border-none cursor-pointer">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    No se encontraron facturas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
