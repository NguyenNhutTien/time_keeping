<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\SettingDay;
use Application\Model\SettingDayTable;
use DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Sql; //note
use Zend\View\View;

class SettingDayController extends My_Controller
{
    private $table;

    public function __construct(SettingDayTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        $settingDayList = $this->table->fetchAll();

        $this->layout()->setVariable('title', 'Date Special Setting');
        return new ViewModel([
            'settingDayList' => $settingDayList,
            'messages' =>  $this->flashMessenger()->getMessages()
        ]);
    }

    public function createAction()
    {
        $user = $this->identity();
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $settingDay = new SettingDay;

        if ($this->getRequest()->isPost()) {
            $type = $this->params()->fromPost('type-day');
            $offDay = ($this->params()->fromPost('offDay') != null) ? $this->params()->fromPost('offDay') : 0;
            $isHalfDay =  $this->params()->fromPost('isHalfDay');
            if ($isHalfDay == 0) {
                $fromDay = $this->params()->fromPost('from_day');
                $toDay = $this->params()->fromPost('to_day');
            } else {
                $fromDay = $this->params()->fromPost('aDay');
                $toDay = $this->params()->fromPost('aDay');
                $isHalfDay = $this->params()->fromPost('day-session');
            }
            $note = $this->params()->fromPost('note');
            $fromDay = date("Y-m-d", strtotime($fromDay));
            $toDay = date("Y-m-d", strtotime($toDay));

            if ($fromDay > $toDay) {
                $this->flashMessenger()->addMessage(
                    '<div class="alert alert-danger" role="alert">
                        The from day is bigger than the to day!
                    </div>'
                );
                return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
            }
            
            if ($settingDay->checkDayIsExist($fromDay) || $settingDay->checkDayIsExist($toDay)) {
                $this->flashMessenger()->addMessage(
                    '<div class="alert alert-danger" role="alert">
                        The day already exists. Please choose another day!
                    </div>'
                );
                return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
            }

            try {
                $data_insert = [
                    'type' => $type,
                    'from' => $fromDay,
                    'to' => $toDay,
                    'off_date' => $offDay,
                    'half_day' => $isHalfDay,
                    'note' => $note,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user->staff_id

                ];

                $settingDay = new SettingDay;
                $settingDay->exchangeArray($data_insert);
                $this->table->saveSettingDay($settingDay);

                $this->flashMessenger()->addMessage(
                    '<div class="alert alert-success" role="alert">
                        Add Success!
                    </div>'
                );
            } catch (\Throwable $th) {
                $this->flashMessenger()->addMessage(
                    '<div class="alert alert-danger" role="alert">
                        Add Fail!
                    </div>'
                );
                //    dd($th->getMessage());
            }
        }

        return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
    }

    public function editAction()
    {

        $id = (int) $this->params()->fromQuery('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
        }

        try {
            $settingDay = $this->table->getSettingDay($id);
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(
                '<div class="alert alert-danger" role="alert">
                    Cannot found this settingDay!
                </div>'
            );
            return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
        }


        if (!$this->getRequest()->isPost()) {
            return new ViewModel([
                'settingDay' => $settingDay
            ]);
        }

        $user = $this->identity();
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $type = $this->params()->fromPost('type-day');
        $offDay = ($this->params()->fromPost('offDay') != null) ? $this->params()->fromPost('offDay') : 0;
        $isHalfDay =  $this->params()->fromPost('isHalfDay');
        if ($isHalfDay == 0) {
            $fromDay = $this->params()->fromPost('from_day');
            $toDay = $this->params()->fromPost('to_day');
        } else {
            $fromDay = $this->params()->fromPost('aDay');
            $toDay = $this->params()->fromPost('aDay');
            $isHalfDay = $this->params()->fromPost('day-session');
        }
        $note = $this->params()->fromPost('note');
        $fromDay = date("Y-m-d", strtotime($fromDay));
        $toDay = date("Y-m-d", strtotime($toDay));

        if ($fromDay > $toDay) {
            $this->flashMessenger()->addMessage(
                '<div class="alert alert-danger" role="alert">
                    The from day is bigger than the to day!
                </div>'
            );
            return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
        }

        if ($fromDay != $settingDay->from) {
            if ($settingDay->checkDayIsExist($fromDay)) {
                $this->flashMessenger()->addMessage(
                    '<div class="alert alert-danger" role="alert">
                        The day already exists. Please choose another day!
                    </div>'
                );
                return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
            }
        }

        if ($toDay != $settingDay->to) {
            if ($settingDay->checkDayIsExist($toDay)) {
                $this->flashMessenger()->addMessage(
                    '<div class="alert alert-danger" role="alert">
                        The day already exists. Please choose another day!
                    </div>'
                );
                return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
            }
        }

        try {
            $data_update = [
                'id' => $id,
                'type' => $type,
                'from' => date("Y-m-d", strtotime($fromDay)),
                'to' => date("Y-m-d", strtotime($toDay)),
                'off_date' => $offDay,
                'half_day' => $isHalfDay,
                'note' => $note,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->staff_id
            ];

            $settingDay->exchangeArray($data_update);
            $this->table->saveSettingDay($settingDay);

            $this->flashMessenger()->addMessage(
                '<div class="alert alert-success" role="alert">
                    Update Success!
                </div>'
            );
        } catch (\Throwable $th) {
            $this->flashMessenger()->addMessage(
                '<div class="alert alert-danger" role="alert">
                    Update Fail!
                </div>'
            );
        }

        return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromQuery('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
        }

        try {
            $this->table->getSettingDay($id);
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(
                '<div class="alert alert-danger" role="alert">
                    Cannot found this settingDay!
                </div>'
            );
            return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
        }

        $this->table->deleteSettingDay($id);
        $this->flashMessenger()->addMessage(
            '<div class="alert alert-success" role="alert">
                Delete Success!
            </div>'
        );
        return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
    }

    public function searchAction()
    {
        if ($this->getRequest()->isPost()) {
            $search = $this->params()->fromPost('search');
            $search_format =  date("Y-m-d", strtotime($search));
            $settingDay = new SettingDay;
            $result_arr = $settingDay->searchByMonthYear($search_format);

            $settingDayList = $this->exchangeFromArrayObjectToArraySettingDay($result_arr);

            $this->layout()->setVariable('title', 'Date Special Setting');
            return new ViewModel([
                'search' => $search,
                'settingDayList' => $settingDayList,
                'messages' =>  $this->flashMessenger()->getMessages()
            ]);
        }
        return $this->redirect()->toRoute('setting-day', ['action' => 'index']);
    }

    // $result_arr is a array object , $settingDayList is a array SettingDay
    public function exchangeFromArrayObjectToArraySettingDay($result_arr)
    {
        $dataExchange = array();
        foreach ($result_arr as $result) {
            $dataExchange[] = get_object_vars($result);
        }

        $settingDayList = array();
        foreach ($dataExchange as $de) {
            $sd = new SettingDay;
            $sd->exchangeArray($de);
            array_push($settingDayList, $sd);
        }

        return $settingDayList;
    }
}
