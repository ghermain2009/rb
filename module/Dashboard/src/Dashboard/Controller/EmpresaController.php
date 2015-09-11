<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Controller;

use DOMPDFModule\View\Model\PdfModel;
use Dashboard\Form\EmpresaForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcDatagrid\Column;
use Zend\Session\Container;
use Zend\Json\Json;
/**
 * Description of EmpresaController
 *
 * @author Administrador
 */
class EmpresaController extends AbstractActionController {
    //put your code here
    public function indexAction() {
        $viewmodel = new ViewModel();
        $form = new EmpresaForm();
        $request = $this->getRequest();
        $serviceLocator = $this->getServiceLocator();
        $form->get('submit');
        $message = ""; //Message

        if ($request->isPost()) {
            // @TODO addfilters
            //$form->setInputFilter($filters);
            $form->setData($request->getPost());
            if ($form->isValid()) {
                //echo $data['id_campana'];
                $data = $form->getData();
                $empresaTable = $serviceLocator->get('Dashboard\Model\GenempresaTable');
                unset($data['submit']);
                $rs = $empresaTable->addEmpresa($data);
                if ($rs) {
                    $form = new EmpresaForm($empresaTable);
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
        $grid->setDefaultItemsPerPage(5);
        $grid->setToolbarTemplate('layout/list-toolbar');
        $grid->setDataSource($sl->get('Dashboard\Model\GenempresaTable')
                        ->getEmpresaList(), $dbAdapter);

        $col = new Column\Select('id_empresa', 'c');
        $col->setLabel('id');
        $col->setWidth(25);
        $col->setIdentity(true);
        $col->setSortDefault(1, 'ASC');
        $grid->addColumn($col);
        
         $col = new Column\Select('razon_social', 'c');
        $col->setLabel('Razón Social');
        $col->setWidth(15);
        $grid->addColumn($col);
        
        $col = new Column\Select('registro_contribuyente', 'c');
        $col->setLabel('RUC / CUIT');
        $col->setWidth(10);
        $grid->addColumn($col);

        $col = new Column\Select('direccion_facturacion', 'c');
        $col->setLabel('Dirección');
        $col->setWidth(25);
        $grid->addColumn($col);

        $col = new Column\Select('descripcion', 'c');
        $col->setLabel('Descripción');
        $col->setWidth(25);
        $grid->addColumn($col);

        $editBtn = new Column\Action\Button();
        $editBtn->setLabel(' ');
        $editBtn->setAttribute('class', 'btn btn-primary glyphicon glyphicon-edit');
        $editBtn->setAttribute('href', '/dashboard/empresa/edit/id/' . $editBtn->getRowIdPlaceholder());
        $editBtn->setAttribute('data-toggle', 'tooltip');
        $editBtn->setAttribute('data-placement', 'left');
        $editBtn->setAttribute('title', 'Editar Empresa');
        
        $conBtn = new Column\Action\Button();
        $conBtn->setLabel(' ');
        $conBtn->setAttribute('class', 'btn btn-info glyphicon glyphicon-list-alt');
        $conBtn->setAttribute('href', 'javascript:registrarcontrato('. $conBtn->getRowIdPlaceholder().');');
        $conBtn->setAttribute('data-toggle', 'tooltip');
        $conBtn->setAttribute('data-placement', 'left');
        $conBtn->setAttribute('title', 'Contrato Empresa');

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
        $empresaTable = $sl->get('Dashboard\Model\GenempresaTable');
        $form = new EmpresaForm();

        if ($request->isPost()) {
            // @TODO addfilters
            //$form->setInputFilter($filters);
            $dataPost = $request->getPost();
            $form->setData($dataPost);
            if ($form->isValid()) {
                $data = $form->getData();
                unset($data['submit']);
                $dataId = array('id_empresa' => $empresaId);
                $empresaTable->editEmpresa($data, $dataId);

                if (trim($dataPost["id_categoria"]) != '') {
                    $empresaCategoriaTable->savecategorias($dataPost["id_categoria"], $empresaId);
                }
            }
        } else {
            $empresaData = $empresaTable->getEmpresa($empresaId);
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
        $contratos = $empresaTable->getContratoxEmpresa($id);
        
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
            $datosEmpresa = $empresaTable->getEmpresa($cont['id_empresa']);
        
            $documentoPdf = new PdfModel();
            $documentoPdf->setOption('filename', $cont["nombre_documento"].'.pdf');
            $documentoPdf->setOption('paperOrientation', 'portrait');
            $documentoPdf->setVariables(array(
                'ruc' => $datosEmpresa[0]['registro_contribuyente'],
                'razon' => $datosEmpresa[0]['razon_social'],
                'direccion' => $datosEmpresa[0]['direccion_facturacion']
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
}
