<?php
namespace YAPI\Core;

/**
 * YAPI/SLIM : YAPI\Core\Utilities
 * ----------------------------------------------------------------------
 * Provides helper static methods for small tasks.
 *
 * @package     YAPI\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class Utilities
{
    /**
     * Security salt file name.
     *
     * Once the security salt file is created, DO NOT CHANGE its name and
     * location, at the risk of losing all the stored password or anything
     * that depends on it.
     *
     * @var string
     */
    const SALT_FILE = '__salt';
    
    /**
     * Hashes a password.
     *
     * @param string $password
     * @return string
     */
    public static function passwordHash(string $password)
    {
        // Security salt path
        $path = (!defined('YX_PATH'))
            ? dirname(dirname(dirname(__DIR__))) : YX_PATH;
        
        // Read/define salt
        $salt = self::securitySaltRead($path);
        
        // Return the hashed password
        return md5(sha1($password).sha1($salt));
    }
    
    /**
     * Retrieves the Security Salt.
     *
     * @return string
     */
    public static function securitySaltRetrieve()
    {
        // Security salt path
        $path = (!defined('YX_PATH'))
            ? dirname(dirname(dirname(__DIR__))) : YX_PATH;
        
        // Read/define salt
        $salt = self::securitySaltRead($path);
        return sha1($salt);
    }
    
    /**
     * Returns a HTTP status code name.
     *
     * @param int $code
     * @return mixed|string
     */
    public static function httpStatusCodeName(int $code)
    {
        // HTTP Status Codes
        $list = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        ];
        
        // Return
        return (isset($list[$code])) ? $list[$code] : 'Unknown Error';
    }
    
    // Private Methods
    // ------------------------------------------------------------------
    
    /**
     * Creates a security salt file in `$path`.
     *
     * @param string $path
     */
    private static function securitySaltMake(string $path)
    {
        // File path
        $file =  $path.'/'.self::SALT_FILE;
        
        // Stores the salt strings
        $list = [];
        
        // All possible characters for the salt
        $char = 'abcdefghijklmnopqrstuvwxyz'
                . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                . '0123456789"!@#$%&*()_+\'-='
                . '[]~,.;/\\|<>:?^}`{';
        
        // Address variable
        $addr = getenv('PROJECT_NAME').'|'.getenv('PROJECT_ADDR');
        
        // Salt headers
        $list[] = md5(sha1($addr)).md5(sha1(date('c')));
        
        // Build salt
        for ($n = 0; $n < 8; $n++) {
            $rand = '';
            for ($i = 0; $i < 64; $i++) {
                $rand.= $char[rand(0, strlen($char) - 1)];
            }
            $list[] = $rand;
        }
        
        // Salt footer
        $list[] = md5(sha1($file)).md5(sha1(date('c')));
        
        // Save salt
        file_put_contents($file, implode("\r\n", $list));
    }
    
    /**
     * Reads the security salt file.
     *
     * @param string $path
     * @return string
     */
    private static function securitySaltRead(string $path)
    {
        // Checks existence of the salt file
        self::securitySaltTest($path);
        
        // Read security salt and return
        return file_get_contents($path.'/'.self::SALT_FILE);
    }
    
    /**
     * Checks existence of the security salt file.
     *
     * @param string $path
     */
    private static function securitySaltTest(string $path)
    {
        if (!file_exists($path.'/'.self::SALT_FILE)) {
            // Builds a security salt from the server
            self::securitySaltMake($path);
        }
    }
}
