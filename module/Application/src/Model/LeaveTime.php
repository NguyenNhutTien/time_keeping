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

class LeaveTime {

    private $table = 'leave_time';

    public function getLeaveTime() {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('l' => $this->table))
                ->columns(['from','to'])
                ->where(array('l.status' => 2));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
}
