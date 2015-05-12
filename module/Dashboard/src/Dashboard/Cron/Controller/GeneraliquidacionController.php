<?php

namespace Dashboard\Cron\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GeneraliquidacionController extends AbstractActionController
{
    public function generaLiquidacionAction()
    {
       $view = new ViewModel();
       //$view->setTerminal(true);
       
       $serviceLocator = $this->getServiceLocator();
       $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
       $datosCuponV = $cuponTable->getCuponesxLiquidar();
       
       $cantidad = count($datosCuponV);
       $id_campana = -1;
       $id_campana_opcion = -1;
       if( $cantidad > 0 ) {
           for( $i=0; $i<$cantidad; $i++) {
               if($id_campana != $datosCuponV[0]['id_campa'] || $id_campana_opcion != $datosCuponV[0]['id_campana_opcion']) {
                   
               }
           }
       }
       
       exit();
       
       return $view;
    }
}
