<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;

/**
 * Description of InteresController
 *
 * @author gtapia
 */
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class ClienteController extends AbstractActionController {

    //put your code here
    public function emailclaveAction() {

        $email = $this->params()->fromPost("email", null);

        $serviceLocator = $this->getServiceLocator();
        $clienteTable = $serviceLocator->get('Dashboard\Model\CupclienteTable');
        $datos = $clienteTable->getUsuarioByUser($email);

        $data = array();
        foreach ($datos as $dato) {
            $data[] = $dato;
        }

        if (count($data) == 0) {
            $data[0]['validar'] = '2';
        } else {

            $nombre = $data[0]['nombres'];
            $token = base64_encode($email);

            $message = new Message();
            $message->addTo($email)
                    ->addFrom('recuperar@rebueno.ec')
                    ->setSubject('Nueva contraseña para Rebueno!‏');

            $transport = new SmtpTransport();
            $options = new SmtpOptions(array(
                'name' => 'smtp.gmail.com',
                'host' => 'smtp.gmail.com',
                'port' => '587',
                'connection_class' => 'login',
                'connection_config' => array(
                    'ssl' => 'tls',
                    'username' => 'ghermain@gmail.com',
                    'password' => 'GENENIOR'
                ),
            ));

            $resolver = new TemplateMapResolver();
            $resolver->setMap(array(
                'mailLayout' => __DIR__ . '/../../../../Application/view/application/cliente/emailclave.phtml'
            ));

            $rendered = new PhpRenderer();
            $rendered->setResolver($resolver);

            $viewModel = new ViewModel();
            $viewModel->setTemplate('mailLayout')->setVariables(array(
                'nombre' => $nombre,
                'token' => $token
            ));

            $content = $rendered->render($viewModel);

            $html = new MimePart($content);
            $html->type = "text/html";

            $body = new MimeMessage();
            $body->addPart($html);

            $message->setBody($body);

            $transport->setOptions($options);
            $transport->send($message);

            $data[0]['validar'] = '1';
        }

        return $this->getResponse()->setContent(Json::encode($data));
    }

    public function recuperarclaveAction() {

        $token = $this->params()->fromQuery("token", null);

        $email = base64_decode($token);

        $serviceLocator = $this->getServiceLocator();
        $clienteTable = $serviceLocator->get('Dashboard\Model\CupclienteTable');
        $datos = $clienteTable->getUsuarioByUser($email);

        $data = array();
        foreach ($datos as $dato) {
            $data[] = $dato;
        }
        //var_dump($data);
        if(count($data) == 0) {
            $email = '';
        } else {
            $email = $data[0]['email_cliente'];
        }
        
        return new ViewModel(array('email' => $email) );
    }
    
    public function cambiarpasswordAction(){
        $email = $this->params()->fromPost('email',null);
        $clave = $this->params()->fromPost('clave',null);
        
        $datos = array('email' => $email,
                       'clave' => $clave);
        
        $serviceLocator = $this->getServiceLocator();
        $clienteTable = $serviceLocator->get('Dashboard\Model\CupclienteTable');
        $clienteTable->setPassword($datos);
        
        $data[0]['validar'] = '1';
        
        return $this->getResponse()->setContent(Json::encode($data));
        
    }

}
