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
/**
 * Description of HoshospedajeTable
 *
 * @author Administrador
 */
class HoshospedajeTable {
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
    
    public function addHospedaje($data) 
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $insert = $sql->insert('hos_hospedaje')->values($data);
                
        $stmt = $sql->prepareStatementForSqlObject($insert);
        
        return $stmt->execute()->getGeneratedValue();
    }
    
    
    public function getHospedaje($idTipo)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select =  $sql
                    ->select()
                    ->from('hos_hospedaje')
                    ->where(array('id_hospedaje' => $idTipo))
                    ->order('id_hospedaje');
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute(); 
        return $results;
    }
    
    public function editHospedaje($set, $where)
    {
        $rs = $this->tableGateway->update($set, $where);
        return $rs;
    }
    
    public function getHospedajeList()
    {
        $select = new Select();
        $select->from(array('a' => 'hos_hospedaje'))
               ->join(array('b' => 'hos_tipo_hospedaje'), 'a.id_tipo = b.id_tipo')
               ->order('a.id_hospedaje');
        
        return $select;
    }
    
    public function deleteHospedaje($idTipo)
    {
        $where = array('id_hospedaje' => $idTipo);
        $rs = $this->tableGateway->delete($where);
        return $rs;
    }
    
    public function getAdicionalesxHospedajeAll($idHospedaje) 
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select =  $sql
                    ->select()
                    ->from(array('a' => 'hos_adicionales'))
                    ->join(array('b' => 'hos_hospedaje_adicionales'), 
                           new Expression('a.id_adicionales = b.id_adicionales and b.id_hospedaje = '.$idHospedaje),
                           array('seleccionado'  => new Expression("IFNULL(b.id_adicionales,0)")) ,
                           'left')
                    ->where(array('id_tipo_adicional' => '1'))
                    ->order('descripcion_adicionales');
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute(); 
        
        return ArrayUtils::iteratorToArray($results);
    }
    
    public function saveAdicionalesHospedaje($adicionales, $id_hospedaje) {
        
        $sql = new Sql($this->tableGateway->adapter);
        $select = $sql->delete();
        $select->from('hos_hospedaje_adicionales')
                ->where(array('id_hospedaje' => $id_hospedaje));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $statement->execute();
        
        foreach( $adicionales as $adicional ) {
            
            $id_adicional = $adicional;
            
            $params = array('id_hospedaje' => $id_hospedaje , 'id_adicionales' => $id_adicional);
            
            $sql = new Sql($this->tableGateway->getAdapter());
            $insert = $sql->insert('hos_hospedaje_adicionales')->values($params);

            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
        }
        
        return;
    }
    
}
