<?php
namespace App\service;
use Dompdf\Dompdf;
use Dompdf\Options;


class servicePdf{


    // private $dompdf;
    // public function __construct()
    // {
    //     $this->dompdf = new Dompdf();
    //     $pdfOptions = new Options();
    //     $pdfOptions->set('defaultFont', 'Arial');

    //     $this->dompdf->setPaper('A4', 'portrait');
    
    //     // Instantiate Dompdf with our options
    //     $this-> dompdf->setOptions($pdfOptions);
    // }
    // public function ShowPdfFile($html)
    // {
    //     $this->dompdf->loadHtml($html);
    //     $this->dompdf->render();
    //     $this->dompdf->stream('facture.pdf',[
    //         'Attachement'=>false
    //     ]);
    // }
    // public function generateBinaryPdf($html)
    // {
    //     $this->dompdf->loadHtml($html);
    //     $this->dompdf->render();
    //     $this->dompdf->output();

    // }    
    
} 
?>