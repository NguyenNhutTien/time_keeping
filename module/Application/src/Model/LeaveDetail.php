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

class LeaveDetail {

    private $table = 'leave_detail';

    public function checkDuplicateLeave($staff_id, $from, $to) {
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('l' => $this->table))
                ->columns(['id'])
                ->where(array('l.staff_id' => $staff_id))
                ->where(array('l.status' => 1))
                ->where("((l.from_date < '" . $from . "' and l.to_date  > '" . $to . "') or (l.from_date > '" . $from . "' and l.to_date > '" . $to . "') or (l.from_date < '" . $from . "' and l.to_date < '" . $to . "'))");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    //check ngay chu nhat
    function isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0);
    }

    public function getArrayDaysSpe($from, $to) { // typeof : string ,, format : '2020-04-26'
        $data = array();

        $date1 = date_create($from);
        $date2 = date_create($to);
        $day_current = $date1;
        //get sunday
        $cout_date_special = 0;
        while ($day_current < $date2) {
            if ($this->isWeekend($day_current->format('Y-m-d'))) {
                $cout_date_special++;
                $data[] = ['data' => $day_current->format('Y-m-d'), 'count' => 1];
            } else {
                $db = new \Database\Controller\AdapterController();
                $adapter = $db->DbAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()
                        ->from(array('sd' => 'setting_date'))
                        ->columns(['count_day_off' => new \Zend\Db\Sql\Expression("SUM(IF(sd.haft_day <> 0,0.5,1))")])
                        ->where("'" . $day_current->format('Y-m-d') . "' BETWEEN sd.`from` AND sd.`to`")
                        ->where('sd.off_date = 1');
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                if (!empty($result->current()['count_day_off'])) {
                    $cout_date_special += $result->current()['count_day_off'];
                    $data[] = ['data' => $day_current->format('Y-m-d'), 'count' => $result->current()['count_day_off']];
                }
            }
            $time = strtotime($day_current->format('Y-m-d'));
            $final = date("Y-m-d", strtotime("+1 day", $time));
            $day_current = date_create($final);
        }


        return [
            'date' => $data,
            'count_date' => $cout_date_special
        ];
    }

    public function checkExceedTime($staff_id, $from, $to, $leave_type) { // 1 max per time ; 2 // max per year //3 : ngay nghi  4 cuccess
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);
        //get type leave
        $QLeaveType = new LeaveType();
        $type_leave = $QLeaveType->getTypeLeaveById($leave_type);

        //check max day
        $date1 = date_create($from);
        $date2 = date_create($to);
        $total_date_leave = date_diff($date1, $date2)->days + 1;
        $special_day = $this->getArrayDaysSpe($from, $to);
        if ($special_day['count_date'] == 0) {
            if ($type_leave['max_day_per_time'] > 0 && $total_date_leave > $type_leave['max_day_per_time']) {
                return 1;
            }

            if ($type_leave['max_day_per_year'] > 0) {
                $select = $sql->select()
                        ->from(array('l' => $this->table))
                        ->columns(['days_leave' => new \Zend\Db\Sql\Expression("SUM(count_day)")])
                        ->where(array('l.staff_id' => $staff_id))
                        ->where(array('l.status' => 1))
                        ->group('staff_id');
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $days_leave = $result->current()['days_leave'];
                if (($days_leave + $total_date_leave) > $type_leave['max_day_per_year']) {
                    return 2;
                }
            }
        } else {

            //check xem phai ngay nghi hay chu nhat hk nhe.
            // check xem co trung voi ngay chu nhat hay ngay le khong. neu trung thi v
            if ($total_date_leave == $special_day['count_date']) {
                return 3;
            } else {
                $total_date_leave = $total_date_leave - $special_day['count_date'];
                if ($type_leave['max_day_per_time'] > 0 && $total_date_leave > $type_leave['max_day_per_time']) {
                    return [
                        'total_date_leave' => $total_date_leave,
                        'date_special' => $special_day['count_date'],
                        'status' => 1
                    ];
                }

                if ($type_leave['max_day_per_year'] > 0) {
                    $select = $sql->select()
                            ->from(array('l' => $this->table))
                            ->columns(['days_leave' => new \Zend\Db\Sql\Expression("SUM(count_day)")])
                            ->where(array('l.staff_id' => $staff_id))
                            ->where(array('l.status' => 1))
                            ->group('staff_id');
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $days_leave = $result->current()['days_leave'];
                    if (($days_leave + $total_date_leave) > $type_leave['max_day_per_year']) {
                        return [
                            'total_date_leave' => $total_date_leave,
                            'date_special' => $special_day['count_date'],
                            'status' => 2
                        ];
                    }
                }
                return [
                    'total_date_leave' => $total_date_leave,
                    'date_special' => $special_day,
                    'status' => 4
                ];
            }
        }
        return 4;
    }

}
