<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
class SendmailController extends AbstractController
{
    #[Route('/sendmail', name: 'app_sendmail')]
    public function index(): Response
    {
        $user= $this->getUser();

        $transport = Transport::fromDsn('smtp://khalilherma6@outlook.fr:KhAlIl332810@smtp.office365.com:587');
        $mailer = new Mailer($transport); 
        $email = (new Email());
        $email->from('khalilherma6@outlook.fr');
        $email->to('sajec54551@pubpng.com');
        $email->subject('Demo message using the Symfony Mailer library.');
        $email->text('This is the plain text body of the message.\nThanks,\nAdmin');
        $email->html('This is the HTML version of the message.<br>Example of inline image:<br><img src="cid:nature" width="200" height="200"><br>Thanks,<br>Admin');
        $mailer->send($email);
        return $this->render('sendmail/index.html.twig', [
            'controller_name' => 'SendmailController',
        ]);
    }



}
