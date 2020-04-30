<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MyLeaveController extends My_Controller {

    public function indexAction() {
        $QLeaveTime = new \Application\Model\LeaveTime();
        $QContract = new \Application\Model\Contract();
        if ($user = $this->identity()) {
            
        } else {
            header('Location: ' . HOST . 'user/login');
            exit();
        }

        $leave_time = $QLeaveTime->getLeaveTime();
        $staff_contract = $QContract->getContractPrevent($user->staff_id);
        $staff_contract_last = $QContract->getContractLast($user->staff_id);

        $this->layout()->setVariable('title', 'My Leave');
        return new ViewModel([
            'leave_time' => $leave_time,
            'staff_contract' => $staff_contract,
            'staff_contract_last' => $staff_contract_last
        ]);
    }

    public function createLeaveAction() {
        $success = '';
        $err = '';
        $QLeaveType = new \Application\Model\LeaveType();
        $QLeaveDetail = new \Application\Model\LeaveDetail();
        echo "<pre>";
        print_r($QLeaveDetail->checkExceedTime(1,'2020-04-23', '2020-04-27', 2));
        echo "</pre>";
        exit();
        //get group leave
        $list_group_leave = $QLeaveType->getGroupLeave();

        if ($this->getRequest()->isPost()) {
            $type = $this->params()->fromPost('type');
            $from_date = $this->params()->fromPost('from_date', null);
            $to_date = $this->params()->fromPost('to_date', null);
            $date_off = $this->params()->fromPost('date_off', null);
            $group_type = $this->params()->fromPost('group_type', 0);
            $leave_type = $this->params()->fromPost('leave_type', 0);
            $reason = $this->params()->fromPost('reson_leave', '');
        }

        $this->layout()->setVariable('title', 'Create Leave');
        return new ViewModel([
            'list_group_leave' => $list_group_leave,
            'error' => $err,
            'success' => $success
        ]);
    }

    public function checkFromToLeaveAction($from , $to , $leave_type) {//1 : trung  2 : max day , 3 day setting // 4 success
        // kiem tra xe co max day khong
        // kiem tra trung
        // kiem tra date setting holiady
        $QLeaveDetail = new \Application\Model\LeaveDetail();
        // kiem tra trung
        $result = $QLeaveDetail->checkDuplicateLeave(1, '2020-04-20', '2020-05-28');
        if ($result) {
            echo 1; exit();
        } else {
            
        }

        echo 4; exit();
    }

    public function loadTypeLeaveAction() {
        $QLeaveType = new \Application\Model\LeaveType();
        if ($this->getRequest()->isPost()) {
            $group_type = $this->params()->fromPost('group_type');
            //get type leave
            $list_type_leave = $QLeaveType->getTypeLeave($group_type);
            echo json_encode($list_type_leave);
            exit();
        }
    }

    public function loadTypeLeaveIdAction() {
        $QLeaveType = new \Application\Model\LeaveType();
        if ($this->getRequest()->isPost()) {
            $leave_type = $this->params()->fromPost('leave_type');
            //get type leave
            $list_type_leave = $QLeaveType->getTypeLeaveById($leave_type);
            echo json_encode($list_type_leave);
            exit();
        }
    }

}
