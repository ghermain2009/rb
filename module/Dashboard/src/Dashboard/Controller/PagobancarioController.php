<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Controller;

use DOMPDFModule\View\Model\PdfModel;
use Dashboard\Form\PagobancarioForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcDatagrid\Column;
use Zend\Session\Container;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;
/**
 * Description of PagobancarioController
 *
 * @author Administrador
 */
class PagobancarioController extends AbstractActionController {
    //put your code here
    public function indexAction() {
        $viewmodel = new ViewModel();
        $serviceLocator = $this->getServiceLocator();
        $tipoDocumentoTable = $serviceLocator->get('Dashboard\Model\GentipodocumentoTable');
        $form = new PagobancarioForm($tipoDocumentoTable);
        $request = $this->getRequest();
        
        $form->get('submit');
        $message = ""; //Message

        if ($request->isPost()) {
            // @TODO addfilters
            //$form->setInputFilter($filters);
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                
                $empresaTable = $serviceLocator->get('Dashboard\Model\GenempresaTable');
                unset($data['submit']);
                $rs = $empresaTable->addPagobancario($data);
                if ($rs) {
                    //$form = new PagobancarioForm($tipoDocumentoTable);
                    $this->redirect()->toRoute('dash_empresa_edit', array('id' => $rs));
                }
            }
        }
        $viewmodel->form = $form;
        $viewmodel->message = $message;
        return $viewmodel;
    }

    public function listAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();
            if ($postData->btnAdd === 'add') {
                $this->redirect()->toRoute('dash_empresa');
            }
        }

        $sl = $this->getServiceLocator();
        $dbAdapter = $sl->get('Zend\Db\Adapter\Adapter');
        $grid = $sl->get('ZfcDatagrid\Datagrid');
        $grid->setDefaultItemsPerPage(10);
        $grid->setToolbarTemplate('layout/list-toolbar');
        $grid->setDataSource($sl->get('Dashboard\Model\CupcuponTable')->getPagobancarioList());
        
        $col = new Column\Select('id_cupon');
        $col->setLabel('id');
        $col->setWidth(25);
        $col->setIdentity(true);
        $col->setSortDefault(1, 'ASC');
        $grid->addColumn($col);
        
        $col = new Column\Select('id_cupon');
        $col->setLabel('Codigo Transacción');
        $col->setWidth(10);
        $grid->addColumn($col);
        
        $col = new Column\Select('tipo');
        $col->setHidden();
        $col->setWidth(1);
        $style = new Column\Style\BackgroundColor(array(
            183,
            249,
            188
        ));
        $style->addByValue($col, '1');
        $grid->addRowStyle($style);
        $grid->addColumn($col);
        
        
        $col = new Column\Select('campana_descripcion');
        $col->setLabel('Detalle Compra');
        $col->setWidth(20);
        $grid->addColumn($col);
        
        $col = new Column\Select('cantidad');
        $col->setLabel('Cantidad Cupones');
        $col->setWidth(10);
        $grid->addColumn($col);

        $col = new Column\Select('precio_total');
        $col->setLabel('Precio Total');
        $col->setWidth(10);
        $grid->addColumn($col);

        $col = new Column\Select('fecha_compra');
        $col->setLabel('Fecha de Pago');
        $col->setWidth(10);
        $grid->addColumn($col);

        $editBtn = new Column\Action\Button();
        $editBtn->setLabel(' ');
        $editBtn->setAttribute('class', 'btn btn-primary glyphicon glyphicon-edit');
        $editBtn->setAttribute('href', '/dashboard/empresa/edit/id/' . $editBtn->getRowIdPlaceholder());
        $editBtn->setAttribute('data-toggle', 'tooltip');
        $editBtn->setAttribute('data-placement', 'left');
        $editBtn->setAttribute('title', 'Editar Pagobancario');
        
        $conBtn = new Column\Action\Button();
        $conBtn->setLabel(' ');
        $conBtn->setAttribute('class', 'btn btn-success glyphicon glyphicon-usd');
        $conBtn->setAttribute('href', 'javascript:registrarpagobancario('.$conBtn->getRowIdPlaceholder().');');
        $conBtn->setAttribute('data-toggle', 'tooltip');
        $conBtn->setAttribute('data-placement', 'left');
        $conBtn->setAttribute('title', 'Confirmar Pago');

        $col = new Column\Action();
        $col->addAction($editBtn);
        $col->addAction($conBtn);
        $grid->addColumn($col);

        return $grid->getResponse();
        
    }

    public function editAction() {
        $serviceLocator = $this->getServiceLocator();

        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];



        $empresaId = $this->params('id');

        $edit_campana = new Container('edit_empresa');
        $edit_campana->id = $empresaId;

        $request = $this->getRequest();
        $viewmodel = new ViewModel(array('dir_image' => $dir_image));
        $sl = $this->getServiceLocator();
        $tipoDocumentoTable = $sl->get('Dashboard\Model\GentipodocumentoTable');
        $empresaTable = $sl->get('Dashboard\Model\GenempresaTable');
        
        $form = new PagobancarioForm($tipoDocumentoTable);

        if ($request->isPost()) {
            // @TODO addfilters
            //$form->setInputFilter($filters);
            $dataPost = $request->getPost();
            $form->setData($dataPost);
            if ($form->isValid()) {
                $data = $form->getData();
                unset($data['submit']);
                $dataId = array('id_empresa' => $empresaId);
                $empresaTable->editPagobancario($data, $dataId);

                /*if (trim($dataPost["id_categoria"]) != '') {
                    $empresaCategoriaTable->savecategorias($dataPost["id_categoria"], $empresaId);
                }*/
            }
        } else {
            $empresaData = $empresaTable->getPagobancario($empresaId);
            $empresa = $empresaData[0];
            //foreach ($empresaData as $empresa) {
                $form->get('id_empresa')->setValue($empresa['id_empresa']);
                $form->get('razon_social')->setValue($empresa['razon_social']);
                $form->get('registro_contribuyente')->setValue($empresa['registro_contribuyente']);
                $form->get('direccion_facturacion')->setValue($empresa['direccion_facturacion']);
                $form->get('direccion_comercial')->setValue($empresa['direccion_comercial']);
                $form->get('telefono')->setValue($empresa['telefono']);
                $form->get('horario')->setValue($empresa['horario']);
                $form->get('web_site')->setValue($empresa['web_site']);
                $form->get('ubicacion_gps')->setValue($empresa['ubicacion_gps']);
                $form->get('numero_cuenta')->setValue($empresa['numero_cuenta']);
                $form->get('descripcion')->setValue($empresa['descripcion']);
                $form->get('tipo_documento_representante')->setValue($empresa['tipo_documento_representante']);
                $form->get('documento_representante')->setValue($empresa['documento_representante']);
                $form->get('nombre_representante')->setValue($empresa['nombre_representante']);
                $form->get('id_operador')->setValue($empresa['id_operador']);
            //}
        }

//        $opciones = $empresaOpcionTable->getOpcionxCampanaAll($empresaId);
//        $opcionCampana = array();
//        foreach ($opciones as $opcion) {
//            $opcionCampana[] = $opcion;
//        }


        $viewmodel->form = $form;
        //$viewmodel->setVariable('opciones', $opcionCampana);

        return $viewmodel;
    }
    
        /* public function deleteAction()
      {
      $sl = $this->getServiceLocator();
      $userId = $this->params('id');
      $userTable = $sl->get('Dashboard\Model\CampanaTable');
      $userTable->deleteCampana($userId);
      $this->redirect()->toRoute('dash_user_list');
      } */
    
    public function contratoAction() {
        
        $id = $this->params()->fromPost("id", null);
        
        $serviceLocator = $this->getServiceLocator();
        $empresaTable = $serviceLocator->get('Dashboard\Model\GenempresaTable');
        $contratos = $empresaTable->getContratoxPagobancario($id);
        
        if( count($contratos) > 0) {
            $id_contrato = $contratos[0]['id_contrato'];
        } else {
            $id_contrato = -1;
        }
        
        return $this->getResponse()->setContent(Json::encode(array('id_contrato' => $id_contrato)));
        
    }
    
    public function registrarcontratoAction() {
        $id_empresa = $this->params()->fromPost("id_empresa", null);
        $nombre     = $this->params()->fromPost("nombre", null);
        $email      = $this->params()->fromPost("email", null);
        
        $serviceLocator = $this->getServiceLocator();
        $contratoTable = $serviceLocator->get('Dashboard\Model\ConcontratoTable');
        
        $contrato = array('id_empresa'      => $id_empresa,
                          'nombre_contacto' => $nombre,
                          'email_contacto'  => $email,
                          'id_estado'       => '1'
                          );
        
        $id_contrato = $contratoTable->addContrato($contrato);
        
        return $this->getResponse()->setContent(Json::encode(array('id_contrato' => $id_contrato)));
    }
    
    public function editarcontratoAction() {
        
        set_time_limit(0);
        
        $id_contrato = $this->params()->fromPost("id_contrato", null);
        
        $serviceLocator = $this->getServiceLocator();
        $contratoTable = $serviceLocator->get('Dashboard\Model\ConcontratoTable');
        $contrato = $contratoTable->getContratoId($id_contrato);
        
        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];
        
        $directorio = $dir_image.$sep_path."..".$sep_path."..".$sep_path."data".$sep_path."contratos".$sep_path;
        
        foreach( $contrato as $cont ) {
            
            $empresaTable = $serviceLocator->get('Dashboard\Model\GenempresaTable');
            $datosPagobancario = $empresaTable->getPagobancario($cont['id_empresa']);
        
            if(!is_file($directorio.$cont["nombre_documento"].'.pdf')) {
                $documentoPdf = new PdfModel();
                $documentoPdf->setOption('filename', $cont["nombre_documento"].'.pdf');
                $documentoPdf->setOption('paperOrientation', 'portrait');
                $documentoPdf->setVariables(array(
                    'ruc' => $datosPagobancario[0]['registro_contribuyente'],
                    'razon' => $datosPagobancario[0]['razon_social'],
                    'direccion' => $datosPagobancario[0]['direccion_facturacion'],
                    'tipdoc_representante' => $datosPagobancario[0]['tipo_documento'],
                    'numero_representante' => $datosPagobancario[0]['documento_representante'],
                    'nombre_representante' => $datosPagobancario[0]['nombre_representante'],
                    'nombre_mes' => $this->_obtenerNombreMes(date('m'))
                ));

                $documentoPdf->setTerminal(true);
                $documentoPdf->setTemplate('dashboard/empresa/contrato-pdf.phtml');
                $htmlPdf = $serviceLocator->get('viewPdfrenderer')->getHtmlRenderer()->render($documentoPdf);
                $engine = $serviceLocator->get('viewPdfrenderer')->getEngine();
                // Cargamos el HTML en DOMPDF
                $engine->load_html($htmlPdf);
                $engine->render();
                // Obtenemos el PDF en memoria
                $pdfCode = $engine->output();

                file_put_contents($directorio.$cont["nombre_documento"].'.pdf', $pdfCode);
            }
        }
        
        return new ViewModel(array('contrato' => $contrato ));
        
    }
    
    public function obtenerPdfContratoAction() 
    {
        $serviceLocator = $this->getServiceLocator();
        $response = $this->getResponse();
        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];
        
        $directorio = $dir_image.$sep_path."..".$sep_path."..".$sep_path."data".$sep_path."contratos".$sep_path;
        
        $params = $this->params()->fromQuery();
        
        $nombreDocumento = !empty($params['nombre_documento']) ? $params['nombre_documento'] : '';
        
        $rutaDocumento = $directorio. 
                         $nombreDocumento.'.pdf';
        
        //if(!is_file($rutaDocumento)) {
        //    $this->redirect()->toRoute('documento-electronico');
        //}

        $contenidoDocumento = file_get_contents($rutaDocumento);
        $response->setContent($contenidoDocumento);

        $headers = $response->getHeaders();
        $headers->clearHeaders()
                ->addHeaderLine('Content-Type', 'application/pdf')
                ->addHeaderLine('Content-Disposition', 'attachment; filename="'.$nombreDocumento.'.pdf"')
                ->addHeaderLine('Content-Length', strlen($contenidoDocumento));


        return $this->response;
    }
    
    private function _obtenerNombreMes($mes) {
        switch($mes) {
            case '01' : $nombre = 'Enero';
                break;
            case '02' : $nombre = 'Febrero';
                break;
            case '03' : $nombre = 'Marzo';
                break;
            case '04' : $nombre = 'Abril';
                break;
            case '05' : $nombre = 'Mayo';
                break;
            case '06' : $nombre = 'Junio';
                break;
            case '07' : $nombre = 'Julio';
                break;
            case '08' : $nombre = 'Agosto';
                break;
            case '09' : $nombre = 'Setiembre';
                break;
            case '10' : $nombre = 'Octubre';
                break;
            case '11' : $nombre = 'Noviembre';
                break;
            case '12' : $nombre = 'Diciembre';
                break;
        }
        
        return $nombre;
    }
    
    public function prepagadoAction() {
        
        $id = $this->params()->fromPost("id", null);
        
        $serviceLocator = $this->getServiceLocator();
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
        $prepagado = $cuponTable->getCuponPrepagado($id);
        
        if( count($prepagado) > 0) {
            $pagado = 1; 
        } else {
            $pagado = -1;
        }
        
        return $this->getResponse()->setContent(Json::encode(array('pagado' => $pagado,
                                                                   'prepagado' => $prepagado)));
        
    }
    
    public function registrarpagobancarioAction(){
        $id_cupon = $this->params()->fromPost("id_cupon", null);
        $numero_operacion     = $this->params()->fromPost("numero_operacion", null);
        $fecha_operacion      = $this->params()->fromPost("fecha_operacion", null);
        
        $serviceLocator = $this->getServiceLocator();
        $contratoTable = $serviceLocator->get('Dashboard\Model\ConcontratoTable');
        
        $contrato = array('id_empresa'      => $id_empresa,
                          'nombre_contacto' => $nombre,
                          'email_contacto'  => $email,
                          'id_estado'       => '1'
                          );
        
        $id_contrato = $contratoTable->addContrato($contrato);
        
        return $this->getResponse()->setContent(Json::encode(array('id_contrato' => $id_contrato)));
    }
}