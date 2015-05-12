<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /dashboard/:controller/:action
            'dashboard' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'dash_login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard/login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'login',
                        'action' => 'index',
                    ),
                ),
            ),
            'dash_logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard/logout',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'login',
                        'action' => 'logout',
                    ),
                ),
            ),
            'dash_index' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
                ),
            ),
            'dash_role' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard/role',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'role',
                        'action' => 'index',
                    ),
                ),
            ),
            'dash_user' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'user',
                        'action' => 'index',
                    ),
                ),
            ),
            'dash_user_list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/user/list/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'user',
                        'action' => 'list',
                    ),
                ),
            ),
            'dash_user_edit' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/user/edit/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'user',
                        'action' => 'edit',
                    ),
                ),
            ),
            'dash_user_del' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/user/delete/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'user',
                        'action' => 'delete',
                    ),
                ),
            ),
            'dash_role_list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/role/list/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'role',
                        'action' => 'list',
                    ),
                ),
            ),
            'dash_role_edit' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/role/edit/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'role',
                        'action' => 'edit',
                    ),
                ),
            ),
            'dash_role_del' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/role/delete/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'role',
                        'action' => 'delete',
                    ),
                ),
            ),
            //campana
            'dash_campana' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard/campana',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'campana',
                        'action' => 'index',
                    ),
                ),
            ),
            'dash_campana_list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/campana/list/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'campana',
                        'action' => 'list',
                    ),
                ),
            ),
            'dash_campana_edit' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/campana/edit/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'campana',
                        'action' => 'edit',
                    ),
                ),
            ),
            //empresa
            'dash_empresa' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard/empresa',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'empresa',
                        'action' => 'index',
                    ),
                ),
            ),
            'dash_empresa_list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/empresa/list/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'empresa',
                        'action' => 'list',
                    ),
                ),
            ),
            'dash_empresa_edit' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/empresa/edit/id[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'empresa',
                        'action' => 'edit',
                    ),
                ),
            ),
            //campana
            'dash_campana' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard/campana',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'campana',
                        'action' => 'index',
                    ),
                ),
            ),
            //mi campana
            'dash_micampana' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/micampana/cupon',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'micampana',
                        'action' => 'cupon',
                    ),
                ),
            ),
            'dash_detallevalidado' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/micampana/detallevalidado[/:empresa]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'micampana',
                        'action' => 'detallevalidado',
                    ),
                ),
            ),
            'dash_detalleliquidacion' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/micampana/detalleliquidacion[/:empresa]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'micampana',
                        'action' => 'detalleliquidacion',
                    ),
                ),
            ),
            'dash_historiacampanas' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/micampana/historiacampanas[/:empresa]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'micampana',
                        'action' => 'historiacampanas',
                    ),
                ),
            ),
            'dash_cron' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/cron/generaliquidacion',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Cron\Controller',
                        'controller' => 'generaliquidacion',
                        'action' => 'generaliquidacion',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Dashboard\Controller\Index' => 'Dashboard\Controller\IndexController',
            'Dashboard\Controller\Login' => 'Dashboard\Controller\LoginController',
            'Dashboard\Controller\User' => 'Dashboard\Controller\UserController',
            'Dashboard\Controller\Role' => 'Dashboard\Controller\RoleController',
            'Dashboard\Controller\Campana' => 'Dashboard\Controller\CampanaController',
            'Dashboard\Controller\Empresa' => 'Dashboard\Controller\EmpresaController',
            'Dashboard\Controller\Micampana' => 'Dashboard\Controller\MicampanaController',
            'Dashboard\Cron\Controller\Generaliquidacion' => 'Dashboard\Cron\Controller\GeneraliquidacionController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layoutDashboard' => __DIR__ . '/../view/layout/layout.phtml',
            'dashboard/index/index' => __DIR__ . '/../view/dashboard/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    //Layouts to module
    'module_layouts' => array(
        'Dashboard' => 'layout/layoutDashboard',
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'Authentication' => 'Dashboard\Plugin\Authentication',
            'RouteHelper' => 'Dashboard\Helper\RouteHelper',
            'Privileges' => 'Dashboard\Plugin\Privileges',
            
        )
    ),
);
