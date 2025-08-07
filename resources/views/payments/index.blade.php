<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="fas fa-money-check-alt mr-2"></i> Gestión de Pagos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Filtros --}}
            <div class="mb-4">
                <form method="GET" class="flex items-center space-x-4">
                    <label class="font-medium text-sm text-gray-700">Estado:</label>
                    <select name="status" class="border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ request('status') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="rechazado" {{ request('status') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </form>
            </div>

            {{-- Alertas --}}
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
            @endif

            {{-- Tabla de Pagos --}}
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Factura</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Método</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Monto</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $payment->invoice->client->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800 capitalize">{{ $payment->payment_method }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $payment->amount ? 'S/ ' . number_format($payment->amount, 2) : '---' }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @switch($payment->status)
                                @case('pagado')
                                <span class="text-green-700 font-semibold">Pagado</span>
                                @break
                                @case('rechazado')
                                <span class="text-red-700 font-semibold">Rechazado</span>
                                @break
                                @default
                                <span class="text-yellow-700 font-semibold">Pendiente</span>
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($payment->status === 'pendiente' && $payment->amount)
                                <div class="flex space-x-2">
                                    <form action="{{ route('payments.accept', $payment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-check-circle mr-1"></i>Aceptar
                                        </button>
                                    </form>

                                    <form action="{{ route('payments.reject', $payment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-times-circle mr-1"></i>Rechazar
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-gray-500 italic">Sin acciones</span>
                                @endif

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No hay pagos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $payments->withQueryString()->links() }}
            </div>

        </div>
    </div>
</x-app-layout>