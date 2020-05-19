<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

define("HOST", "http://local.time-keeping.vn/");
return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'user' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user[/:action]',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'timing' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/timing[/:action]',
                    'defaults' => [
                        'controller' => Controller\TimingController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'my-leave' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/my-leave[/:action]',
                    'defaults' => [
                        'controller' => Controller\MyLeaveController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'time-staff' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/time-staff[/:action]',
                    'defaults' => [
                        'controller' => Controller\TimeStaffController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'leave-staff' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/leave-staff[/:action]',
                    'defaults' => [
                        'controller' => Controller\LeaveStaffController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'list-approve' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/list-approve[/:action]',
                    'defaults' => [
                        'controller' => Controller\ListApproveController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'diligent' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/diligent[/:action]',
                    'defaults' => [
                        'controller' => Controller\DiligentController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'time' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/time[/:action]',
                    'defaults' => [
                        'controller' => Controller\TimeController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'setting-day' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/setting-day[/:action]',
                    'defaults' => [
                        'controller' => Controller\SettingDayController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\TimingController::class => InvokableFactory::class,
            Controller\MyLeaveController::class => InvokableFactory::class,
            Controller\TimeStaffController::class => InvokableFactory::class,
            Controller\LeaveStaffController::class => InvokableFactory::class,
            Controller\ListApproveController::class => InvokableFactory::class,
            Controller\DiligentController::class => InvokableFactory::class,
            Controller\TimeController::class => InvokableFactory::class,
           // Controller\SettingDayController::class => InvokableFactory::class,
            Controller\UserController::class => InvokableFactory::class,
            Controller\ApiController::class => InvokableFactory::class,
        ],
    ],
    'model' => [
        'factories' => [
            Model\TimeGps::class => InvokableFactory::class,
            Model\SettingDay::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
