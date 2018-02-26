<?php
namespace API\Config;

/**
 * YAPI : API\Config\Constants
 * ----------------------------------------------------------------------
 * API constants, autoloaded by Composer's `autoload.php`.
 * 
 * @package     API\Config
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */

// API BASE PATHS
// ----------------------------------------------------------------------

/**
 * Root directory.
 * 
 * @var string 
 */
define('API_ROOT', dirname(dirname(dirname(__DIR__))));

/**
 * Source directory.
 * 
 * @var string 
 */
define('API_SOURCE', API_ROOT.'\src');

// API FLAGS
// ----------------------------------------------------------------------

/**
 * Defines development mode on/off.
 * 
 * @var bool
 */
define('API_DEV_MODE', true);
