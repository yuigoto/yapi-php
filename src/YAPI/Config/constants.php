<?php
namespace YAPI\Config;

/**
 * YAPI\Config\Constants
 * ----------------------------------------------------------------------
 * @package     API\Config
 * @author      Fabio Y. Goto <lab@yuiti.dev>
 * @copyright   2021© Fabio Y. Goto
 * @since       0.0.1
 */

/**
 * Root directory.
 *
 * @var string
 */
define( 'YAPI_ROOT', dirname( dirname( dirname( __DIR__ ) ) ) );

/**
 * Source folder.
 *
 * @var string 
 */
define( 'YAPI_SOURCE', YAPI_ROOT . '\src' );

/**
 * Toggles development mode on/off.
 *
 * @var bool 
 */
define( 'YAPI_DEV_MODE', true );
