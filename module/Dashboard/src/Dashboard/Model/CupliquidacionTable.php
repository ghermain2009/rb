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
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Authentication\AuthenticationService;
/**
 * Description of CupliquidacionTable
 *
 * @author gtapia
 */
class CupliquidacionTable {
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
    
    public function getLiquidacionList()
    {
        $select = new Select();
        $select->from(array(
                    'c' => 'cup_liquidacion'
                ))
                ->join(array(
                    'e' => 'cup_campana'
                    ), 
                    'c.id_campana = e.id_campana'
                 )
                ->order('c.id_campana');
        
        return $select;
    }
    
    public function getPreLiquidacion($id_campana) {
        
        $fecha_liquidacion = date('%Y-%m-%d %H:%i:%s');

        $sql = new Sql($this->tableGateway->adapter);

        $insert = $sql->insert('cup_liquidacion')->values(array(
            'fecha_liquidacion' => $fecha_liquidacion,
            'id_campana' => $id_campana,
            'estado_liquidacion' => '1'
        ));

        $statement = $sql->prepareStatementForSqlObject($insert);

        $result = $statement->execute()->getGeneratedValue();

        return $result; /* Se inserto Informacion */
        
    }
}
