<?php

/**
 * Description of DashboardMenu
 *
 * @author fragote
 */
namespace Application\Navigation;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Authentication\AuthenticationService;
use Application\Helper\RouteHelper;

class ApplicationMenu extends DefaultNavigationFactory
{
    protected function getPages(ServiceLocatorInterface $serviceLocator) 
    {
        $menu = array();
        if (null == $this->pages) {
 
            $mvcEvent = $serviceLocator->get('Application')->getMvcEvent();
            $listMenu = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
            
            $dataMenu = $listMenu->getMenu();
            $menu = $this->menuFormat($dataMenu);
            
            $routeMatch = $mvcEvent->getRouteMatch();
            $router = $mvcEvent->getRouter();
            $pages = $this->getPagesFromConfig($menu);
            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }
        
        return $this->pages;
    }
    
    public function menuFormat($dataMenu) 
    {
        $menu = array();
        $routHelper = new RouteHelper();
        
        $padre = '';
        
        foreach ($dataMenu as $opt) {
            
            if( $opt['categoria'] != $padre ) {
                $menu[$opt['id_categoria']] = array(
                    'label' => $opt['categoria'],
                    'uri' => "",
                    'order' => $opt['cantidad']
                );
            }
            
            $menu[$opt['id_categoria']]['pages'][] = array(
                'label' => $opt['subcategoria'],
                //'uri' => "javascript:postfunction('/campana','categoria','".base64_encode($opt['id_categoria'])."','".base64_encode($opt['id_sub_categoria'])."');",
                'uri' => "/campana/categoria/".base64_encode($opt['id_categoria'])."/".base64_encode($opt['id_sub_categoria']),
                'order' => $opt['cantidad']
            );
            
            $padre = $opt['categoria'];
        }
        
        return $menu;
    }
}
