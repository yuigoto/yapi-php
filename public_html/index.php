<?php
use API\Api;

require_once '../vendor/autoload.php';

/**
 * YAPI : Index
 * ----------------------------------------------------------------------
 * Requires autoload, fires API, runs it, period.
 * 
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
(new Api())->getApp()->run();
// YES, THAT'S ALL
