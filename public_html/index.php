<?php
/**
 * YAPI/SLIM : Index
 * ----------------------------------------------------------------------
 * Index file.
 *
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */

// Require Composer autoload
require_once '../vendor/autoload.php';

// Fire Application
$app = (new Api())->get();

// Run!
$app->run();
