<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
  
return [
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 3,
        // Session lưu 30 trên server
        'gc_maxlifetime' => 60 * 60 * 24 * 30,
        'use_cookies' => true,
        'cookie_httponly' => true,
        'name' => 'xtlab'
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class,
    ],
];
