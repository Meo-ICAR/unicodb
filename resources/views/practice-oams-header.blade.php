<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Practices -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                        Totale Pratiche OAM
                    </p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        {{ $totalPractices }}
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-800 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Perfected Last Year Amount -->
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">
                        Importo Perfezionate Anno Precedente
                    </p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                        € {{ $perfectedLastYearAmount }}
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-800 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Working Last Year Amount -->
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-amber-600 dark:text-amber-400">
                        Importo Lavorazione Anno Precedente
                    </p>
                    <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">
                        € {{ $workingLastYearAmount }}
                    </p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-800 rounded-full p-3">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Statistiche pratiche OAM con condizioni temporali
            </p>
            <div class="flex space-x-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Totale Importi: €{{ number_format(floatval(str_replace('.', '', str_replace(',', '', $perfectedLastYearAmount))) + floatval(str_replace('.', '', str_replace(',', '', $workingLastYearAmount))), 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>
