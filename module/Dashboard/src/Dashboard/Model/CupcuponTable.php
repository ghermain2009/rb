<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dashboard\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Between;

/**
 * Description of CupcuponTable
 *
 * @author Administrador
 */
class CupcuponTable {

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

    public function addCupon($datos) {


        if (isset($datos)) {

            $codigo_cupon = $this->getCodigoCupon();

            $sql = new Sql($this->tableGateway->adapter);

            $insert = $sql->insert('cup_cupon')->values(array(
                //'id_cupon' => (isset($datos[''])) ? $datos[''] : null,
                'codigo_cupon' => $codigo_cupon,
                'email_cliente' => (isset($datos['email'])) ? $datos['email'] : null,
                'id_campana' => (isset($datos['IdCampana'])) ? base64_decode($datos['IdCampana']) : null,
                'id_campana_opcion' => (isset($datos['IdOpcion'])) ? base64_decode($datos['IdOpcion']) : null,
                'cantidad' => (isset($datos['cantidad'])) ? $datos['cantidad'] : null,
                'precio_unitario' => (isset($datos['PriceUnit'])) ? $datos['PriceUnit'] : null,
                'precio_total' => (isset($datos['PriceTotal'])) ? $datos['PriceTotal'] : null,
                'id_tarjeta' => (isset($datos['metodo'])) ? $datos['metodo'] : null,
                'id_estado_compra' => '1'
            ));

            //echo $insert->getSqlString();

            $statement = $sql->prepareStatementForSqlObject($insert);

            $result = $statement->execute()->getGeneratedValue();

            return $result; /* Se inserto Informacion */
        }
    }

    public function updEstadoVenta($orden, $estado) {

        $set = array('id_estado_compra' => $estado);
        $where = array('id_cupon' => $orden);

        $rs = $this->tableGateway->update($set, $where);

        $sql = new Sql($this->tableGateway->getAdapter());

        $select = $sql->select();

        $select->columns(array('id_campana', 'id_campana_opcion', 'cantidad'))
                ->from(array('c' => 'cup_cupon'))
                ->where(array('c.id_cupon' => $orden));

        $stmt = $sql->prepareStatementForSqlObject($select);

        $results = $stmt->execute();

        $cantidad = array();
        foreach ($results as $cupon) {
            $cantidad = $cupon;
        }

        return $cantidad;
    }

    private function getCodigoCupon() {
        return '7676767889';
    }

    public function validarCupon($cadena, $tipo) {
        $aCupones = explode(",", $cadena);
        $aValidar = array();
        foreach ($aCupones as $cupon) {

            $sql = new Sql($this->tableGateway->getAdapter());

            $select = $sql->select();

            $select->columns(array('cantidad' => new Expression("count(1)")))
                    ->from(array('c' => 'cup_cupon'))
                    ->where(array('c.codigo_cupon' => $cupon, 'c.id_estado_compra' => '3'));

            $stmt = $sql->prepareStatementForSqlObject($select);

            $results = $stmt->execute();

            $valido = -1;
            foreach ($results as $exito) {
                $valido = $exito['cantidad'];
            }

            if ($valido == 1 && $tipo == 'V') {
                
                $sql = new Sql($this->tableGateway->getAdapter());
                $select = $sql
                        ->update()
                        ->table('cup_cupon')
                        ->set(array('id_estado_compra' => '5'))
                        ->where(array('codigo_cupon' => $cupon));

                $stmt = $sql->prepareStatementForSqlObject($select);

                $results = $stmt->execute();

            }

            $aValidar[] = array('cupon' => $cupon, 'cantidad' => $valido, 'tipo' => $tipo);
        }

        return $aValidar;
    }

}
