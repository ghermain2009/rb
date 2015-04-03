<?php

namespace Dashboard\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Stdlib\ArrayUtils;

class GenempresaTable
{
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
    
    public function getEmpresaByCampana($id_campana)
    {
        $sql = new Sql($this->tableGateway->adapter);
                
        $select = $sql->select();
        
        $select->columns(array(
            'id_campana'
        ))
        ->from('cup_campana')
        ->join('gen_empresa', new Expression("cup_campana.id_empresa = gen_empresa.id_empresa"))
        ->where(array('id_campana' => $id_campana));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return ArrayUtils::iteratorToArray($result);
    }
    
    public function getEmpresaList()
    {
        $select = new Select();
        $select->from(array(
                    'c' => 'gen_empresa'
                ))
                /*->join(array(
                    'e' => 'gen_empresa'
                    ), 
                    'c.id_empresa = e.id_empresa'
                 )*/
                ->order('c.id_empresa');
        
        return $select;
    }
    
    public function getEmpresa($userId)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql
                  ->select()
                  ->from(array('c' => 'gen_empresa'))
                  ->where(array('c.id_empresa' => $userId));

        $stmt = $sql->prepareStatementForSqlObject($select);
        
        $results = $stmt->execute(); 
        
        return $results;
    }
    
    public function editEmpresa($set, $where)
    {
      
        $rs = $this->tableGateway->update($set, $where);
        return $rs;
    }
    
    public function addEmpresa($params)
    {
        //$auth = new AuthenticationService();
        //$identity = $auth->getIdentity();
        
        //$params['id_user'] = $identity->id;
       
        $sql = new Sql($this->tableGateway->getAdapter());
        $insert = $sql->insert('gen_empresa')->values($params);
                
        $stmt = $sql->prepareStatementForSqlObject($insert);

        return $stmt->execute()->getGeneratedValue();
    }
}
