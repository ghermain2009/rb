<?php

/**
 * CampanaController - This allows add, delete and edit users
 * @autor Francis Gonzales <fgonzalestello91@gmail.com>
 */

namespace Dashboard\Controller;

use Dashboard\Form\CampanaForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcDatagrid\Column;
use Zend\Session\Container;
use Zend\Json\Json;

class CampanaController extends AbstractActionController {

    public function indexAction() {
        $viewmodel = new ViewModel();
        $sl = $this->getServiceLocator();
        $empresaTable = $sl->get('Dashboard/Model/GenempresaTable');
        $form = new CampanaForm($empresaTable);
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
                $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
                unset($data['submit']);
                $rs = $campanaTable->addCampana($data);
                if ($rs) {
                    //$form = new CampanaForm($empresaTable);
                    $this->redirect()->toRoute('dash_campana_edit', array('id' => $rs));
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
                $this->redirect()->toRoute('dash_campana');
            }
        }

        $sl = $this->getServiceLocator();
        $dbAdapter = $sl->get('Zend\Db\Adapter\Adapter');
        $grid = $sl->get('ZfcDatagrid\Datagrid');
        $grid->setDefaultItemsPerPage(5);
        $grid->setToolbarTemplate('layout/list-toolbar');
        $grid->setDataSource($sl->get('Dashboard\Model\CupcampanaTable')
                        ->getCampanaList(), $dbAdapter);

        $col = new Column\Select('id_campana', 'c');
        $col->setLabel('id');
        $col->setWidth(25);
        $col->setIdentity(true);
        $col->setSortDefault(1, 'ASC');
        $grid->addColumn($col);
        
         $col = new Column\Select('subtitulo', 'c');
        $col->setLabel('Titulo Portada');
        $col->setWidth(25);
        $grid->addColumn($col);

        $col = new Column\Select('titulo', 'c');
        $col->setLabel('Titulo Detalle');
        $col->setWidth(25);
        $grid->addColumn($col);

        $col = new Column\Select('razon_social', 'e');
        $col->setLabel('Empresa');
        $col->setWidth(25);
        $grid->addColumn($col);

        $editBtn = new Column\Action\Button();
        $editBtn->setLabel(' ');
        $editBtn->setAttribute('class', 'btn btn-primary glyphicon glyphicon-edit');
        $editBtn->setAttribute('href', '/dashboard/campana/edit/id/' . $editBtn->getRowIdPlaceholder());
        $editBtn->setAttribute('data-toggle', 'tooltip');
        $editBtn->setAttribute('data-placement', 'left');
        $editBtn->setAttribute('title', 'Editar Campaña');

        $preBtn = new Column\Action\Button();
        $preBtn->setLabel(' ');
        $preBtn->setAttribute('class', 'btn btn-warning glyphicon glyphicon-eye-close');
        $preBtn->setAttribute('href', 'javascript:visualizar(' . $preBtn->getRowIdPlaceholder().');');
        $preBtn->setAttribute('data-toggle', 'tooltip');
        $preBtn->setAttribute('data-placement', 'left');
        $preBtn->setAttribute('title', 'Preview Campaña');
        
        $col = new Column\Action();
        $col->addAction($editBtn);
        $col->addAction($preBtn);
        $grid->addColumn($col);

        return $grid->getResponse();
        
    }

    public function editAction() {
        $serviceLocator = $this->getServiceLocator();

        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];

        $campanaId = $this->params('id');

        $edit_campana = new Container('edit_campana');
        $edit_campana->id = $campanaId;

        $request = $this->getRequest();
        $viewmodel = new ViewModel(array('dir_image' => $dir_image,
                                         'sep_path' => $sep_path));
        $sl = $this->getServiceLocator();
        $empresaTable = $sl->get('Dashboard\Model\GenempresaTable');
        $campanaTable = $sl->get('Dashboard\Model\CupcampanaTable');
        $campanaOpcionTable = $sl->get('Dashboard\Model\CupcampanaopcionTable');
        $campanaCategoriaTable = $sl->get('Dashboard\Model\CupcampanacategoriaTable');

        $form = new CampanaForm($empresaTable);

        if ($request->isPost()) {
            // @TODO addfilters
            //$form->setInputFilter($filters);
            $dataPost = $request->getPost();
            $form->setData($dataPost);
            if ($form->isValid()) {
                $data = $form->getData();
                unset($data['submit']);
                $dataId = array('id_campana' => $campanaId);
                $campanaTable->editCampana($data, $dataId);

                if (trim($dataPost["id_categoria"]) != '') {
                    $campanaCategoriaTable->savecategorias($dataPost["id_categoria"], $campanaId);
                }
            }
        } else {
            $campanaData = $campanaTable->getCampana($campanaId);
            foreach ($campanaData as $campana) {
                $form->get('id_campana')->setValue($campana['id_campana']);
                $form->get('titulo')->setValue($campana['titulo']);
                $form->get('subtitulo')->setValue($campana['subtitulo']);
                $form->get('descripcion')->setValue($campana['descripcion']);
                $form->get('sobre_campana')->setValue($campana['sobre_campana']);
                $form->get('observaciones')->setValue($campana['observaciones']);
                $form->get('id_empresa')->setValue($campana['id_empresa']);
                $form->get('fecha_inicio')->setValue($campana['fecha_inicio']);
                $form->get('hora_inicio')->setValue($campana['hora_inicio']);
                $form->get('fecha_final')->setValue($campana['fecha_final']);
                $form->get('hora_final')->setValue($campana['hora_final']);
                $form->get('fecha_validez')->setValue($campana['fecha_validez']);
            }
        }

        $opciones = $campanaOpcionTable->getOpcionxCampanaAll($campanaId);
        $opcionCampana = array();
        foreach ($opciones as $opcion) {
            $opcionCampana[] = $opcion;
        }

        $categorias = $campanaCategoriaTable->getcategoriaxcampana($campanaId);
        $categoriaCampana = array();
        foreach ($categorias as $categoria) {
            $categoriaCampana[] = $categoria;
        }

        $viewmodel->form = $form;
        $viewmodel->setVariable('opciones', $opcionCampana);
        $viewmodel->setVariable('categorias', $categoriaCampana);

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

    

    public function editopcionAction() {

        $opcion = $this->params()->fromPost("opcion", null);
        $campana = $this->params()->fromPost("campana", null);


        $serviceLocator = $this->getServiceLocator();
        $campanaOpcionTable = $serviceLocator->get('Dashboard\Model\CupcampanaopcionTable');

        $opciones = $campanaOpcionTable->getOpcionxCampanaId($opcion, $campana);

        $datos = array();
        foreach ($opciones as $opc) {
            $datos[] = $opc;
        }

        //$datos = array('opcion' => $opcion, 'campana' => $campana);

        return $this->getResponse()->setContent(Json::encode($datos));
    }

    public function saveopcionAction() {

        $id_campana_opcion = $this->params()->fromPost("id_campana_opcion", null);
        $id_campana = $this->params()->fromPost("id_campana", null);
        $descripcion = $this->params()->fromPost("descripcion", null);
        $precio_regular = $this->params()->fromPost("precio_regular", null);
        $precio_especial = $this->params()->fromPost("precio_especial", null);
        $comision = $this->params()->fromPost("comision", null);

        $data = array('id_campana_opcion' => $id_campana_opcion,
            'id_campana' => $id_campana,
            'descripcion' => $descripcion,
            'precio_regular' => $precio_regular,
            'precio_especial' => $precio_especial,
            'comision' => $comision
        );

        $serviceLocator = $this->getServiceLocator();
        $campanaOpcionTable = $serviceLocator->get('Dashboard\Model\CupcampanaopcionTable');

        $datos = $campanaOpcionTable->addOpcionxCampana($data);


        return $this->getResponse()->setContent(Json::encode($datos));
    }

}
