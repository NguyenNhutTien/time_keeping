<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Storage\Session;
use Zend\Session\Storage\SessionStorage;
use Zend\View\Helper\Placeholder\Registry;
use Zend\Db\Sql\Sql; //note
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\Adapter\Apc;
use Zend\Cache\Storage\Plugin\ExceptionHandler;

class UserController extends My_Controller {

    public function loginAction() {
        $message = '';
        //connect db
        $dbAdapter = new \Database\Controller\AdapterController();
        $adapter = $dbAdapter->DbAdapter();

        if ($this->getRequest()->isPost()) {
            $email = $this->params()->fromPost('email');
            $pass = $this->params()->fromPost('pass');
            $authAdapter = new CredentialTreatmentAdapter(
                    $adapter, //Zend\Db\Adapter\Adapter
                    'user', //Tên bảng
                    'email', //Tên cột tương ứng với identity
                    'password'      //Tên cột tương ứng với credential
            );

            //Xác thực
            $authAdapter->setIdentity($email);
            $authAdapter->setCredential(md5($pass));

            $auth = new AuthenticationService();
            $result = $auth->authenticate($authAdapter);

            if ($result->getCode() == 1) {
                $storage = $auth->getStorage();
                $storage->write($authAdapter->getResultRowObject(
                                null,
                                'password'
                ));

                // save cache menu
                $user = $this->identity();

                $db = new \Database\Controller\AdapterController();
                $adapter = $db->DbAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()
                        ->from(array('u' => 'user'))
                        ->join(array('a' => 'access_group'), 'a.id = u.permission_id', array('permission', 'default_page'))
                        ->columns([])
                        ->where(array('u.id' => $user->id));
                $statement = $sql->prepareStatementForSqlObject($select);
                $permission = $statement->execute();

                $select = $sql->select()
                        ->from('menu')
                        ->where('id IN(' . $db->fetch_array($permission)[1]['permission'] . ')')
                        ->order('order_by');
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result2 = $db->fetch_array($result);

                $this->cache->removeItem('menu');
                $this->cache->addItem('menu', json_encode($result2));

                //save cache info
                $QStaff = new \Application\Model\Staff();
                $result_staff = $QStaff->getInfoStaff($user->staff_id);
                $this->cache->removeItem('info_user');
                $this->cache->addItem('info_user', json_encode($result_staff));

                //get link default
                $select = $sql->select()
                        ->from(array('u' => 'user'))
                        ->join(array('a' => 'access_group'), 'a.id = u.permission_id', array('default_page'))
                        ->columns([])
                        ->where(array('u.id' => $user->id));
                $statement = $sql->prepareStatementForSqlObject($select);
                $default_page = $statement->execute();
                if (empty($default_page->current()['default_page'])) {
                    $this->redirect()->toRoute('home');
                } else {
                    header('Location: ' . HOST . $default_page->current()['default_page']);
                    exit();
                }
            } else {
                $message = 'Email hoặc mật khẩu không hợp lệ !';
            }
        }
        $this->layout()->setTemplate('layout/login');
        return new ViewModel([
            'message' => $message
        ]);
    }

    public function logoutAction() {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            ;
        }
        $auth->clearIdentity();
        $this->redirect()->toRoute('user', array('controller' => 'user', 'action' => 'login'));
    }

}
