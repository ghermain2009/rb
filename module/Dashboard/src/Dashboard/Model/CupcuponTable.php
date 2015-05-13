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
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\Between;
use Zend\Stdlib\ArrayUtils;

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
    
    public function getCupon($orden) {

        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();

        $select->columns(array(
            'id_cupon',
            'codigo_cupon',
            'id_campana',
            'id_campana_opcion',
            'cantidad',
            'precio_total',
            'fecha_compra' => new Expression("date_format(fecha_compra,'%d-%m-%Y')")
        ))
        ->from('cup_cupon')
        ->join('cup_campana', new Expression("cup_cupon.id_campana = cup_campana.id_campana"),
                array(
                   'sobre_campana',
                   'saber' => 'observaciones',
                   'fecha_validez' => new Expression("date_format(fecha_validez,'%d-%m-%Y')")
                ))        
        ->join('cup_campana_opcion', new Expression("cup_cupon.id_campana = cup_campana_opcion.id_campana and "
                                                  . "cup_cupon.id_campana_opcion = cup_campana_opcion.id_campana_opcion"),
                array('campana_descripcion' => 'descripcion'
                    ))
        ->join('gen_empresa', new Expression("cup_campana.id_empresa = gen_empresa.id_empresa"),
                array('razon_social',
                      'ubicacion_gps',
                      'horario',
                      'web_site',
                      'descripcion_empresa' => 'descripcion',
                      'direccion' => new Expression("case when ifnull(direccion_comercial,'') = '' then direccion_facturacion else direccion_comercial end ")
                    ))
        ->where(array('id_cupon' => $orden));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }

    public function addCupon($datos,$sl) {


        if (isset($datos)) {

            $codigo_cupon = $this->_getCodigoCupon($sl);

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

        $set = array('id_estado_compra' => $estado,
                     'fecha_compra' => new Expression("NOW()")
                    );
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

    private function _getCodigoCupon($sl) {
        
        if(!empty($sl)) {
            $config = $sl->get('Config');
            $prefijo = $config['constantes']['prefijo'];
        } else {
            $prefijo = 'OPT';
        }
        
        $codigo = $prefijo .
                  '-' .
                  $this->_desordenarNumero(date('YmdHis')) .
                  '-' .
                  rand(0, 9);
                  
        return $codigo;
    }
    
    private function _desordenarNumero($cadena) {
        $posi = 0;
        $cadenanueva = substr($cadena, $posi + 10, 2) .
                       substr($cadena, $posi , 2) .
                       substr($cadena, $posi + 8, 2) .
                       substr($cadena, $posi + 6, 2) .
                       substr($cadena, $posi + 2, 1) .
                       '-' .
                       substr($cadena, $posi + 3, 1) .
                       substr($cadena, $posi + 12, 2) .
                       substr($cadena, $posi + 4, 2);
        
        return $cadenanueva;
    }
    

    public function validarCupon($id_empresa, $cadena, $tipo) {
        $aCupones = explode(",", $cadena);
        $aValidar = array();
        foreach ($aCupones as $cupon) {

            $sql = new Sql($this->tableGateway->getAdapter());

            $select = $sql->select();

            $select->columns(array('estado' => 'id_estado_compra'))
                    ->from('cup_cupon')
                    ->join('cup_campana', new
                            Expression("cup_cupon.id_campana = cup_campana.id_campana and "
                            . "cup_campana.id_empresa = $id_empresa"),
                            array())        
                    ->where(array('cup_cupon.codigo_cupon' => $cupon));

            $stmt = $sql->prepareStatementForSqlObject($select);

            $results = $stmt->execute();

            $valido = 0;
            foreach ($results as $exito) {
                if($exito['estado'] == '3' ) { $valido = 1; }
                if($exito['estado'] == '5' ) { $valido = 2; }
            }

            if ($valido == 1 && $tipo == 'V') {
                
                $fecha_validacion = date("Y-m-d H:i:s");
                
                $sql = new Sql($this->tableGateway->getAdapter());
                $select = $sql
                        ->update()
                        ->table('cup_cupon')
                        ->set(array('id_estado_compra' => '5',
                                    'fecha_validacion' => $fecha_validacion))
                        ->where(array('codigo_cupon' => $cupon));

                $stmt = $sql->prepareStatementForSqlObject($select);

                $results = $stmt->execute();

            }

            $aValidar[] = array('cupon' => $cupon, 'valido' => $valido, 'tipo' => $tipo);
        }

        return $aValidar;
    }
    
    public function getCuponValidado($id_empresa, $cantidad = 0) {

        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();

        $select->columns(array(
            'id_cupon',
            'codigo_cupon',
            'id_campana',
            'id_campana_opcion',
            'cantidad',
            'precio_total',
            'fecha_compra' => new Expression("date_format(fecha_compra,'%d-%m-%Y')"),
            'fecha_validacion' => new Expression("date_format(fecha_validacion,'%d-%m-%Y')")
        ))
        ->from('cup_cupon');
        
        if( $cantidad > 0 ) { 
            $select->limit($cantidad); 
        }
        
        $select->join('cup_campana', new Expression("cup_cupon.id_campana = cup_campana.id_campana and "
                                           . "cup_campana.id_empresa = $id_empresa"),
                array(
                   'sobre_campana',
                   'saber' => 'observaciones',
                   'fecha_validez' => new Expression("date_format(fecha_validez,'%d-%m-%Y')"),
                    'fecha_inicio' => new Expression("date_format(fecha_inicio,'%d-%m-%Y')")
                ))        
        ->join('cup_campana_opcion', new Expression("cup_cupon.id_campana = cup_campana_opcion.id_campana and "
                                                  . "cup_cupon.id_campana_opcion = cup_campana_opcion.id_campana_opcion"),
                array('campana_descripcion' => 'descripcion'
                    ))
        ->join('gen_empresa', new Expression("cup_campana.id_empresa = gen_empresa.id_empresa"),
                array('razon_social',
                      'ubicacion_gps',
                      'horario',
                      'web_site',
                      'descripcion_empresa' => 'descripcion',
                      'direccion' => new Expression("case when ifnull(direccion_comercial,'') = '' then direccion_facturacion else direccion_comercial end ")
                    ))
        ->where(new In('id_estado_compra', array('5','7')));
        
        
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCuponesxLiquidar() {

        $fecha = '11/05/2015';
        $fechaF = '12/05/2015';
        
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        
        $select->columns(array(
            'id_cupon',
            'id_campana', 
            'id_campana_opcion',
            'fecha_validacion',
            'codigo_cupon', 
            'cantidad', 
            'precio_unitario', 
            'precio_total'
        ))->from('cup_cupon')
        ->where(array('cup_cupon.id_estado_compra' => '5'))
        //->where->addPredicate(new Between('fecha_validacion', new Expression("STR_TO_DATE('".$fecha." 00:00:00','%d/%m/%Y %H:%i:%s')"), new Expression("NOW()")));
        ->where->addPredicate(new Between('fecha_validacion', new Expression("STR_TO_DATE('".$fecha." 00:00:00','%d/%m/%Y %H:%i:%s')"), new Expression("STR_TO_DATE('".$fechaF." 23:59:59','%d/%m/%Y %H:%i:%s')")));

        $select->order(array('cup_cupon.id_campana', 'cup_cupon.id_campana_opcion', 'cup_cupon.fecha_validacion'));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
        
    }
    
    public function updCupon($set, $where) {
        
        $rs = $this->tableGateway->update($set, $where);
        
        return $rs;
        
    }
    
    public function getHistorialEmpresa($id_empresa) {

        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();

        $select->columns(array(
            'validados' => new Expression("count(1)"),
            'cantidad_pagados' => new Expression("sum(case when id_estado_compra = '7' then 1 else 0 end)"),
            'total_pagados' => new Expression("sum(case when id_estado_compra = '7' then precio_total else 0 end)")
        ))
        ->from('cup_cupon')
        ->join('cup_campana', new Expression("cup_cupon.id_campana = cup_campana.id_campana and "
                                           . "cup_campana.id_empresa = $id_empresa"),
                array())
        ->where(new In('id_estado_compra', array('5','7')));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
}
