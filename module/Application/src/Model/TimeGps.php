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

class TimeGps {

    private $table = 'time_gps';

    public function checkDis($id,$long,$lat) {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('s' => 'staff'))
                ->join(array('o' => 'office'), 'o.id = s.id_office', array('long','lat'))
                ->columns([])
                ->where(array('s.id' => $id));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        $my = new \Application\Controller\My_Controller();
        return $my->getDistance($result->current()['lat'], $result->current()['long'], $lat, $long, 'K');
    }
    
    public function checkCheckIn($id , $date,$type){//$id staff type : 1 : check in : 2 : check out
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('t' => $this->table))
                ->columns(['id'])
                ->where(array('t.staff_id' => $id,'t.day'=>$date , 't.type'=>$type));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        
        return count($db->fetch_array($result)) > 0;
    }

}
