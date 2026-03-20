<?php

namespace App\Http\Controllers;

use App\Services\PurchaseInvoiceImportService;
use App\Models\PurchaseInvoice;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchaseInvoiceController extends Controller
{
    protected $importService;

    public function __construct(PurchaseInvoiceImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display a listing of purchase invoices
     */
    public function index(Request $request)
    {
        $companyId = $request->get('company_id');
        $invoices = PurchaseInvoice::byCompany($companyId)
            ->orderBy('registration_date', 'desc')
            ->paginate(50);

        return response()->json($invoices);
    }

    /**
     * Import purchase invoices from CSV/Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'company_id' => 'required|string|exists:companies,id'
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->storeAs('imports', 'purchase_invoices_' . time() . '.' . $file->getClientOriginalExtension());
            
            $fullPath = storage_path('app/' . $filePath);
            
            $results = $this->importService->import($fullPath, $request->company_id);

            Log::info('Purchase invoices import completed', [
                'company_id' => $request->company_id,
                'file' => $filePath,
                'results' => $results
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Purchase invoices import failed', [
                'company_id' => $request->company_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test import from the public folder file
     */
    public function testImport(Request $request)
    {
        $request->validate([
            'company_id' => 'required|string|exists:companies,id'
        ]);

        try {
            $filePath = public_path('Fatture acquisto reg.csv');
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test file not found: ' . $filePath
                ], 404);
            }

            $results = $this->importService->import($filePath, $request->company_id);

            Log::info('Purchase invoices test import completed', [
                'company_id' => $request->company_id,
                'results' => $results
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test import completed successfully',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Purchase invoices test import failed', [
                'company_id' => $request->company_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Test import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific purchase invoice
     */
    public function show(PurchaseInvoice $purchaseInvoice)
    {
        return response()->json($purchaseInvoice);
    }

    /**
     * Get import statistics
     */
    public function statistics(Request $request)
    {
        $companyId = $request->get('company_id');
        
        $stats = [
            'total' => PurchaseInvoice::byCompany($companyId)->count(),
            'open' => PurchaseInvoice::byCompany($companyId)->open()->count(),
            'paid' => PurchaseInvoice::byCompany($companyId)->paid()->count(),
            'total_amount' => PurchaseInvoice::byCompany($companyId)->sum('amount'),
            'total_amount_including_vat' => PurchaseInvoice::byCompany($companyId)->sum('amount_including_vat'),
        ];

        return response()->json($stats);
    }
}
