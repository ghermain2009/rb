<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Dashboard;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Dashboard\Model\Privilege;
use Dashboard\Model\PrivilegeTable;
use Dashboard\Model\Role;
use Dashboard\Model\RoleTable;
use Dashboard\Model\User;
use Dashboard\Model\UserTable;

use Dashboard\Model\Cupcampana;
use Dashboard\Model\CupcampanaTable;
use Dashboard\Model\Cupcampanacategoria;
use Dashboard\Model\CupcampanacategoriaTable;
use Dashboard\Model\Cupcampanaopcion;
use Dashboard\Model\CupcampanaopcionTable;
use Dashboard\Model\Cupcliente;
use Dashboard\Model\CupclienteTable;
use Dashboard\Model\Cupcupon;
use Dashboard\Model\CupcuponTable;
use Dashboard\Model\Cupliquidacion;
use Dashboard\Model\CupliquidacionTable;

use Dashboard\Model\Genempresa;
use Dashboard\Model\GenempresaTable;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $sharedManager = $eventManager->getSharedManager();
        //Setting layouts
        $sharedManager->attach('Zend\Mvc\Controller\AbstractController', 'dispatch', function($e) {
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config          = $e->getApplication()->getServiceManager()->get('config');

            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
        
        $router = $sm->get('router');
        $request = $sm->get('request');
        $matchedRoute = $router->match($request);
        if (null !== $matchedRoute) {
            //Check the Authentication in every controller different with Login
            //If there is no identity this will redirect to Login
            $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function() use ($sm) {
                $sm->get('ControllerPluginManager')->get('Authentication')->isAuthtenticated();    
            }, 2);
            // @todo implement ACL
            //Check ACL and show the menu
            //$sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function() use ($sm) {
            //    $sm->get('ControllerPluginManager')->get('Privileges')->doAuthorization();
            //}, 2);
        }
        
        $e->getApplication()->getServiceManager()->get('translator');
        $e->getApplication()->getServiceManager()->get('viewhelpermanager')->setFactory('controllerName', function($sm) use ($e) {
            $viewHelper = new \Dashboard\Helper\ControllerNameHelper($e->getRouteMatch());
            
            return $viewHelper;
        });
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
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                    'NavigationHome'  => 'Application\Navigation\ApplicationMenuFactory',
                    'Navigation'  => 'Dashboard\Navigation\DashboardMenuFactory',
                    //Registrando Modelos
                    'Dashboard\Model\PrivilegeTable' => function($sl){
                        $gateway = $sl->get('PrivilegeTableGateway');
                        $table = new PrivilegeTable($gateway);
                        return $table;
                    },
                    'PrivilegeTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Privilege());
                        $tableGateway = new TableGateway('privilege', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                    'Dashboard\Model\UserTable' => function($sl){
                        $gateway = $sl->get('UserTableGateway');
                        $table = new UserTable($gateway);
                        return $table;
                    },
                    'UserTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new User());
                        $tableGateway = new TableGateway('user', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                    'Dashboard\Model\RoleTable' => function($sl){
                        $gateway = $sl->get('RoleTableGateway');
                        $table = new RoleTable($gateway);
                        return $table;
                    },
                    'RoleTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Role());
                        $tableGateway = new TableGateway('role', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                    'Dashboard\Model\CupcampanaTable' => function($sl){
                        $gateway = $sl->get('CupcampanaTableGateway');
                        $table = new CupcampanaTable($gateway);
                        return $table;
                    },
                    'CupcampanaTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Cupcampana());
                        $tableGateway = new TableGateway('cup_campana', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                    'Dashboard\Model\CupcampanacategoriaTable' => function($sl){
                        $gateway = $sl->get('CupcampanacategoriaTableGateway');
                        $table = new CupcampanacategoriaTable($gateway);
                        return $table;
                    },
                    'CupcampanacategoriaTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Cupcampanacategoria());
                        $tableGateway = new TableGateway('cup_campana_categoria', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                    'Dashboard\Model\CupcampanaopcionTable' => function($sl){
                        $gateway = $sl->get('CupcampanaopcionTableGateway');
                        $table = new CupcampanaopcionTable($gateway);
                        return $table;
                    },
                    'CupcampanaopcionTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Cupcampanaopcion());
                        $tableGateway = new TableGateway('cup_campana_opcion', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                    'Dashboard\Model\CupcuponTable' => function($sl){
                        $gateway = $sl->get('CupcuponTableGateway');
                        $table = new CupcuponTable($gateway);
                        return $table;
                    },
                    'CupcuponTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Cupcupon());
                        $tableGateway = new TableGateway('cup_cupon', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                            
                    'Dashboard\Model\CupliquidacionTable' => function($sl){
                        $gateway = $sl->get('CupliquidacionTableGateway');
                        $table = new CupliquidacionTable($gateway);
                        return $table;
                    },
                    'CupliquidacionTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Cupliquidacion());
                        $tableGateway = new TableGateway('cup_liquidacion', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                            
                    'Dashboard\Model\CupclienteTable' => function($sl){
                        $gateway = $sl->get('CupclienteTableGateway');
                        $table = new CupclienteTable($gateway);
                        return $table;
                    },
                    'CupclienteTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Cupcliente());
                        $tableGateway = new TableGateway('cup_cliente', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                    'Dashboard\Model\GenempresaTable' => function($sl){
                        $gateway = $sl->get('GenempresaTableGateway');
                        $table = new GenempresaTable($gateway);
                        return $table;
                    },
                    'GenempresaTableGateway' => function($sl) {
                        $adapter = $sl->get('Zend\Db\Adapter\Adapter');
                        $rsPrototype = new ResultSet();
                        $rsPrototype->setArrayObjectPrototype(new Genempresa());
                        $tableGateway = new TableGateway('gen_empresa', $adapter, null, $rsPrototype);
                        return $tableGateway;
                    },
                )
            );
            
    }
}
