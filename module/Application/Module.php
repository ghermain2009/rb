<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager(); 
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function(MvcEvent $event) use ($serviceManager ) 
        { 
            $response = $event->getResponse(); 
            if ($response->getStatusCode() == "404") 
            { 
                $viewModel = new \Zend\View\Model\ViewModel(array( 
                'message' => 'Page not found', 
                'reason' => 'error-occurred', 
                'exception' => 'Page not found - 404', 
                )); 

                $request = $event->getRequest(); 
                $response = $event->getResponse(); 

                //Render AJAX error view if it's an AJAX Request, 
                //else render normal error view. 
                if ($request->isXmlHttpRequest()) { 
                    $viewModel->setTerminal(true); 
                    $viewModel->setTemplate('error/404-ajax.phtml'); 
                } 
                else 
                { 
                    $viewModel->setTemplate('error/404.phtml'); 
                } 
                $event->getViewModel()->addChild($viewModel); 
                $event->stopPropagation(); 
                $response->setStatusCode(200); 
                return $viewModel; 
            } 
        }, -100);
    
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    
}
