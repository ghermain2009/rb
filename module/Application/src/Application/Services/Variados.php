<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Services;

use DOMPDFModule\View\Model\PdfModel;
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
            'horario' => $datos["horario"]
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

        return $pdfCode;
    }
}
