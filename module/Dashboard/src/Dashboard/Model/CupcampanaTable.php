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
use Zend\Db\Sql\Predicate\Between;
/**
 * Description of CupcampanaTable
 *
 * @author Administrador
 */
class CupcampanaTable {
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
    
    public function getCampanasAll()
    {
        $sql = new Sql($this->tableGateway->adapter);
                
                
        $select = $sql->select();

        $select->columns(array(
            'categoria' => new Expression("'Lo más nuevo'"),
            'id_campana',
            'subtitulo',
            'maximo_cupones' => new Expression("IFNULL(cantidad_cupones,0)"),
            'mostrar' => new Expression("case when IFNULL(tiempo_online,0) >= 0 and IFNULL(tiempo_offline,0) > 0 then CASE WHEN MOD(ROUND(TIME_TO_SEC(TIMEDIFF(NOW(),STR_TO_DATE(CONCAT(DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),' ',hora_inicio),'%d/%m/%Y %H:%i:%s'))) / 3600),tiempo_online + tiempo_offline) > tiempo_online THEN 0 ELSE 1 END else 1 end"),
        ))
        ->from('cup_campana')
        ->join('cup_campana_opcion', new Expression("cup_campana.id_campana = cup_campana_opcion.id_campana"),
                array('precio_regular' => new Expression("MIN(precio_regular)") ,
                      'precio_especial'  => new Expression("MIN(precio_especial)") ,
                      'vendidos'  => new Expression("SUM(IFNULL(vendidos,0))") ,
                      'descuento'  => new Expression("100-ROUND(MIN(precio_especial)*100/MIN(precio_regular))") ,
                    ))
        ->where("NOW() >= CONCAT(DATE_FORMAT(cup_campana.fecha_inicio,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_inicio,'%H:%i:%s'))")
        ->where("NOW() <= CONCAT(DATE_FORMAT(cup_campana.fecha_final,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_final,'%H:%i:%s'))");
        
        $select->group(array('cup_campana.id_campana'));
        $select->group(array('cup_campana.subtitulo'));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCampanasAllNotId($id_campana)
    {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();

        $select->columns(array(
            'categoria' => new Expression("'Lo más nuevo'"),
            'id_campana',
            'subtitulo',
            'maximo_cupones' => new Expression("IFNULL(cantidad_cupones,0)"),
            'mostrar' => new Expression("case when IFNULL(tiempo_online,0) >= 0 and IFNULL(tiempo_offline,0) > 0 then CASE WHEN MOD(ROUND(TIME_TO_SEC(TIMEDIFF(NOW(),STR_TO_DATE(CONCAT(DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),' ',hora_inicio),'%d/%m/%Y %H:%i:%s'))) / 3600),tiempo_online + tiempo_offline) > tiempo_online THEN 0 ELSE 1 END else 1 end"),
        ))
        ->from('cup_campana')
        ->join('cup_campana_opcion', new Expression("cup_campana.id_campana = cup_campana_opcion.id_campana"),
                array('precio_regular' => new Expression("MIN(precio_regular)") ,
                      'precio_especial'  => new Expression("MIN(precio_especial)") ,
                      'vendidos'  => new Expression("SUM(IFNULL(vendidos,0))") ,
                      'descuento'  => new Expression("100-ROUND(MIN(precio_especial)*100/MIN(precio_regular))") ,
                    ));
      
        $select->where("NOW() >= CONCAT(DATE_FORMAT(cup_campana.fecha_inicio,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_inicio,'%H:%i:%s'))");
        $select->where("NOW() <= CONCAT(DATE_FORMAT(cup_campana.fecha_final,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_final,'%H:%i:%s'))");
        $select->where->notIn('cup_campana.id_campana', array($id_campana)) ;
        $select->group(array('cup_campana.id_campana'));
        $select->group(array('cup_campana.subtitulo'));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCampanaGrupo()
    {
        $sql = new Sql($this->tableGateway->adapter);
                
                
        $select = $sql->select();

        $select->columns(array(
            'id_campana',
            'subtitulo',
            'maximo_cupones' => new Expression("IFNULL(cantidad_cupones,0)"),
            'mostrar' => new Expression("case when IFNULL(tiempo_online,0) >= 0 and IFNULL(tiempo_offline,0) > 0 then CASE WHEN MOD(ROUND(TIME_TO_SEC(TIMEDIFF(NOW(),STR_TO_DATE(CONCAT(DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),' ',hora_inicio),'%d/%m/%Y %H:%i:%s'))) / 3600),tiempo_online + tiempo_offline) > tiempo_online THEN 0 ELSE 1 END else 1 end"),
        ))
        ->from('cup_campana')
        ->join('cup_campana_opcion', new Expression("cup_campana.id_campana = cup_campana_opcion.id_campana"),
                array('precio_regular' => new Expression("MIN(precio_regular)") ,
                      'precio_especial'  => new Expression("MIN(precio_especial)") ,
                      'vendidos'  => new Expression("SUM(IFNULL(vendidos,0))") ,
                      'descuento'  => new Expression("100-ROUND(MIN(precio_especial)*100/MIN(precio_regular))") ,
                    ))
        ->join('cup_campana_categoria', new Expression("cup_campana.id_campana = cup_campana_categoria.id_campana"), array())
        ->join('gen_sub_categoria', new Expression("cup_campana_categoria.id_sub_categoria = gen_sub_categoria.id_sub_categoria"),array())
        ->join('gen_categoria', new Expression("gen_sub_categoria.id_categoria = gen_categoria.id_categoria"),array('categoria' => 'descripcion','id_categoria'));
        $select->where("NOW() >= CONCAT(DATE_FORMAT(cup_campana.fecha_inicio,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_inicio,'%H:%i:%s'))");
        $select->where("NOW() <= CONCAT(DATE_FORMAT(cup_campana.fecha_final,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_final,'%H:%i:%s'))");
        $select->group(array('gen_categoria.descripcion'));
        $select->group(array('gen_categoria.id_categoria'));
        $select->group(array('cup_campana.id_campana'));
        $select->group(array('cup_campana.subtitulo'));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCampanaCategoria($id_categoria, $id_subcategoria)
    {
        $sql = new Sql($this->tableGateway->adapter);
                
                
        $select = $sql->select();

        $select->columns(array(
            'id_campana',
            'subtitulo',
            'maximo_cupones' => new Expression("IFNULL(cantidad_cupones,0)"),
            'mostrar' => new Expression("case when IFNULL(tiempo_online,0) >= 0 and IFNULL(tiempo_offline,0) > 0 then CASE WHEN MOD(ROUND(TIME_TO_SEC(TIMEDIFF(NOW(),STR_TO_DATE(CONCAT(DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),' ',hora_inicio),'%d/%m/%Y %H:%i:%s'))) / 3600),tiempo_online + tiempo_offline) > tiempo_online THEN 0 ELSE 1 END else 1 end"),
        ))
        ->from('cup_campana')
        ->join('cup_campana_opcion', new Expression("cup_campana.id_campana = cup_campana_opcion.id_campana"),
                array('precio_regular' => new Expression("MIN(precio_regular)") ,
                      'precio_especial'  => new Expression("MIN(precio_especial)") ,
                      'vendidos'  => new Expression("SUM(IFNULL(vendidos,0))") ,
                      'descuento'  => new Expression("100-ROUND(MIN(precio_especial)*100/MIN(precio_regular))") ,
                    ))
        ->join('cup_campana_categoria', new Expression("cup_campana.id_campana = cup_campana_categoria.id_campana"), array())
        ->join('gen_sub_categoria', new Expression("cup_campana_categoria.id_sub_categoria = gen_sub_categoria.id_sub_categoria"),array('subcategoria' => 'descripcion','id_sub_categoria'))
        ->join('gen_categoria', new Expression("gen_sub_categoria.id_categoria = gen_categoria.id_categoria"),array('categoria' => 'descripcion','id_categoria'));
        if( $id_subcategoria != null ) {
            $select->where(array('gen_categoria.id_categoria' => $id_categoria, 'cup_campana_categoria.id_sub_categoria' => $id_subcategoria));
        } else {
            if( $id_categoria != null ) {
                $select->where(array('gen_categoria.id_categoria' => $id_categoria));
            }
        }
        $select->where("NOW() >= CONCAT(DATE_FORMAT(cup_campana.fecha_inicio,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_inicio,'%H:%i:%s'))");
        $select->where("NOW() <= CONCAT(DATE_FORMAT(cup_campana.fecha_final,'%Y-%m-%d'),' ',TIME_FORMAT(cup_campana.hora_final,'%H:%i:%s'))");

        $select->group(array('gen_categoria.descripcion'));
        $select->group(array('gen_categoria.id_categoria'));
        $select->group(array('cup_campana.id_campana'));
        $select->group(array('cup_campana.subtitulo'));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCampanaId($id_campana)
    {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        
        $select->columns(array(
            'id_campana',
            'titulo',
            'subtitulo',
            'descripcion',
            'sobre_campana',
            'observaciones',
            'fecha_final' => new Expression("DATE_FORMAT(ADDTIME(fecha_final, hora_final),'%m/%d/%Y %l:%i %p')")
        ))
        ->from('cup_campana')
        ->where(array('id_campana' => $id_campana));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCampanaOpciones($id_campana)
    {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        
        $select->columns(array(
            'id_campana',
            'id_campana_opcion',
            'descripcion',
            'precio_regular',
            'precio_especial',
            'vendidos' => new Expression("IFNULL(vendidos,0)"),
            'ahorro' => new Expression("precio_regular - precio_especial"),
            'descuento' => new Expression("100-ROUND(precio_especial*100/precio_regular)")
        ))
        ->from('cup_campana_opcion')
        ->where(array('id_campana' => $id_campana));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCampanaOpcionId($id_opcion)
    {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        
        $select->columns(array(
            'id_campana',
            'id_campana_opcion',
            'descripcion',
            'precio_regular',
            'precio_especial',
            'vendidos' => new Expression("IFNULL(vendidos,0)"),
            'ahorro' => new Expression("precio_regular - precio_especial"),
            'descuento'  => new Expression("100-ROUND(precio_especial*100/precio_regular)")
        ))
        ->from('cup_campana_opcion')
        ->where(array('id_campana_opcion' => $id_opcion));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getCampanaList()
    {
        $select = new Select();
        $select->from(array(
                    'c' => 'cup_campana'
                ))
                ->join(array(
                    'e' => 'gen_empresa'
                    ), 
                    'c.id_empresa = e.id_empresa'
                 )
                ->order('c.id_campana');
        
        return $select;
    }
    
    public function editCampana($set, $where)
    {
      
        $rs = $this->tableGateway->update($set, $where);
        return $rs;
    }
    
    public function addCampana($params)
    {
        $auth = new AuthenticationService();
        $identity = $auth->getIdentity();
        
        $params['id_user'] = $identity->id;
        //$rs = $this->tableGateway->insert($params);
        
        $sql = new Sql($this->tableGateway->getAdapter());
        $insert = $sql->insert('cup_campana')->values($params);
                
        $stmt = $sql->prepareStatementForSqlObject($insert);

        return $stmt->execute()->getGeneratedValue();
    }
    
    public function getCampana($id)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql
                  ->select()
                  ->from(array('c' => 'cup_campana'))
                  ->where(array('c.id_campana' => $id));

        $stmt = $sql->prepareStatementForSqlObject($select);
        
        $results = $stmt->execute(); 
        
        return $results;
    }
    
    public function getMenu() {
                        
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        
        $select->columns(array(
                    'categoria' => 'descripcion',
                    'id_categoria',
                    'cantidad' => new Expression("count(1)")
                ))
                ->from('gen_categoria')
                ->join('gen_sub_categoria', new Expression("gen_categoria.id_categoria = gen_sub_categoria.id_categoria"), 
                        array('subcategoria' => 'descripcion', 
                              'id_sub_categoria')
                )
                ->join('cup_campana_categoria', new Expression("gen_sub_categoria.id_sub_categoria = cup_campana_categoria.id_sub_categoria"),array())
                ->join('cup_campana', new Expression("cup_campana_categoria.id_campana = cup_campana.id_campana AND NOW() BETWEEN ADDTIME(cup_campana.fecha_inicio, cup_campana.hora_inicio) AND ADDTIME(cup_campana.fecha_final, cup_campana.hora_final)"),array());
        
        $select->group(array('gen_categoria.descripcion','gen_categoria.id_categoria','gen_sub_categoria.descripcion','gen_sub_categoria.id_sub_categoria'));

        //echo $select->getSqlString();
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute(); 
        return $results;

    }
    
    /*public function getCampanaActiva($id_empresa)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select()
                  ->from('cup_campana')
                  ->where(array('cup_campana.id_empresa' => $id_empresa))
                  ->where(new Expression("NOW() <= cup_campana.fecha_final"));

        $stmt = $sql->prepareStatementForSqlObject($select);
        
        $results = $stmt->execute(); 
        
        return $results;
    }*/
    
    
    public function getCampanaActiva($id_empresa)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        
        $select->columns(array(
                    'id_campana',
                    'fecha_inicio' => new Expression("date_format(fecha_inicio,'%d-%m-%Y')"),
                    'fecha_final' => new Expression("date_format(fecha_final,'%d-%m-%Y')"),
                    'descripcion' => 'titulo',
                    'vendidos' => new Expression("count(1)"),
                    'validados' => new Expression("sum(case when cup_cupon_detalle.id_estado_cupon in ('5','7') then 1 else 0 end)"), 
                    'pagados' => new Expression("sum(case when cup_cupon_detalle.id_estado_cupon in ('7') then 1 else 0 end)")
                ))
                  ->from('cup_campana')
                  ->join('cup_cupon', new Expression("cup_campana.id_campana = cup_cupon.id_campana and cup_cupon.id_estado_compra in ('3')"), 
                            array(),'left')
                  ->join('cup_cupon_detalle', new Expression("cup_cupon.id_cupon = cup_cupon_detalle.id_cupon and cup_cupon_detalle.id_estado_cupon in ('3','5','7')"), 
                            array(),'left')
                  ->where(array('cup_campana.id_empresa' => $id_empresa))
                  ->where(new Expression("NOW() <= cup_campana.fecha_final"));
       
        $select->group(array('cup_campana.id_campana'));
        $select->group(array('cup_campana.fecha_inicio'));
        $select->group(array('cup_campana.fecha_final'));
        $select->group(array('cup_campana.descripcion'));
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        
        $results = $stmt->execute(); 
        
        return ArrayUtils::iteratorToArray($results);
    }
    
}
