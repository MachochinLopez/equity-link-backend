<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    /**
     * Display a list of invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Invoice::orderBy('created_at', 'desc')
            ->simplePaginate(config('app.pagination_per_page'));
    }

    /**
     * Store a newly created invoice from XML file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}

    /**
     * Display the specified invoice.
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
     * @return float
     */
    private function getExchangeRate() {}
}
