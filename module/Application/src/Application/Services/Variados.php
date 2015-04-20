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
    
    public function obtenerDocumentoPdf($codigoEmpresa,$tipoDocumento, $nroDocumento, $fechaEmision, $montoTotal)
    {
        $sl = $this->serviceLocator;
        
        $config = $sl->get('config');
        
        $params['is_https'] = $config['is_https'];
        $params['dir_cert'] = $config['rutas']['dir_cert'];
        $params['dir_repo'] = $config['rutas']['dir_repo'];
        $params['codigo_empresa'] = $codigoEmpresa;
        $params['tipo_documento'] = $tipoDocumento;
        $params['nro_documento'] = $nroDocumento; 
        $params['fecha_emision'] = Fechas::obtenerDMYFormat(str_replace('-','/',substr($fechaEmision,0,10)));
        $params['monto_total'] = $montoTotal;
        
        $docCabTable = $sl->get('Application\Model\DocelectronicocabTable');
        //$docCabTable->fetchAll();
        //$docCabTable::Tranportes_CDS
        $documentoPdf = $docCabTable->obtenerDocumentoElectronicoPDF($params);
        $documentoPdf->setTerminal(true);
        $documentoPdf->setTemplate('application/documento-electronico/documento-pdf.phtml');
        $htmlPdf = $sl->get('viewPdfrenderer')->getHtmlRenderer()->render($documentoPdf);
        $engine = $sl->get('viewPdfrenderer')->getEngine();
        // Cargamos el HTML en DOMPDF
        $engine->load_html($htmlPdf);
        $engine->render();
        // Obtenemos el PDF en memoria
        $pdfCode = $engine->output();

        //$this->enviarMail($pdfCode);
        return base64_encode($pdfCode);
    }
}
