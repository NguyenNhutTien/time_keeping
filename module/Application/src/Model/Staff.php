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

class Staff {

    private $table = 'staff';

    public function getInfoStaff($user_id) {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('s' => $this->table))
                ->columns(['full_name'])
                ->where(array('s.id' => $user_id));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return $result->current();
    }

}
