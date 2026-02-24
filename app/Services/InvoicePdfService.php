<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class InvoicePdfService
{
    public function download(Invoice $invoice): Response
    {
        $invoice->loadMissing(["items", "registration.event"]);

        $fileName = str_replace(["/", "\\"], "-", $invoice->invoice_number) . ".pdf";

        return Pdf::loadView("invoices.pdf", [
            "invoice" => $invoice,
        ])
            ->setPaper("a4")
            ->download($fileName);
    }
}
