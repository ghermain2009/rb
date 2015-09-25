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
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        $empresa = $config['empresa'];
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
        return new ViewModel(array('empresa' => $empresa));
    }
    
    public function comofuncionaAction(){
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
    
    public function preguntasfrecuentesAction(){
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        $empresa = $config['empresa'];
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
        return new ViewModel(array('empresa' => $empresa));
    }
    
    public function terminosycondicionesAction(){
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
    
    public function contactanosAction(){
        $serviceLocator = $this->getServiceLocator();
        $config = $serviceLocator->get('config');
        $empresa = $config['empresa'];
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
        return new ViewModel(array('empresa' => $empresa));
    }
    
    public function politicasdeprivacidadAction(){
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
    
    public function mapadesitioAction(){
        $serviceLocator = $this->getServiceLocator();
        $categoriaTable = $serviceLocator->get('Dashboard\Model\GencategoriaTable');
        $categorias = $categoriaTable->fetchAll();
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
        return new ViewModel(array('categorias' => $categorias));
    }
    
}
