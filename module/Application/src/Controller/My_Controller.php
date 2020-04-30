<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
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


class My_Controller extends AbstractActionController {

    public $cache = null;

    public function __construct() {

        $auth = new AuthenticationService();
        if ($auth->hasIdentity() == 1 && $_SERVER['REQUEST_URI'] == '/user/login') {
            header('Location: ' . HOST);
            exit();
        } else if ($auth->hasIdentity() != 1 && $_SERVER['REQUEST_URI'] != '/user/login') {
            header('Location: ' . HOST . 'user/login');
            exit();
        }

        $this->cache = StorageFactory::factory([
                    'adapter' => [
                        'name' => 'filesystem',
                        'options' => ['ttl' => 3600],
                    ],
                    'plugins' => [
                        // Don't throw exceptions on cache errors
                        'exception_handler' => [
                            'throw_exceptions' => false
                        ],
                    ],
        ]);
        
        
    }
    
    public function getDistance($lat1, $lon1, $lat2, $lon2, $unit){//lat1/long 1  dia chi vp
       
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
    
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}
