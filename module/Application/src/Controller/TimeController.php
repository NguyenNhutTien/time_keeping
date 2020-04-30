<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Db\Sql\Sql; //note

class TimeController extends My_Controller {

    public function checkinAction() {
        $success = '';
        $error = '';
        $QTimeGps = new \Application\Model\TimeGps();
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);

        $user = $this->identity();

        $is_check_in = 0;
        if ($QTimeGps->checkCheckIn($user->staff_id, date('Y-m-d'), 1)) {
            $is_check_in = 1;
            $error = 'Bạn đã check in hôm này rồi nhé. Ngày :' . date('Y-m-d');
        }
        if ($this->getRequest()->isPost()) {
            $long = $this->params()->fromPost('long');
            $lat = $this->params()->fromPost('lat');
            $img1 = $this->params()->fromPost('img1');
            $img2 = $this->params()->fromPost('img2');
            if (empty($long) || empty($lat)) {
                $error = 'Open location';
            } else if (empty($img1) || empty($img2)) {
                $error = 'Take enough pictures';
            } else {
                //check kc
                if ($QTimeGps->checkCheckIn($user->staff_id, date('Y-m-d'), 1)) {
                    $error = 'Bạn đã check in hôm này rồi nhé. Ngày :' . date('Y-m-d');
                } else {
                    $result_check = $QTimeGps->checkDis($user->staff_id, $long, $lat);
                    if ($result_check > 2) {
                        $error = 'Exceeds 200m radius';
                    } else {
                        $img1 = str_replace('data:image/jpeg;base64,', '', $img1);
                        $img1 = str_replace(' ', '+', $img1);
                        $data1 = base64_decode($img1);
                        $name1 = "image_1_" . $user->staff_id . "_" . time() . ".png";
                        $file1 = APP_PATH . "/public/photo/pg-check-in/" . $name1;
                        $success1 = file_put_contents($file1, $data1);

                        $img2 = str_replace('data:image/jpeg;base64,', '', $img2);
                        $img2 = str_replace(' ', '+', $img2);
                        $data2 = base64_decode($img2);
                        $name2 = "image_2_" . $user->staff_id . "_" . time() . ".png";
                        $file2 = APP_PATH . "/public/photo/pg-check-in/" . $name2;
                        $success2 = file_put_contents($file2, $data2);

                        if ($success1 && $success2) {
                            try {
                                $data_insert = array(
                                    'staff_id' => $user->staff_id,
                                    'day' => date('Y-m-d'),
                                    'check_at' => date("h:i"),
                                    'long' => $long,
                                    'lat' => $lat,
                                    'img1' => $name1,
                                    'img2' => $name2,
                                    'type' => 1
                                );
                                $insert = $sql->insert('time_gps');
                                $insert->values($data_insert);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $result = $statement->execute();
                                $success = 'Success';
                            } catch (Exception $exc) {
                                $error = 'SQL Fail';
                            }
                        } else
                            $error = 'Upload image fail';
                    }
                }
            }
        }
        $this->layout()->setVariable('title', 'Check In Gps');
        return new ViewModel([
            'is_check_in' => $is_check_in,
            'success' => $success,
            'error' => $error,
        ]);
    }

    public function checkoutAction() {
        $success = '';
        $error = '';
        $QTimeGps = new \Application\Model\TimeGps();
        $db = new \Database\Controller\AdapterController();
        $adapter = $db->DbAdapter();
        $sql = new Sql($adapter);

        $user = $this->identity();

        $is_check_out = 0;//1:da check out , 2 : chuaw check in
        if ($QTimeGps->checkCheckIn($user->staff_id, date('Y-m-d'), 2)) {
            $is_check_out = 1;
            $error = 'Bạn đã check out hôm này rồi nhé. Ngày :' . date('Y-m-d');
        }else if(!$QTimeGps->checkCheckIn($user->staff_id, date('Y-m-d'), 1)){
            $error = 'Bạn chưa check in hôm nay. Vui lòng check in. Ngày :' . date('Y-m-d');
            $is_check_out = 2;
        }
        if ($this->getRequest()->isPost()) {
            $long = $this->params()->fromPost('long');
            $lat = $this->params()->fromPost('lat');
            $img1 = $this->params()->fromPost('img1');
            $img2 = $this->params()->fromPost('img2');
            if (empty($long) || empty($lat)) {
                $error = 'Open location';
            } else if (empty($img1) || empty($img2)) {
                $error = 'Take enough pictures';
            } else {
                //check kc
                if (!$QTimeGps->checkCheckIn($user->staff_id, date('Y-m-d'), 1)) {
                    $error = 'Bạn chưa check in hôm nay. Vui lòng check in. Ngày :' . date('Y-m-d');
                    sleep(2000);
                    header('Location: ' . HOST . 'time/check in');
                    exit();
                } else {
                    if ($QTimeGps->checkCheckIn($user->staff_id, date('Y-m-d'), 2)) {
                        $error = 'Bạn đã check out hôm này rồi nhé. Ngày :' . date('Y-m-d');
                    } else {
                        $result_check = $QTimeGps->checkDis($user->staff_id, $long, $lat);
                        if ($result_check > 2) {
                            $error = 'Exceeds 200m radius';
                        } else {
                            $img1 = str_replace('data:image/jpeg;base64,', '', $img1);
                            $img1 = str_replace(' ', '+', $img1);
                            $data1 = base64_decode($img1);
                            $name1 = "image_1_" . $user->staff_id . "_" . time() . ".png";
                            $file1 = APP_PATH . "/public/photo/pg-check-in/" . $name1;
                            $success1 = file_put_contents($file1, $data1);

                            $img2 = str_replace('data:image/jpeg;base64,', '', $img2);
                            $img2 = str_replace(' ', '+', $img2);
                            $data2 = base64_decode($img2);
                            $name2 = "image_2_" . $user->staff_id . "_" . time() . ".png";
                            $file2 = APP_PATH . "/public/photo/pg-check-in/" . $name2;
                            $success2 = file_put_contents($file2, $data2);

                            if ($success1 && $success2) {
                                try {
                                    $data_insert = array(
                                        'staff_id' => $user->staff_id,
                                        'day' => date('Y-m-d'),
                                        'check_at' => date("h:i"),
                                        'long' => $long,
                                        'lat' => $lat,
                                        'img1' => $name1,
                                        'img2' => $name2,
                                        'type' => 2
                                    );
                                    $insert = $sql->insert('time_gps');
                                    $insert->values($data_insert);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $result = $statement->execute();
                                    $success = 'Success';
                                } catch (Exception $exc) {
                                    $error = 'SQL Fail';
                                }
                            } else
                                $error = 'Upload image fail';
                        }
                    }
                }
            }
        }

        $this->layout()->setVariable('title', 'Check Out Gps');
        return new ViewModel([
            'is_check_out' => $is_check_out,
            'success' => $success,
            'error' => $error,
        ]);
    }

}
