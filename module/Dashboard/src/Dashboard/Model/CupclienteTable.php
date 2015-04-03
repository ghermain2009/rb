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
 * Description of CupclienteTable
 *
 * @author Administrador
 */
class CupclienteTable {

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

    public function getUsuarioByUser($email) {

        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql
                ->select()
                ->from(array('c' => 'cup_cliente'))
                ->where(array('c.email_cliente' => $email));

        $stmt = $sql->prepareStatementForSqlObject($select);

        $results = $stmt->execute();

        return $results;
    }

    public function addCliente($datos) {

        if (isset($datos)) {

            $email_cliente = $datos["email"];

            $sql = new Sql($this->tableGateway->adapter);

            $select = $sql->select();

            $select->columns(array(
                        'cantidad' => new Expression("count(1)")
                    ))
                    ->from('cup_cliente')
                    ->where(array('email_cliente' => $email_cliente));

            $statement = $sql->prepareStatementForSqlObject($select);

            $result = $statement->execute();

            $encontrado = 0;
            foreach ($result as $cliente) {
                $encontrado = $cliente['cantidad'];
            }

            if ($encontrado == 0) {

                $insert = $sql->insert('cup_cliente')->values(array(
                    'email_cliente' => (isset($datos['email'])) ? $datos['email'] : null,
                    'id_tipo_documento' => (isset($datos['tipdoc'])) ? $datos['tipdoc'] : null,
                    'numero_documento' => (isset($datos['numdoc'])) ? $datos['numdoc'] : null,
                    'nombres' => (isset($datos['nombre'])) ? $datos['nombre'] : null,
                    'apellidos' => (isset($datos['apellido'])) ? $datos['apellido'] : null,
                    'telefono' => (isset($datos['telefono'])) ? $datos['telefono'] : null,
                    'celular' => (isset($datos['celular'])) ? $datos['celular'] : null,
                    'password' => (isset($datos['clave'])) ? sha1($datos['clave']) : null,
                    'id_sexo' => (isset($datos['genero'])) ? $datos['genero'] : null,
                    'nombres_facebook' => (isset($datos['nombres'])) ? $datos['nombres'] : null,
                ));

                //echo $insert->getSqlString();

                $statement = $sql->prepareStatementForSqlObject($insert);

                $result = $statement->execute();

                return 1; /* Se inserto Informacion */
            } else {
                return 3; /* Se actualiza */
            }

           

            /**/
        } else {
            return 2; /* No hay datos a actualizar */
        }
    }
    
    public function setPassword($datos) {
        
        $set = array('password' => sha1($datos['clave']));
        $where = array('email_cliente' => $datos['email']);
        
        $rs = $this->tableGateway->update($set, $where);
        return $rs;
    }

}
