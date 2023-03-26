<?php

namespace App\Services;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;


class mailService
{    public function sendFacture($emailUser,$pdf,$content){


   
       $transport = Transport::fromDsn('smtp://ggym45@outlook.com:Arafet26845815@smtp.office365.com:587');
       $mailer = new Mailer($transport); 
       $email = (new Email());
       $email->from('ggym45@outlook.com');
       $email->to($emailUser);
       $email->subject('Command Confirmed');

       
       $pdfContent = $pdf->Output('facture.pdf', 'S');
       $email->attach($pdfContent,"facture.pdf","Application/pdf");
       
       ///

       $email->html($content);
       $mailer->send($email);


     

   return true;

}
}