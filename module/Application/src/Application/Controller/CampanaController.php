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
use Zend\Json\Json;
use Zend\Session\Container;
use Zend\Http\Response;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\Rename;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

use Application\Services\Variados;


class CampanaController extends AbstractActionController {

    public function detalleAction() {
        
        //$identificador = $this->params()->fromPost("id", null);
        $id = base64_decode($this->params()->fromRoute("id", null));
        
        $serviceLocator = $this->getServiceLocator();

        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];
        $localhost = $config['constantes']['localhost'];
        $moneda = $config['moneda'];
        
        $user_session = new Container('user');
        $user_session->id_campana = $id;
        $user_session->localhost = $localhost;

        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $empresaTable = $serviceLocator->get('Dashboard\Model\GenempresaTable');
        
        $data   = $campanaTable->getCampanaId($id);
        $data_o = $campanaTable->getCampanaOpciones($id);
        $data_p = $campanaTable->getCampanasAllNotId($id);

        $data_e = $empresaTable->getEmpresaByCampana($id);
        
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $telefono_empresa = $config['empresa']['telefono'];
        $this->layout()->telefono_empresa = $telefono_empresa;

        return new ViewModel(array('data' => $data,
            'data_o' => $data_o,
            'data_p' => $data_p,
            'data_e' => $data_e,
            'id' => $id,
            'dir_image' => $dir_image,
            'sep_path' => $sep_path,
            'moneda' => $moneda,
            'localhost' => $localhost
            ));
    }

    public function formulariopagoAction() {

        $id = base64_decode($this->params()->fromRoute("id", null));
        $op = base64_decode($this->params()->fromRoute("op", null));
        $fl = base64_decode($this->params()->fromRoute("fl", null));
        $em = base64_decode($this->params()->fromRoute("em", null));

        $serviceLocator = $this->getServiceLocator();
        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $config = $serviceLocator->get('config');
        
        $moneda = $config['moneda'];

        $data_o = $campanaTable->getCampanaOpcionId($op);

        $this->layout('layout/layout_pago');

        $user_session = new Container('user');
        
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $telefono_empresa = $config['empresa']['telefono'];
        $this->layout()->telefono_empresa = $telefono_empresa;

        /* if ($fl == null) {
          return new ViewModel(array('id' => $id,
          'op' => $op,
          'data_o' => $data_o));
          } else { */
        return new ViewModel(array('id' => $id,
            'op' => $op,
            'fl' => $fl,
            'em' => $em,
            'data_o' => $data_o,
            'user_session' => $user_session,
            'moneda' => $moneda,
                ));
        //}
    }

    public function pagoAction() {

        $datos = $this->params()->fromPost();

        $serviceLocator = $this->getServiceLocator();

        $clienteTable = $serviceLocator->get('Dashboard\Model\CupclienteTable');
        $clienteTable->addCliente($datos);

        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
        $idTransaccion = $cuponTable->addCupon($datos,$serviceLocator);
        
        switch($datos['metodo']) {
            case '001':

                $config = $serviceLocator->get('config');
                $postURL = $config["tarjetas"];

                $url = $postURL[$datos['metodo']]['url'];
                $usuario = $postURL[$datos['metodo']]['user'];
                $password = $postURL[$datos['metodo']]['pass'];

                $request = new Request;
                $request->getHeaders()->addHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
                ]);
                $request->setUri($url);
                $request->setMethod('POST'); //uncomment this if the POST is used
                $request->getPost()->set('operacion', $idTransaccion);
                $request->getPost()->set('monto', $datos['PriceTotal']);
                $request->getPost()->set('usuario', $usuario);
                $request->getPost()->set('password', $password);

                $client = new Client;

                $client->setAdapter("Zend\Http\Client\Adapter\Curl");

                $response = $client->dispatch($request);

                return $response;
                
                break;
            
            default :
                
                $config = $serviceLocator->get('config');
                
                $pais = $config['id_pais'];
                $capital = $config['id_capital'];

                $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
                $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);

                $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
                $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
                
                $telefono_empresa = $config['empresa']['telefono'];
                
                $activo   = $config['correo']['activo'];
                $name     = $config['correo']['name'];
                $host     = $config['correo']['host'];
                $port     = $config['correo']['port'];
                $tls      = $config['correo']['tls'];
                $username = $config['correo']['username'];
                $password = $config['correo']['password'];
                $cuenta   = $config['correo']['cuenta-recuperar-clave'];
                $localhost = $config['constantes']['localhost'];
                $telefono = $config['empresa']['telefono'];
                
                if( $activo == '1' ) {

                    $email = $datos['email'];
                    
                    $message = new Message();
                    $message->addTo($email)
                            ->addFrom($cuenta)
                            ->setSubject('Estas muy cerca de obtener una oferta Rebuena...‏');

                    if( $tls ) {
                        $connection_config = array(
                            'ssl' => 'tls',
                            'username' => $username,
                            'password' => $password
                        );
                    } else {
                        $connection_config = array(
                            'username' => $username,
                            'password' => $password
                        );
                    }

                    $transport = new SmtpTransport();
                    $options = new SmtpOptions(array(
                        'name' => $name,
                        'host' => $host,
                        'port' => $port,
                        'connection_class' => 'login',
                        'connection_config' => $connection_config
                    ));

                    $resolver = new TemplateMapResolver();
                    $resolver->setMap(array(
                        'mailLayout' => __DIR__ . '/../../../../Application/view/application/campana/pago-bancario.phtml'
                    ));

                    $rendered = new PhpRenderer();
                    $rendered->setResolver($resolver);

                    $viewModel = new ViewModel();
                    $viewModel->setTemplate('mailLayout')->setVariables(array(
                        'localhost' => $localhost,
                        'telefono' => $telefono,
                        'operacion' => $idTransaccion,
                        'peciototal' => $datos['PriceTotal']
                    ));

                    $content = $rendered->render($viewModel);

                    $html = new MimePart($content);
                    $html->type = "text/html";

                    $body = new MimeMessage();
                    $body->addPart($html);

                    $message->setBody($body);

                    $transport->setOptions($options);
                    $transport->send($message);
                }

                $this->layout()->pais = $pais;
                $this->layout()->capital = $capital;
                $this->layout()->departamentos = $departamentos;
                $this->layout()->provincias = $provincias;
                $this->layout()->telefono_empresa = $telefono_empresa;

                return  new ViewModel(array('operacion' => $idTransaccion,
                                            'peciototal' => $datos['PriceTotal']));
                
                break;
        }
    }

    public function categoriaAction() {
        $id = base64_decode($this->params()->fromRoute("id", null));
        $op = base64_decode($this->params()->fromRoute("op", null));

        $serviceLocator = $this->getServiceLocator();
        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $config = $serviceLocator->get('config');
                
        $data = $campanaTable->getCampanaCategoria($id,$op);
        
        $moneda = $config['moneda'];
        
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $telefono_empresa = $config['empresa']['telefono'];
        $this->layout()->telefono_empresa = $telefono_empresa;

        return new ViewModel(array('data' => $data, 
                                   'subcategoria' => $op,
                                   'moneda' => $moneda,
            ));
    }

    public function cerrarsessionAction() {

        $user_session = new Container('user');
        $user_session->getManager()->getStorage()->clear('user');

        $data = array();
        return $this->getResponse()->setContent(Json::encode($data));
    }

    public function clienteAction() {

        $usuario = $this->params()->fromPost("email", null);
        $password = $this->params()->fromPost("password", null);
        $tipo = $this->params()->fromPost("tipo", null);
        $fnombre = $this->params()->fromPost("fname", null);
        $lnombre = $this->params()->fromPost("lname", null);
        $sexo = $this->params()->fromPost("sex", null);
        $facebook = $this->params()->fromPost("facebook", null);

        if ($tipo == 'E') {
            $usuario = base64_decode($usuario);
        }

        $serviceLocator = $this->getServiceLocator();
        $clienteTable = $serviceLocator->get('Dashboard\Model\CupclienteTable');
        $datos = $clienteTable->getUsuarioByUser($usuario);

        $data = array();
        foreach ($datos as $dato) {
            $data[] = $dato;
        }

        $user_session = new Container('user');

        if ($facebook == '1') {
            $data[0]['validar'] = '1';

            $user_session->username = $usuario;
            $user_session->nombre = $fnombre;
            $user_session->apellido = $lnombre;
            $user_session->nombres = $fnombre.' '.$lnombre;
            $user_session->genero = $sexo;
            $user_session->facebook = 'S';

            return $this->getResponse()->setContent(Json::encode($data));
        }

        if (count($data) == 0) {
            $data[0]['validar'] = '3';
            $user_session->getManager()->getStorage()->clear('user');
        } else {
            if ($password == null) {
                $data[0]['validar'] = '0';
                $user_session->getManager()->getStorage()->clear('user');
            } else {
                if ($data[0]['password'] == sha1($password)) {
                    $data[0]['validar'] = '1';
                    $data[0]['email'] = base64_encode($data[0]['email_cliente']);
                    /* Guardamos los datos de la session del usuario */
                    $user_session->username = $usuario;
                    $user_session->nombre = $data[0]['nombres'];
                    $user_session->apellido = $data[0]['apellidos'];
                    $user_session->tipdoc = $data[0]['id_tipo_documento'];
                    $user_session->numdoc = $data[0]['numero_documento'];
                    $user_session->telefono = $data[0]['telefono'];
                    $user_session->celular = $data[0]['celular'];
                    $user_session->genero = $data[0]['id_sexo'];
                    $user_session->facebook = 'N';
                } else {
                    $data[0]['validar'] = '2';
                    $user_session->getManager()->getStorage()->clear('user');
                }
            }
        }

        return $this->getResponse()->setContent(Json::encode($data));
    }

    public function pagorequestAction() {
        
        $datos = $this->params()->fromPost();
        
        set_time_limit(0);
        
        $orden = $datos["transaccion"];
        $estado = $datos["estado"];

        $serviceLocator = $this->getServiceLocator();
        
        $config = $serviceLocator->get('Config');
        $localhost = $config['constantes']['localhost'];
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
        $opcion_campana = $cuponTable->updEstadoVenta($orden, $estado);

        if ($estado == '3') {
            $campanaopcionTable = $serviceLocator->get('Dashboard\Model\CupcampanaopcionTable');
            $campanaopcionTable->updCantidadVendidos($opcion_campana['id_campana'], $opcion_campana['id_campana_opcion'], $opcion_campana['cantidad']);
        }
        
        /*Enviamos el correo*/
        $datosCupon = $cuponTable->getCupon($orden);
        $variados = new Variados($serviceLocator);
        $variados->obtenerCuponPdf($datosCupon);
        /********************/

        $url = $localhost."/campana/cuponbuenaso";

        $request = new Request;
        $request->getHeaders()->addHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
        ]);
        $request->setUri($url);
        $request->setMethod('POST'); 
        $request->getPost()->set('orden', $orden);
        $request->getPost()->set('estado', $estado);
        
        $confCurl = array(
            'adapter'   => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => array(CURLOPT_CONNECTTIMEOUT => 0)
        );
        
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $telefono_empresa = $config['empresa']['telefono'];
        $this->layout()->telefono_empresa = $telefono_empresa;
        
        $client = new Client($url, $confCurl);

        $response = $client->dispatch($request);

        return $response;
    }

    public function recuperarAction() {
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        $telefono_empresa = $config['empresa']['telefono'];
        
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $this->layout()->telefono_empresa = $telefono_empresa;
        return new ViewModel();
    }

    public function registrarAction() {
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        $telefono_empresa = $config['empresa']['telefono'];
        
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $this->layout()->telefono_empresa = $telefono_empresa;
        
        $flag = $this->params()->fromPost('id',null);
        return new ViewModel(array('flag' => $flag));
    }

    public function registrarusuarioAction() {
        
        $datos = $this->params()->fromPost();
        
        $serviceLocator = $this->getServiceLocator();
        $clienteTable = $serviceLocator->get('Dashboard\Model\CupclienteTable');
        
        $clienteTable->addCliente($datos);
        
        $user_session = new Container('user');

        $user_session->username = $datos['email'];
        $user_session->nombre = $datos['nombre'];
        $user_session->apellido = $datos['apellido'];
        $user_session->tipdoc = $datos['tipdoc'];
        $user_session->numdoc = $datos['numdoc'];
        $user_session->telefono = $datos['telefono'];
        $user_session->celular = $datos['celular'];
        $user_session->genero = $datos['genero'];
        $user_session->facebook = 'N';
        
        return $this->redirect()->toRoute('home');
    }

    public function cuponbuenasoAction() {
        $datos = $this->params()->fromPost();
        
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        $cuponTable = $serviceLocator->get('Dashboard\Model\CupcuponTable');
        $datosCupon = $cuponTable->getCupon($datos["orden"]);
        $datosArray = $datosCupon[0];
        //$variados = new Variados($serviceLocator);
        //$variados->obtenerCuponPdf($datosArray);
        
        $pais = $config['id_pais'];
        $capital = $config['id_capital'];
        
        $departamentoTable = $serviceLocator->get('Dashboard\Model\UbidepartamentoTable');
        $departamentos = $departamentoTable->getDepartamentosxPaisFavoritos($pais);
        
        $provinciaTable = $serviceLocator->get('Dashboard\Model\UbiprovinciaTable');
        $provincias = $provinciaTable->getProvinciasxDepartamento($pais, $capital);
        
        $this->layout()->pais = $pais;
        $this->layout()->capital = $capital;
        $this->layout()->departamentos = $departamentos;
        $this->layout()->provincias = $provincias;
        
        $telefono_empresa = $config['empresa']['telefono'];
        $this->layout()->telefono_empresa = $telefono_empresa;
        
        return new ViewModel(array('datos' => $datosArray));
        
    }

    public function uploadAction() {

        $edit_campana = new Container('edit_campana');
        $campana = $edit_campana->id;
        
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];
        
        $ruta_int = $dir_image . 
                    $sep_path . 
                    ".." .
                    $sep_path .
                    ".." .
                    $sep_path .
                    "public" .
                    $sep_path .
                    "img" .
                    $sep_path .
                    $campana .
                    $sep_path;
        
        $ruta = $ruta_int .
                "small" .
                $sep_path;
        
        if (!file_exists($ruta_int)) mkdir($ruta_int);
        if (!file_exists($ruta)) mkdir($ruta);

        $uploads = new Http();
        $uploads->setDestination($ruta);
        //$uploads->addFilter('Rename', 'image1.jpg');
        $files = $uploads->getFileInfo();

        foreach ($files as $file => $fileInfo) {
            if ($uploads->isUploaded($file)) {
                if ($uploads->isValid($file)) {
                    if ($uploads->receive($file)) {
                        $info = $uploads->getFileInfo($file);
                        $tmp = $info[$file]['tmp_name'];
                        $data = file_get_contents($tmp);
                        // here $tmp is the location of the uploaded file on the server
                        // var_dump($info); to see all the fields you can use
                        /* $filter = new RenameUpload("./img/45/");
                          $filter->filter($file); */
                        $datos = array();
                    } else {
                        $datos = array('error' => 'No se puedo recibir archivo.');
                    }
                } else {
                    $datos = array('error' => 'Archivo no válido.');
                }
            } else {
                $datos = array('error' => 'Archivo no se puede cargar.');
            }
        }

        return $this->getResponse()->setContent(Json::encode($datos));
    }
    
    function uploaddeleteAction() {
        
        $nombre_file = $this->params()->fromPost("key", null);
        
        $edit_campana = new Container('edit_campana');
        $campana = $edit_campana->id;
        
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];

        $ruta = $dir_image . 
                $sep_path . 
                ".." .
                $sep_path .
                ".." .
                $sep_path .
                "public" .
                $sep_path .
                "img" .
                $sep_path .
                $campana .
                $sep_path .
                "small" .
                $sep_path .
                $nombre_file;
                
        if(unlink($ruta)) {
            $datos = array();
        } else {
            $datos = array('error' => 'No se pudo eliminar archivo.');
        }
        
        return $this->getResponse()->setContent(Json::encode($datos));
        
    }
    
    public function upload2Action() {

        $edit_campana = new Container('edit_campana');
        $campana = $edit_campana->id;
        
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];

        $ruta_int = $dir_image . 
                    $sep_path . 
                    ".." .
                    $sep_path .
                    ".." .
                    $sep_path .
                    "public" .
                    $sep_path .
                    "img" .
                    $sep_path .
                    $campana .
                    $sep_path;
        
        $ruta = $ruta_int .
                "small2" .
                $sep_path;
        
        if (!file_exists($ruta_int)) mkdir($ruta_int);
        if (!file_exists($ruta)) mkdir($ruta);

        $uploads = new Http();
        $uploads->setDestination($ruta);
        $uploads->addFilter('Rename',array('target' => 'image1.jpg'));
        $files = $uploads->getFileInfo();

        foreach ($files as $file => $fileInfo) {
            if ($uploads->isUploaded($file)) {
                if ($uploads->isValid($file)) {
                    if ($uploads->receive($file)) {
                        $info = $uploads->getFileInfo($file);
                        $tmp = $info[$file]['tmp_name'];
                        $data = file_get_contents($tmp);
                        // here $tmp is the location of the uploaded file on the server
                        // var_dump($info); to see all the fields you can use
                        /*$filter = new RenameUpload($tmp);
                        $filter->filter('image1.jpg');*/
                        $datos = array();
                    } else {
                        $datos = array('error' => 'No se puedo recibir archivo.');
                    }
                } else {
                    $datos = array('error' => 'Archivo no válido.');
                }
            } else {
                $datos = array('error' => 'Archivo no se puede cargar.');
            }
        }

        return $this->getResponse()->setContent(Json::encode($datos));
    }
    
    function uploaddelete2Action() {
        
        $nombre_file = $this->params()->fromPost("key", null);
        
        $edit_campana = new Container('edit_campana');
        $campana = $edit_campana->id;
        
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('Config');
        $dir_image = $config['constantes']['dir_image'];
        $sep_path = $config['constantes']['sep_path'];

        $ruta = $dir_image . 
                    $sep_path . 
                    ".." .
                    $sep_path .
                    ".." .
                    $sep_path .
                    "public" .
                    $sep_path .
                    "img" .
                    $sep_path .
                    $campana .
                    $sep_path .
                    "small2" .
                    $sep_path .
                    "image1.jpg";
        
        if(unlink($ruta)) {
            $datos = array();
        } else {
            $datos = array('error' => 'No se pudo eliminar archivo.');
        }
        
        return $this->getResponse()->setContent(Json::encode($datos));
        
    }

    public function previewAction() {
        
        $id = $this->params()->fromPost("id", null);

        $serviceLocator = $this->getServiceLocator();

        $config = $serviceLocator->get('Config');
        
        $dir_image = $config['constantes']['dir_image'];
        $moneda = $config['moneda'];

        $campanaTable = $serviceLocator->get('Dashboard\Model\CupcampanaTable');
        $empresaTable = $serviceLocator->get('Dashboard\Model\GenempresaTable');
        
        $data   = $campanaTable->getCampanaId($id);
        $data_o = $campanaTable->getCampanaOpciones($id);
        $data_p = $campanaTable->getCampanasAllNotId($id);

        $data_e = $empresaTable->getEmpresaByCampana($id);
        
        $viewmodel = new ViewModel(array('data' => $data,
            'data_o' => $data_o,
            'data_p' => $data_p,
            'data_e' => $data_e,
            'id' => $id,
            'dir_image' => $dir_image,
            'moneda' => $moneda,
            ));
        
        $viewmodel->setTerminal(true);

        return $viewmodel;
    }
}
