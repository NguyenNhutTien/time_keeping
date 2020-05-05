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

class SettingDay
{
    public $id;
    public $type;
    public $from;
    public $to;
    public $off_date;
    public $half_day;
    public $note;
    public $created_by;
    public $created_at;


    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : 0;
        $this->type =  $data['type'];
        $this->from  = $data['from'];
        $this->to  = $data['to'];
        $this->off_date  = $data['off_date'];
        $this->half_day  =  $data['half_day'];
        $this->note  = $data['note'];
        $this->created_by  = $data['created_by'];
        $this->created_at  =  $data['created_at'];
    }


    public function getStaffName($staff_id)
    {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);

        $select = $sql->select()
            ->from(array('l' => 'staff'))
            ->columns(['full_name'])
            ->where(array('l.id' => $staff_id));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $staff_name_arr =  $result->current();
        return $staff_name_arr['full_name'];
    }

    public function checkDayIsExist($day)
    {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();

        $query = sprintf("select * from `setting_date` where `from` <= CAST('%s' AS DATE) and `to` >= CAST('%s' AS DATE)", $day, $day);
        $result = $adapter->query($query, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $result_arr =  $db->fetch_array($result);
        if (count($result_arr) > 0)
            return true;
        return false;
    }

    public function searchByMonthYear($search)
    {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();

        $query = sprintf("select * from `setting_date` where (month(`from`) = month(CAST('%s' AS DATE)) and year(`from`) = year(CAST('%s' AS DATE))) or (month(`to`) = month(CAST('%s' AS DATE)) and year(`to`) = year(CAST('%s' AS DATE)))", $search, $search, $search, $search);
        
        $result = $adapter->query($query, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $result_arr =  $db->fetch_array($result);
        return $result_arr;
    }

}
