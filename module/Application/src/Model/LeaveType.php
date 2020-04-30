<?php

namespace Application\Model;

use Zend\Mvc\Plugin\Identity;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Storage\Session;
use Zend\Session\Storage\SessionStorage;
use Zend\View\Helper\Placeholder\Registry;
use Zend\Db\Sql\Sql; //note
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\Adapter\Apc;
use Zend\Cache\Storage\Plugin\ExceptionHandler;

class LeaveType {

    private $table = 'leave_type';

    public function getGroupLeave() {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('l' => $this->table))
                ->columns(['id','title'])
                ->where(array('l.parent' => 0))
                ->where(array('l.status' => 1));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $db->fetch_array($result);
    }
    
    public function getTypeLeave($group_leave) {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('l' => $this->table))
                ->columns(['id','title'])
                ->where(array('l.parent' => $group_leave))
                ->where(array('l.status' => 1));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $db->fetch_array($result);
    }
    
    public function getTypeLeaveById($id) {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('l' => $this->table))
                ->columns(['id','max_day_per_time','max_day_per_year','summary'])
                ->where(array('l.id' => $id))
                ->where(array('l.status' => 1));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
}
