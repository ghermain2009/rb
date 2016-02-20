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

/**
 * Description of CupcampanaopcionTable
 *
 * @author Administrador
 */
class CupcampanaopcionTable {

    //put your code here
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getTableGateway() {
        return $this->tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function updCantidadVendidos($campana, $opcion, $cantidad) {

        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql
                ->update()
                ->table('cup_campana_opcion')
                ->set(array('vendidos' => new Expression("IFNULL(vendidos,0) + " . $cantidad)))
                ->where(array('id_campana_opcion' => $opcion,
            'id_campana' => $campana));

        $stmt = $sql->prepareStatementForSqlObject($select);

        //echo($stmt->getSql());

        $results = $stmt->execute();

        return $results;
    }

    public function getOpcionxCampanaAll($id_campana) {
        $sql = new Sql($this->tableGateway->adapter);

        $select = $sql->select();

        $select->columns(array(
                    'id_campana_opcion',
                    'id_campana',
                    'descripcion',
                    'precio_regular',
                    'precio_especial',
                    'comision'
                ))
                ->from('cup_campana_opcion')
                ->where(array('id_campana' => $id_campana));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    public function getOpcionxCampanaId($id_opcion, $id_campana) {
        $sql = new Sql($this->tableGateway->adapter);

        $select = $sql->select();

        $select->columns(array(
                    'id_campana_opcion',
                    'id_campana',
                    'descripcion',
                    'precio_regular',
                    'precio_especial',
                    'comision'
                ))
                ->from('cup_campana_opcion')
                ->where(array('id_campana_opcion' => $id_opcion, 'id_campana' => $id_campana));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    public function addOpcionxCampana($datos) {
        
        if(trim($datos["id_campana_opcion"]) == '' ) {

            $sql = new Sql($this->tableGateway->adapter);

            $insert = $sql->insert('cup_campana_opcion')->values(array(
                'id_campana' => (isset($datos['id_campana'])) ? $datos['id_campana'] : null,
                'descripcion' => (isset($datos['descripcion'])) ? $datos['descripcion'] : null,
                'precio_regular' => (isset($datos['precio_regular'])) ? $datos['precio_regular'] : null,
                'precio_especial' => (isset($datos['precio_especial'])) ? $datos['precio_especial'] : null,
                'comision' => (isset($datos['comision'])) ? $datos['comision'] : null,
            ));

            $statement = $sql->prepareStatementForSqlObject($insert);

            $result = $statement->execute()->getGeneratedValue();
            
            $datos = array('resultado' => 'I', 'id' => $result);
            
        } else {
            
            $set = array('descripcion' => (isset($datos['descripcion'])) ? $datos['descripcion'] : null,
                         'precio_regular' => (isset($datos['precio_regular'])) ? $datos['precio_regular'] : null,
                         'precio_especial' => (isset($datos['precio_especial'])) ? $datos['precio_especial'] : null,
                         'comision' => (isset($datos['comision'])) ? $datos['comision'] : null);
            
            $where = array('id_campana_opcion' => (isset($datos['id_campana_opcion'])) ? $datos['id_campana_opcion'] : null,
                           'id_campana' => (isset($datos['id_campana'])) ? $datos['id_campana'] : null);
        
            $result = $this->tableGateway->update($set, $where);
            
            
            
            $datos = array('resultado' => 'U', 'id' => $datos["id_campana_opcion"]);
            
        }
        
        return $datos;
    }

    public function delOpcionxCampanaId($id_opcion, $id_campana) {
        $sql = new Sql($this->tableGateway->adapter);

        $select = $sql->delete('cup_campana_opcion');
        $select->where(array('id_campana_opcion' => $id_opcion, 'id_campana' => $id_campana));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
    
    public function setIncremenarApertura($campana_opcion, $cantidad = 5) {
        
        $sql = new Sql($this->tableGateway->getAdapter());
        
        $update = $sql->update('cup_campana_opcion');
        $update->set(array('apertura' => new Expression("IFNULL(apertura,0) + ".$cantidad)));
        $update->where(array('id_campana' => $campana_opcion));
        
        $sql->prepareStatementForSqlObject($update)->execute();
        
        return true;
    }
}
