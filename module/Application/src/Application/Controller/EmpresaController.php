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

        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        //$clienteTable = $serviceLocator->get('Dashboard\Model\CupclienteTable');
        //$datos = $clienteTable->getUsuarioByUser($email);

        
        
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
        
        return new ViewModel(array('nombre_documento' => $id_contrato) );
    }
    
}
