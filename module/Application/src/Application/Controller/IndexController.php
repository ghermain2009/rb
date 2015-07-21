<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        $en_produccion = $config['en_produccion'];
        $constantes = $config['constantes'];
        $moneda = $config['moneda'];

        if( !$en_produccion ) {
            $this->redirect()->toRoute('index2');
        }
        
        $user_session = new Container('user');
        
        $user_session->facebook = array('id' => $constantes["id_facebook"],
                                        've' => $constantes["ve_facebook"]);
        
        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $datos = $campanaTable->getCampanasAll();
        
        $data = array();
        foreach ($datos as $dato) {
             $data[] = $dato;
        }
        
        $datosG = $campanaTable->getCampanaGrupo();
        
        $dataG = array();
        foreach ($datosG as $dato) {
             $dataG[] = $dato;
        }
        
        return new ViewModel(array('data' => $data, 
                                   'dataG' => $dataG,
                                   'user_session' => $user_session,
                                   'moneda' => $moneda,
                                   ));
    }
    
    public function phpinfoAction()
    {
        return new ViewModel();
    }
    
    public function index2Action()
    {
        $this->layout('layout/layout_afiliacion');
        return new ViewModel();
    }
    
    
}
