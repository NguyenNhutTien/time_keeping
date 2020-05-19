<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
define("APP_PATH", realpath(dirname(__FILE__) . '/../'));
return [
    'Zend\Db',
    'Zend\Log',
    'Zend\ServiceManager\Di',
    'Zend\Mvc\Plugin\FilePrg',
    'Zend\Mvc\Plugin\FlashMessenger',
    'Zend\Mvc\Plugin\Identity',
    'Zend\Authentication\AuthenticationService',
    'Zend\Mvc\Plugin\Prg',
    'Zend\Session',
    'Zend\Mvc\I18n',
    'Zend\Form',
    'Zend\Hydrator',
    'Zend\InputFilter',
    'Zend\Filter',
    'Zend\I18n',
    'Zend\Cache',
    'Zend\Router',
    'Zend\Validator',
    'ZendDeveloperTools',
    'Application',
    'Database',
    
];
