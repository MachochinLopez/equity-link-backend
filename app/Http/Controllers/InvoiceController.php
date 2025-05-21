<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceXmlParser;
use App\Services\DofExchangeRateService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private DofExchangeRateService $dofService;

    public function __construct()
    {
        $this->dofService = new DofExchangeRateService(config('services.dof.token'));
    }

    /**
     * Display a list of invoices with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Invoice::orderBy('created_at', 'desc')
            ->simplePaginate(config('app.pagination_per_page'));
    }

    /**
     * Create a new invoice from XML file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml'
        ]);

        $xmlContent = file_get_contents($request->file('xml_file')->getRealPath());

        try {
            $parser = new InvoiceXmlParser($xmlContent);
            $invoiceData = $parser->toArray();

            $existingInvoice = Invoice::where('uuid', $invoiceData['uuid'])->first();
            if ($existingInvoice) {
                return response()->json([
                    'message' => 'Esta factura ya ha existe'
                ], 400);
            }

            try {
                $exchangeRate = $this->getExchangeRate();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Hubo un problema al obtener el tipo de cambio',
                    'error' => $e->getMessage()
                ], 500);
            }

            $invoice = Invoice::create([
                'uuid' => $invoiceData['uuid'],
                'folio' => $invoiceData['folio'],
                'issuer' => $invoiceData['issuer'],
                'receiver' => $invoiceData['receiver'],
                'currency' => $invoiceData['currency'],
                'total' => $invoiceData['total'],
                'exchange_rate' => $exchangeRate,
            ]);

            return response()->json([
                'message' => 'Factura creada correctamente',
                'data' => $invoice
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Archivo XML inválido: ' . $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return invoice's data.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        return $invoice;
    }

    /**
     * Get exchange rate from DOF API.
     *
     * @return float|null
     */
    private function getExchangeRate()
    {
        return $this->dofService->getExchangeRate();
    }
}
