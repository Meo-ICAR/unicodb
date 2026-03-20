<div class="space-y-6">
    <!-- Summary Section -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold mb-3">Summary Overview</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600">Total Compenso</p>
                <p class="text-lg font-bold">{{ number_format($summary->total_compenso, 2, ',', '.') }} €</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Provvigione</p>
                <p class="text-lg font-bold">{{ number_format($summary->total_provvigione, 2, ',', '.') }} €</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Storno</p>
                <p class="text-lg font-bold">{{ number_format($summary->total_storno, 2, ',', '.') }} €</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Record Count</p>
                <p class="text-lg font-bold">{{ $summary->record_count }}</p>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div>
        <h3 class="text-lg font-semibold mb-3">Detailed Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compenso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lavorazione</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Premio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rimborso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assicurazione</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storno</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provvigione</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($details as $detail)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->compenso, 2, ',', '.') }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->compenso_lavorazione, 2, ',', '.') }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->compenso_premio, 2, ',', '.') }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->compenso_rimborso, 2, ',', '.') }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->compenso_assicurazione, 2, ',', '.') }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->compenso_cliente, 2, ',', '.') }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->storno, 2, ',', '.') }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail->provvigione, 2, ',', '.') }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Provvigione Breakdown -->
    <div>
        <h3 class="text-lg font-semibold mb-3">Provvigione Breakdown</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-3 rounded">
                <p class="text-sm text-blue-600">Lavorazione</p>
                <p class="font-semibold">{{ number_format($summary->total_provvigione_lavorazione, 2, ',', '.') }} €</p>
            </div>
            <div class="bg-green-50 p-3 rounded">
                <p class="text-sm text-green-600">Premio</p>
                <p class="font-semibold">{{ number_format($summary->total_provvigione_premio, 2, ',', '.') }} €</p>
            </div>
            <div class="bg-yellow-50 p-3 rounded">
                <p class="text-sm text-yellow-600">Rimborso</p>
                <p class="font-semibold">{{ number_format($summary->total_provvigione_rimborso, 2, ',', '.') }} €</p>
            </div>
            <div class="bg-purple-50 p-3 rounded">
                <p class="text-sm text-purple-600">Assicurazione</p>
                <p class="font-semibold">{{ number_format($summary->total_provvigione_assicurazione, 2, ',', '.') }} €</p>
            </div>
            <div class="bg-red-50 p-3 rounded">
                <p class="text-sm text-red-600">Storno</p>
                <p class="font-semibold">{{ number_format($summary->total_provvigione_storno, 2, ',', '.') }} €</p>
            </div>
        </div>
    </div>
</div>
