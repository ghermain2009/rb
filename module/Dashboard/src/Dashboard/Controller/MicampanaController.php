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
        $datosHistorialValidados = $cuponTable->getHistorialValidadosEmpresa($id_empresa);
        $datosHistorialPagados = $cuponTable->getHistorialPagadosEmpresa($id_empresa);
        $datosHistorialDia = $cuponTable->getHistorialpordiaEmpresa($id_empresa);
        $datosLiquidacion = $liquidacionTable->getLiquidaciones($id_empresa,3);
        $datosCampana = $campanaTable->getCampanaActiva($id_empresa);
        
        $nombre_empresa = $datosEmpresa[0]['razon_social'];
        
        //$this->layout('layout/micampana');
        
        return new ViewModel(array('id_empresa' => $id_empresa,
                                   'nombre_empresa' => $nombre_empresa,
                                   'cupon_validado' => $datosCuponV,
                                   'liquidaciones' => $datosLiquidacion,
                                   'historialValidados' => $datosHistorialValidados,
                                   'historialPagados' => $datosHistorialPagados,
                                   'historialDia' => $datosHistorialDia,
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
        
        $limite = 10;
        $id_empresa = base64_decode($this->params()->fromRoute("empresa", null));
        
        $serviceLocator = $this->getServiceLocator();
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
        $datosCuponV = $cuponTable->getCuponValidado($id_empresa);
        
        $grid = $serviceLocator->get('ZfcDatagrid\Datagrid');
        $grid->setUserFilterDisabled();
        $grid->setToolbarTemplate(null);
        $grid->setDefaultItemsPerPage(9);
        $grid->setDataSource($datosCuponV);
        
        $col = new Column\Select('codigo_cupon');
        $col->setLabel('Código CupoRebueno');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);

        $col = new Column\Select('fecha_validacion');
        $col->setLabel('Fecha Validación');
        $col->setWidth(13);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('id_campana');
        $col->setLabel('N° Publicación');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('fecha_inicio');
        $col->setLabel('Fecha Publicación');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('precio_total');
        $col->setLabel('Precio Total');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('precio_total');
        $col->setLabel('Monto Pagado');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        
        $viewModel = new ViewModel();
        $viewModel->addChild($grid->getResponse(), 'cupon_validado');
        $viewModel->setVariable('id_empresa', $id_empresa);
        
        return $viewModel;
    }
    
    public function detalleliquidacionAction() {
        
        $id_empresa = base64_decode($this->params()->fromRoute("empresa", null));
        
        $serviceLocator = $this->getServiceLocator();
        $liquidacionTable = $serviceLocator->get('Dashboard\Model\CupliquidacionTable');
        $datosLiquidacion = $liquidacionTable->getLiquidaciones($id_empresa,0);
        
        $grid = $serviceLocator->get('ZfcDatagrid\Datagrid');
        $grid->setUserFilterDisabled();
        $grid->setToolbarTemplate(null);
        $grid->setDefaultItemsPerPage(9);
        $grid->setDataSource($datosLiquidacion);
        
        $col = new Column\Select('id_liquidacion');
        $col->setLabel('Número de Liquidación');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);

        $col = new Column\Select('id_campana');
        $col->setLabel('N° Publicación');
        $col->setWidth(13);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('fecha_liquidacion');
        $col->setLabel('Fecha Emisión');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('cantidad_cupones');
        $col->setLabel('Cantidad de CupoRebuenos');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('total_liquidacion');
        $col->setLabel('Total');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $viewModel = new ViewModel();
     
        $viewModel->addChild($grid->getResponse(), 'liquidaciones');
        $viewModel->setVariable('id_empresa', $id_empresa);
        
        return $viewModel;
    }
    
    public function historiacampanasAction() {
        
        $id_empresa = base64_decode($this->params()->fromRoute("empresa", null));
        
        $serviceLocator = $this->getServiceLocator();
        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $datosCampana = $campanaTable->getCampanaActiva($id_empresa);
        
        $grid = $serviceLocator->get('ZfcDatagrid\Datagrid');
        $grid->setUserFilterDisabled();
        $grid->setToolbarTemplate(null);
        $grid->setDefaultItemsPerPage(9);
        $grid->setDataSource($datosCampana);
        
        $col = new Column\Select('descripcion');
        $col->setLabel('Titulo de Campaña');
        $col->setWidth(23);
        $col->setUserSortDisabled();
        $grid->addColumn($col);

        $col = new Column\Select('fecha_inicio');
        $col->setLabel('Fecha Inicio');
        $col->setWidth(13);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('fecha_final');
        $col->setLabel('Fecha Final');
        $col->setWidth(13);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('vendidos');
        $col->setLabel('Cantidad Vendidos');
        $col->setWidth(13);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('validados');
        $col->setLabel('Cantidad Validados');
        $col->setWidth(13);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $col = new Column\Select('pagados');
        $col->setLabel('Cantidad Pagados');
        $col->setWidth(13);
        $col->setUserSortDisabled();
        $grid->addColumn($col);
        
        $viewModel = new ViewModel();
        
        $viewModel->addChild($grid->getResponse(), 'historico_campanas');
        $viewModel->setVariable('id_empresa', $id_empresa);
        
        return $viewModel;
    }
}
