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
        
        $documentoPdf = new PdfModel();
        $documentoPdf->setOption('filename', 'documento.pdf');
        $documentoPdf->setOption('paperOrientation', 'portrait');
        $documentoPdf->setVariables(array(
            'codigo_cupon' => $datos["codigo_cupon"],
            'cantidad' => $datos["cantidad"],
            'id_campana' => $datos["id_campana"],
            'campana_descripcion' => $datos["campana_descripcion"],
            'fecha_compra' => $datos["fecha_compra"],
            'fecha_validez' => $datos["fecha_validez"],
            'sobre_campana' => $datos["sobre_campana"],
            'razon_social' => $datos["razon_social"],
            'descripcion_empresa' => $datos["descripcion_empresa"],
            'ubicacion_gps' => $datos["ubicacion_gps"],
            'web_site' => $datos["web_site"],
            'direccion' => $datos["direccion"],
            'horario' => $datos["horario"],
            'localhost' => $localhost
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
        
        $email = 'ghermain@gmail.com';

        if(!empty($email)) {
            
            $message = new Message();
            $message->addTo($email)
                    ->addFrom('cupones@rebueno.com')
                    ->setSubject('Un cuponazo Rebueno ...‏');

            $transport = new SmtpTransport();
            $options = new SmtpOptions(array(
                'name' => 'smtp.gmail.com',
                'host' => 'smtp.gmail.com',
                'port' => '587',
                'connection_class' => 'login',
                'connection_config' => array(
                    'ssl' => 'tls',
                    'username' => 'ghermain@gmail.com',
                    'password' => 'GENENIOR'
                ),
            ));

            $attachment = new MimePart($pdfCode);
            $attachment->type = 'application/pdf';
            $attachment->filename = 'cuponaso-rebueno.pdf';
            $attachment->disposition = Mime::DISPOSITION_INLINE;
            $attachment->encoding = Mime::ENCODING_BASE64;

            $body = new MimeMessage();
            $body->addPart($attachment);
            
            $message->setBody($body);

            $transport->setOptions($options);
            $transport->send($message);

        }

        return 1;
    }
}
