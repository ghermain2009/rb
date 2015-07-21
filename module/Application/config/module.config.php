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
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'cliente' => array(
                //'type' => 'Zend\Mvc\Router\Http\Literal',
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/cliente[/:action]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Cliente',
                        'action'     => 'index',
                    ),
                    
                ),
            ),
            'interes' => array(
                //'type' => 'Zend\Mvc\Router\Http\Literal',
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/interes[/:action]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Interes',
                        'action'     => 'index',
                    ),
                ),
            ),
            'voucher' => array(
                //'type' => 'Zend\Mvc\Router\Http\Literal',
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/campana/cuponbuenaso',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Campana',
                        'action'     => 'cuponbuenaso',
                    ),
                ),
            ),
            'detalle' => array(
                //'type' => 'Zend\Mvc\Router\Http\Literal',
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/campana[/:action[/:id][/:op][/:fl][/:em]]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Campana',
                        'action'     => 'detalle',
                    ),
                ),
            ),
            'phpinfo' => array(
                //'type' => 'Zend\Mvc\Router\Http\Literal',
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/index/:action',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'phpinfo',
                    ),
                ),
            ),
            'index2' => array(
                //'type' => 'Zend\Mvc\Router\Http\Literal',
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/:action',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index2',
                    ),
                ),
            ),
            'home' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
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
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Campana' => 'Application\Controller\CampanaController',
            'Application\Controller\Interes' => 'Application\Controller\InteresController',
            'Application\Controller\Cliente' => 'Application\Controller\ClienteController',
            ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'index/index'       => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    //Layouts to module
    'module_layouts' => array(
        'Application' => 'layout/layout',
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);