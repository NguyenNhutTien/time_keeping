<?php

namespace Database\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql; //note

class AdapterController extends AbstractActionController {

    public function DbAdapter() {
        $adapter = new Adapter(array(
            'driver' => 'Mysqli',
            'database' => 'time_keeping',
            'username' => 'root',
            'password' => '',
            'hostname' => 'localhost',
            'charset' => 'utf8'
        ));
        return $adapter;
    }

    public function fetch_array($data) {
        $data_return = array();
        foreach ($data as $key => $item) {
            $data_return[$key] = $item;
        }
        return $data_return;
    }

//    public function indexAction() {
//        $adapter = $this->AdapterDB();
//        $sql = new Sql($adapter);
//        $select = $sql->select()
//                ->from('trang');
//        $statement  = $sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
////        $statement = $adapter->query('SELECT * FROM `trang`');
////        $result = $statement->execute();
//        echo "<pre>";
//        print_r($result->current());
//        echo "</pre>";
//        exit();
//
//    }
}
