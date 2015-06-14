<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Services;

use DOMPDFModule\View\Model\PdfModel;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Zend\Mail\Transport\SmtpOptions;
use QRCode\Service\QRCode;
use Zend\Barcode\Barcode;
/**
 * Description of Variados
 *
 * @author Administrador
 */
class Variados {
    //put your code here
    public $serviceLocator;

    function __construct($seviceLocator)
    {
        $this->serviceLocator = $seviceLocator;
    }
    
    public function obtenerCuponPdf($datos)
    {
        $sl = $this->serviceLocator;
        
        $config = $sl->get('Config');
        $localhost = $config['constantes']['localhost'];
        
        $is_https = $config['is_https'];
        
        $activo   = $config['correo']['activo'];
        $name     = $config['correo']['name'];
        $host     = $config['correo']['host'];
        $port     = $config['correo']['port'];
        $username = $config['correo']['username'];
        $password = $config['correo']['password'];
        $cuenta   = $config['correo']['cuenta-envio-cupones'];

        $body = new MimeMessage();

        for($i=0;$i<count($datos);$i++) {
        //for($i=0;$i<1;$i++) {
            if($i==0) {
                
                /*********Codigo de Barra Code128****************/
                $barcodeOptions = array('text' => $datos[$i]["codigo_cupon"]);
                $rendererOptions = array('imageType' => 'jpg');
                $img128 = Barcode::factory('code128', 'image', $barcodeOptions, $rendererOptions)->draw();
                ob_start();
                imagejpeg($img128);
                $data128 = ob_get_clean();
                
                /*********Codigo QR ****************************/
                $qr = new QRCode();
                if ($is_https) {
                    $qr->isHttps();
                } else {
                    $qr->isHttp();
                }
                $dataSerializadaQR = $datos[$i]["codigo_cupon"];
                $qr->setData($dataSerializadaQR);
                $qr->setDimensions(90, 90);
                $qrImg = $qr->getResult();
                
                $documentoPdf = new PdfModel();
                $documentoPdf->setOption('filename', 'cupon-'.$datos[$i]["codigo_cupon"].'.pdf');
                $documentoPdf->setOption('paperOrientation', 'portrait');
                $documentoPdf->setVariables(array(
                    'codigo_cupon' => $datos[$i]["codigo_cupon"],
                    'cantidad' => $datos[$i]["cantidad"],
                    'id_campana' => $datos[$i]["id_campana"],
                    'campana_descripcion' => $datos[$i]["campana_descripcion"],
                    'fecha_compra' => $datos[$i]["fecha_compra"],
                    'fecha_validez' => $datos[$i]["fecha_validez"],
                    'sobre_campana' => $datos[$i]["sobre_campana"],
                    'razon_social' => $datos[$i]["razon_social"],
                    'descripcion_empresa' => $datos[$i]["descripcion_empresa"],
                    'ubicacion_gps' => $datos[$i]["ubicacion_gps"],
                    'web_site' => $datos[$i]["web_site"],
                    'direccion' => $datos[$i]["direccion"],
                    'horario' => $datos[$i]["horario"],
                    'localhost' => $localhost,
                    'barras_img' => 'data:jpeg;base64,' . base64_encode($data128),
                    'qr_img' => $qrImg
                ));

                $documentoPdf->setTerminal(true);
                $documentoPdf->setTemplate('application/campana/cuponbuenaso-pdf.phtml');
                $htmlPdf = $sl->get('viewPdfrenderer')->getHtmlRenderer()->render($documentoPdf);
                $engine = $sl->get('viewPdfrenderer')->getEngine();
                // Cargamos el HTML en DOMPDF
                $engine->load_html($htmlPdf);
                $engine->render();
                // Obtenemos el PDF en memoria
                $pdfCode = $engine->output();
                //unset($documentoPdf);
                //$documentoPdf->clearOptions();
            }
            
            $attachment = new MimePart($pdfCode);
            $attachment->type = 'application/pdf';
            $attachment->filename = 'cupon-'.$datos[$i]["codigo_cupon"].'.pdf';
            /*if($i==0) {
                $attachment->disposition = Mime::DISPOSITION_INLINE;
            } else {*/
                $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
            //}
            $attachment->encoding = Mime::ENCODING_BASE64;
            
            $body->addPart($attachment);

        }
        
        $email = 'ghermain@gmail.com';

        if($activo == '1') {
            
            $message = new Message();
            $message->addTo($email)
                    ->addFrom($cuenta)
                    ->setSubject('Un cuponazo Rebueno ...‏');

            $transport = new SmtpTransport();
            $options   = new SmtpOptions(array(
                'name' => $name,
                'host' => $host,
                'port' => $port,
                'connection_class' => 'login',
                'connection_config' => array(
                    //'ssl' => 'tls',
                    'username' => $username,
                    'password' => $password
                ),
            ));

            $message->setBody($body);

            $transport->setOptions($options);
            $transport->send($message);

        }

        return 1;
    }
}
