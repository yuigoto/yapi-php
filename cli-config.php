<?php
/**
 * YAPI/SLIM : CLI Config (Doctrine)
 * ----------------------------------------------------------------------
 * CLI configuration file for Doctrine's Entity Builder.
 *
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
// Require Composer Autoloader
require_once 'vendor/autoload.php';

// Fire up application and get the container
$container = (new Api())->getContainer();

// Set entityManager
$em = $container->get('em');

// Return helper set
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
