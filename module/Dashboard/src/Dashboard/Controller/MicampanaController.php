<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Controller;
/**
 * Description of MicampanaController
 *
 * @author Administrador
 */
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcDatagrid\Column;
use Zend\Session\Container;
use Zend\Json\Json;
    
class MicampanaController extends AbstractActionController {
    //put your code here
    public function indexAction() {
        $id_empresa = 5;
        
        $serviceLocator = $this->getServiceLocator();
        $empresaTable = $serviceLocator->get('Dashboard\Model\GenempresaTable');
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
        $liquidacionTable = $serviceLocator->get('Dashboard\Model\CupliquidacionTable');
        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $datosEmpresa = $empresaTable->getEmpresa($id_empresa);
        $datosCuponV = $cuponTable->getCuponValidado($id_empresa,3);
        $datosHistorial = $cuponTable->getHistorialEmpresa($id_empresa);
        $datosLiquidacion = $liquidacionTable->getLiquidaciones($id_empresa,3);
        $datosCampana = $campanaTable->getCampanaActiva($id_empresa);
        
        $nombre_empresa = $datosEmpresa[0]['razon_social'];
        
        //$this->layout('layout/micampana');
        
        return new ViewModel(array('id_empresa' => $id_empresa,
                                   'nombre_empresa' => $nombre_empresa,
                                   'cupon_validado' => $datosCuponV,
                                   'liquidaciones' => $datosLiquidacion,
                                   'historial' => $datosHistorial,
                                   'datosCampana' => $datosCampana
                                  ));
    }

    public function cuponAction() {
        $id_empresa = $this->params()->fromPost("empresa", null);
        $cupon = $this->params()->fromPost("cupon", null);
        $tipo = $this->params()->fromPost("tipo", null);

        $serviceLocator = $this->getServiceLocator();
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');

        $datos = $cuponTable->validarCupon($id_empresa, $cupon, $tipo);

        $viewmodel = new ViewModel(array('datos' => $datos));
        $viewmodel->setTerminal(true);
        return $viewmodel;
    }
    
    public function detallevalidadoAction() {
        
        $id_empresa = base64_decode($this->params()->fromRoute("empresa", null));
        
        $serviceLocator = $this->getServiceLocator();
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
        $datosCuponV = $cuponTable->getCuponValidado($id_empresa,0);
        
        return new ViewModel(array('id_empresa' => $id_empresa,
                                   'cupon_validado' => $datosCuponV));
    }
    
    public function detalleliquidacionAction() {
        
        $id_empresa = base64_decode($this->params()->fromRoute("empresa", null));
        
        $serviceLocator = $this->getServiceLocator();
        $liquidacionTable = $serviceLocator->get('Dashboard\Model\CupliquidacionTable');
        $datosLiquidacion = $liquidacionTable->getLiquidaciones($id_empresa,0);
        
        return new ViewModel(array('id_empresa' => $id_empresa,
                                   'liquidaciones' => $datosLiquidacion));
    }
    
    public function historiacampanasAction() {
        
        $id_empresa = base64_decode($this->params()->fromRoute("empresa", null));
        
        $serviceLocator = $this->getServiceLocator();
        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $datosCampana = $campanaTable->getCampanaActiva($id_empresa);
        
        return new ViewModel(array('id_empresa' => $id_empresa,
                                   'campanas' => $datosCampana));
    }
}
