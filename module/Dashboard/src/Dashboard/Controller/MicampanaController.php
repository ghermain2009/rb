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
        $request = $this->getRequest();
        
        //$this->layout('layout/micampana');
        
        return new ViewModel();
    }

    public function cuponAction() {
        $cupon = $this->params()->fromPost("cupon", null);
        $tipo = $this->params()->fromPost("tipo", null);

        $serviceLocator = $this->getServiceLocator();
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');

        $datos = $cuponTable->validarCupon($cupon, $tipo);

        $viewmodel = new ViewModel(array('datos' => $datos));
        $viewmodel->setTerminal(true);
        return $viewmodel;
    }
}
