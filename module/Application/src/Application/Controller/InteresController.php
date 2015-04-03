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

class InteresController extends AbstractActionController {
    //put your code here
    public function indexAction(){
        return new ViewModel();
    }
    
    public function comofuncionaAction(){
        return new ViewModel();
    }
    
    public function preguntasfrecuentesAction(){
        return new ViewModel();
    }
    
    public function terminosycondicionesAction(){
        return new ViewModel();
    }
    
    public function contactanosAction(){
        return new ViewModel();
    }
    
    public function politicasdeprivacidadAction(){
        return new ViewModel();
    }
    
    public function mapadesitioAction(){
        return new ViewModel();
    }
    
}
