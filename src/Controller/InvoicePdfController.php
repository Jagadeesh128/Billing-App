<?php

namespace App\Controller;

use App\Entity\Invoice;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoicePdfController extends AbstractController
{
    #[Route('/invoice/{id}/pdf', name: 'invoice_pdf')]
    public function pdf(Invoice $invoice): Response
    {
        $html = $this->renderView('admin/invoice/pdf.html.twig', [
            'invoice' => $invoice
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="invoice-'.$invoice->getInvoiceNumber().'.pdf"'
            ]
        );
    }

    #[Route('/invoice/{id}/print', name: 'invoice_print')]
    public function print(Invoice $invoice): Response
    {
        return $this->render('admin/invoice/view.html.twig', [
            'invoice' => $invoice
        ]);
    }

}
