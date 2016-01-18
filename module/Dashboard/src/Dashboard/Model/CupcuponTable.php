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
            'email_cliente',
            'id_campana',
            'id_campana_opcion',
            'cantidad',
            'precio_total',
            'fecha_compra' => new Expression("date_format(fecha_compra,'%d-%m-%Y')")
        ))
        ->from('cup_cupon')
        ->join('cup_cupon_detalle', new Expression("cup_cupon.id_cupon = cup_cupon_detalle.id_cupon"),
                array('codigo_cupon'))
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
        ->where(array('cup_cupon.id_cupon' => $orden));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }

    public function addCupon($datos,$sl) {


        if (isset($datos)) {

            $sql = new Sql($this->tableGateway->adapter);

            $insert = $sql->insert('cup_cupon')->values(array(
                'email_cliente' => (isset($datos['email'])) ? $datos['email'] : null,
                'id_campana' => (isset($datos['IdCampana'])) ? base64_decode($datos['IdCampana']) : null,
                'id_campana_opcion' => (isset($datos['IdOpcion'])) ? base64_decode($datos['IdOpcion']) : null,
                'cantidad' => (isset($datos['cantidad'])) ? $datos['cantidad'] : null,
                'precio_unitario' => (isset($datos['PriceUnit'])) ? $datos['PriceUnit'] : null,
                'precio_total' => (isset($datos['PriceTotal'])) ? $datos['PriceTotal'] : null,
                'id_tarjeta' => (isset($datos['metodo'])) ? $datos['metodo'] : null,
                'id_estado_compra' => '1',
                'fecha_registro' => new Expression("NOW()")
            ));
            
            $statement = $sql->prepareStatementForSqlObject($insert);

            $id_cuponera = $statement->execute()->getGeneratedValue();
            
            $cantidad_cupones = $datos['cantidad'];
            
            for($i=0;$i<$cantidad_cupones;$i++) {
                $codigo_cupon = $this->_getCodigoCupon($sl);
                
                $insert = $sql->insert('cup_cupon_detalle')->values(array(
                    'codigo_cupon' => $codigo_cupon,
                    'id_cupon' => $id_cuponera,
                    'precio_unitario' => $datos['PriceUnit'],
                    'id_estado_cupon' => '1',
                    'fecha_cancelacion' => new Expression("NOW()")
                ));
                
                $statement = $sql->prepareStatementForSqlObject($insert);

                $statement->execute();
                
            }

            return $id_cuponera; /* Se inserto Informacion */
        }
    }
    
    public function updDatosPayme($set, $where) {
        
        $rs = $this->tableGateway->update($set, $where);
        
        return $rs;
    }

    public function updEstadoVenta($orden, $estado) {

        $set = array('id_estado_compra' => $estado,
                     'fecha_compra' => new Expression("NOW()")
                    );
        $where = array('id_cupon' => $orden);

        $rs = $this->tableGateway->update($set, $where);

        $sql = new Sql($this->tableGateway->getAdapter());
        
        $update = $sql->update('cup_cupon_detalle');
        $update->set(array('id_estado_cupon' => $estado,
                           'fecha_cancelacion' => new Expression("NOW()")));
        $update->where(array('id_cupon' => $orden));
        
        $sql->prepareStatementForSqlObject($update)->execute();

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
                  str_pad(rand(0, 9999), 4, "0", STR_PAD_LEFT);
                  
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

            $select->columns(array('estado' => 'id_estado_cupon'))
                    ->from('cup_cupon_detalle')
                    ->join('cup_cupon', new
                            Expression("cup_cupon.id_cupon = cup_cupon_detalle.id_cupon"),
                            array())        
                    ->join('cup_campana', new
                            Expression("cup_cupon.id_campana = cup_campana.id_campana and "
                            . "cup_campana.id_empresa = $id_empresa"),
                            array())        
                    ->where(array('cup_cupon_detalle.codigo_cupon' => $cupon));

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
                        ->table('cup_cupon_detalle')
                        ->set(array('id_estado_cupon' => '5',
                                    'fecha_validacion' => $fecha_validacion))
                        ->where(array('codigo_cupon' => $cupon));

                $stmt = $sql->prepareStatementForSqlObject($select);

                $results = $stmt->execute();

            }

            $aValidar[] = array('cupon' => $cupon, 'valido' => $valido, 'tipo' => $tipo);
        }

        return $aValidar;
    }
    
    public function getCuponValidado($id_empresa, $cantidad = 0, $inicio = 0) {

        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();

        $select->columns(array(
            'id_cupon',
            'cantidad' => new Expression("1"),
            'precio_total' => 'precio_unitario',
            'codigo_cupon',
            'fecha_compra' => new Expression("date_format(fecha_compra,'%d-%m-%Y')"),
            'fecha_validacion' => new Expression("date_format(fecha_validacion,'%d-%m-%Y')")
        ))
        ->from('cup_cupon_detalle');
        
        if( $cantidad > 0 ) { 
            $select->limit($cantidad); 
        }
        
        if( $inicio > 0 ) { 
            $select->offset($inicio); 
        }
        
        $select->join('cup_cupon', new Expression("cup_cupon_detalle.id_cupon = cup_cupon.id_cupon "),
                array(
                    'id_campana',
                    'id_campana_opcion'
                ))        
        ->join('cup_campana', new Expression("cup_cupon.id_campana = cup_campana.id_campana and "
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
        ->where(new In('id_estado_cupon', array('5','7')));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCuponesxLiquidar() {

        $fecha = '24/05/2015';
        
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        
        $select->columns(array(
            'fecha_validacion',
            'codigo_cupon', 
            'cantidad' => new Expression("1"), 
            'precio_unitario', 
            'precio_total' => 'precio_unitario'
        ))->from('cup_cupon_detalle')
        ->join('cup_cupon', new Expression("cup_cupon_detalle.id_cupon = cup_cupon.id_cupon "),
                array('id_campana', 
                      'id_campana_opcion'))       
        ->where(array('cup_cupon_detalle.id_estado_cupon' => '5'))
        ->where->addPredicate(new Between('fecha_validacion', new Expression("STR_TO_DATE('".$fecha." 00:00:00','%d/%m/%Y %H:%i:%s')"), new Expression("STR_TO_DATE('".$fecha." 23:59:59','%d/%m/%Y %H:%i:%s')")));

        $select->order(array('cup_cupon.id_campana', 'cup_cupon.id_campana_opcion', 'cup_cupon_detalle.fecha_validacion'));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
        
    }
    
    public function updCupon($set, $where) {
        
        $rs = $this->tableGateway->update($set, $where);
        
        return $rs;
        
    }
    
    public function updCuponDetalle($set, $where) {
        
        $sql = new Sql($this->tableGateway->adapter);
        
        $update = $sql->update('cup_cupon_detalle');
        $update->set($set);
        $update->where($where);
        
        $rs = $sql->prepareStatementForSqlObject($update)->execute();
        
        return $rs;
        
    }
    
    public function getHistorialValidadosEmpresa($id_empresa) {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        $select->columns(array(
            'validados' => new Expression("count(1)"),
            'cantidad_pagados' => new Expression("sum(case when id_estado_cupon = '7' then 1 else 0 end)"),
            'total_pagados' => new Expression("sum(case when id_estado_cupon = '7' then cup_cupon_detalle.precio_unitario else 0 end)")
        ))
        ->from('cup_cupon_detalle')
        ->join('cup_cupon', new Expression("cup_cupon_detalle.id_cupon = cup_cupon.id_cupon "),
                array()) 
        ->join('cup_campana', new Expression("cup_cupon.id_campana = cup_campana.id_campana and "
                                           . "cup_campana.id_empresa = $id_empresa"),
                array())
        ->where(new In('id_estado_cupon', array('5','7')));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getHistorialPagadosEmpresa($id_empresa) {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        $select->columns(array(
            'cantidad_pagados' => new Expression("sum(cup_liquidacion.cantidad_cupones)"),
            'total_pagados' => new Expression("sum(total_liquidacion)")
        ))
        ->from('cup_liquidacion')
        ->join('cup_campana', new Expression("cup_liquidacion.id_campana = cup_campana.id_campana and "
                                           . "cup_campana.id_empresa = $id_empresa"),
                array())
        ->where(new In('estado_liquidacion', array('1')));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getHistorialpordiaEmpresa($id_empresa) {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        $select->columns(array(
            'periodo' => new Expression("DATE_FORMAT(fecha_cancelacion,'%M %Y')"), 
            'dia' => new Expression("DATE_FORMAT(fecha_cancelacion,'%d')"),
            'vendidos' => new Expression("count(1)"),
            'validados' => new Expression("sum(case when id_estado_cupon in ('5','7') then 1 else 0 end)")
        ))
        ->from('cup_cupon_detalle')
        ->join('cup_cupon', new Expression("cup_cupon_detalle.id_cupon = cup_cupon.id_cupon "),
                array()) 
        ->join('cup_campana', new Expression("cup_cupon.id_campana = cup_campana.id_campana and "
                                           . "cup_campana.id_empresa = $id_empresa"),
                array())
        ->where(new In('id_estado_cupon', array('3','5','7')));
        
        $select->group(array(new Expression("DATE_FORMAT(fecha_cancelacion,'%M %Y')")));
        $select->group(array(new Expression("DATE_FORMAT(fecha_cancelacion,'%d')")));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getPagobancarioList() {

        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();

        $select->columns(array(
            'id_cupon',
            'transaccion' => 'id_cupon',
            'id_campana',
            'id_campana_opcion',
            'cantidad',
            'precio_total',
            'fecha_compra' => new Expression("date_format(fecha_compra,'%d-%m-%Y')"),
            'tipo' => new Expression("case when id_estado_compra = '1' then '0' else '1' end"),
            'fec_operacion' => new Expression("date_format(fecha_operacion,'%d-%m-%Y')"),
            'num_operacion' => 'observacion'
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
        ->where(new In('cup_cupon.id_estado_compra', array('1','3')))
        ->where(array('cup_cupon.id_tarjeta' => '999'));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCuponPrepagado($id_cupon) {
    
            $sql = new Sql($this->tableGateway->getAdapter());
    
            $select = $sql->select();

            $select->columns(array('operacion' => 'observacion',
                                   'fecha_operacion' => new Expression("DATE_FORMAT(fecha_operacion,'%d-%m-%Y')")))
                    ->from('cup_cupon')
                    ->where(array('cup_cupon.id_cupon' => $id_cupon))
                    ->where(new In('cup_cupon.id_estado_compra', array('3','5','7')));

            $stmt = $sql->prepareStatementForSqlObject($select);

            $result = $stmt->execute();
            
            return ArrayUtils::iteratorToArray($result);
    
    }
    
    public function getDatosOrden($id_cupon) {
    
            $sql = new Sql($this->tableGateway->getAdapter());
    
            $select = $sql->select();

            $select->columns(array('id_campana', 'id_campana_opcion', 'cantidad'))
                    ->from(array('c' => 'cup_cupon'))
                    ->where(array('c.id_cupon' => $id_cupon));

            $stmt = $sql->prepareStatementForSqlObject($select);

            $results = $stmt->execute();
            
            return ArrayUtils::iteratorToArray($results);
    
    }
    
}
