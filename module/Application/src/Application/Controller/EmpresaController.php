<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;

/**
 * Description of InteresController
 *
 * @author gtapia
 */
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EmpresaController extends AbstractActionController {

    public function firmaContratoAction() {

        $token = $this->params()->fromQuery("token", null);

        $id_contrato = base64_decode($token);
        
        error_log($id_contrato);

        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        
        $contratoTable = $serviceLocator->get('Dashboard\Model\ConcontratoTable');
        $contrato = $contratoTable->getContratoId($id_contrato);
        
        $nombre_documento = '';
        
        foreach( $contrato as $cont ) {
            $nombre_documento = $cont["nombre_documento"];
        }
                
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $telefono_empresa = $config['empresa']['telefono'];
        $this->layout()->telefono_empresa = $telefono_empresa;
        
        return new ViewModel(array('nombre_documento' => $nombre_documento) );
    }
    
    public function verContratoPdfAction() 
    {
        $serviceLocator = $this->getServiceLocator();
        $response = $this->getResponse();
        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path  = $config['constantes']['sep_path'];
        
        $directorio = $dir_image.$sep_path."..".$sep_path."..".$sep_path."data".$sep_path."contratos".$sep_path;
        
        $params = $this->params()->fromQuery();
        
        $nombreDocumento = !empty($params['nombre_documento']) ? $params['nombre_documento'] : '';
        
        $rutaDocumento = $directorio. 
                         $nombreDocumento.'.pdf';
        
        $contenidoDocumento = file_get_contents($rutaDocumento);
        $response->setContent($contenidoDocumento);

        $headers = $response->getHeaders();
        $headers->clearHeaders()
                ->addHeaderLine('Content-Type', 'application/pdf')
                ->addHeaderLine('Content-Disposition', 'inline; filename="'.$nombreDocumento.'.pdf"')
                ->addHeaderLine('Content-Length', strlen($contenidoDocumento));


        return $this->response;
    }
}
