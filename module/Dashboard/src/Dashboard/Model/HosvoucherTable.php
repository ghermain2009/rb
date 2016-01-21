<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Authentication\AuthenticationService;
use Zend\Stdlib\ArrayUtils;
use Zend\Db\Sql\Predicate\NotIn;
/**
 * Description of HosvoucherTable
 *
 * @author Administrador
 */
class HosvoucherTable {
    //put your code here
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
     
    public function getTableGateway()
    {
        return $this->tableGateway;
    }
 
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function addVoucher($data) 
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $insert = $sql->insert('hos_voucher')->values($data);
                
        $stmt = $sql->prepareStatementForSqlObject($insert);
        
        return $stmt->execute()->getGeneratedValue();
    }
    
    
    public function getVoucher($idVoucher)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select =  $sql
                    ->select()
                    ->from('hos_voucher')
                    ->where(array('id_voucher' => $idVoucher))
                    ->order('id_voucher');
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute(); 
        return $results;
    }
    
    public function getDatosVoucher($idVoucher)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select =  $sql
                    ->select()
                    ->from('hos_voucher')
                    ->join('hos_hospedaje', 
                            new Expression("hos_voucher.id_hospedaje = hos_hospedaje.id_hospedaje"),
                            array('descripcion_hospedaje' => 'descripcion_hospedaje',
                                  'direccion_hospedaje'  => 'direccion_hospedaje',
                                  'telefono_hospedaje'  => 'telefono_hospedaje'
                    ))
                    ->join('hos_categoria_habitacion', 
                            new Expression("hos_voucher.id_categoria = hos_categoria_habitacion.id_categoria"),
                            array('descripcion_categoria' => 'descripcion_categoria',
                                  'personas_categoria'  => 'personas_categoria'
                    ))
                    ->where(array('id_voucher' => $idVoucher));
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute(); 
        return $results;
    }
    
    public function editVoucher($set, $where)
    {
        $rs = $this->tableGateway->update($set, $where);
        return $rs;
    }
    
    public function getVoucherList()
    {
        $select = new Select();
        $select->from('hos_voucher')
                ->order('id_voucher');
        
        return $select;
    }
    
}
