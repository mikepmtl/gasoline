<?php

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     1.0-dev
 * @author      Gasoline Development Teams
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

// Add package 'Message' as a core namespace
Autoloader::add_core_namespace('Message');

// And add the classes of the package one by one
Autoloader::add_classes(array(
    'Message\\Container'    => __DIR__ . '/classes/container.php',
    'Message\\Item'         => __DIR__ . '/classes/item.php',
    
    'Message\\Container_Instance'   => __DIR__ . '/classes/container/instance.php',
));
