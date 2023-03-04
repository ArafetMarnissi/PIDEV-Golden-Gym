<?php

namespace App\Services;

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Color\Color;

class QrcodeService
{
    protected $builder;
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder; 
    }

    public function qrcode($query)
    {
        $url = 'https://www.google.com/search?q=';
        $date_actu = new \DateTime('NOW');
        
        $result = $this->builder
        ->data($url.$query)
        ->size(150)
        ->Margin(10)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->labelText($date_actu->format('Y-m-d'))
        ->labelFont(new NotoSans(7))
        ->logoPath((\dirname(__DIR__,2).'/public/img/logo1.png'))
        ->logoResizeToWidth('120')
        ->logoResizeToHeight('50')
        ->backgroundColor(new Color(250, 250, 250))
        ->build();

        $nameQr = uniqid().'.png';
        $result->saveToFile((\dirname(__DIR__,2).'/public/img/qr-code/'.$nameQr));
        return $result->getDataUri();
    }

}







?>