<?php
use API\Api;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

/**
 * YAPI : CLI Config (Doctrine)
 * ----------------------------------------------------------------------
 * Used by Doctrine's Entity Builder.
 * 
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
require_once 'vendor/autoload.php';

// Fire application and get container
$container = (new Api())->getContainer();

// Get entity manager
$em = $container->get('em');

// Return helper set
return ConsoleRunner::createHelperSet($em);
