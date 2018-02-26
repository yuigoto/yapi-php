<?php
namespace API\Core;

/**
 * YAPI : API\Core\Salt
 * ----------------------------------------------------------------------
 * Handles security salt. Usually, you'll only get it, that's why it's the 
 * only method available publicly.
 * 
 * In a production environment, you should NEVER change or edit the security 
 * salt file once it's generated and put to use, since it might affect ALL 
 * the saved passwords.
 * 
 * Backups are generated in case you end up deleting. BUT JUST DON'T! ;)
 * 
 * @package     API\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class Salt 
{
    /**
     * Returns the contents of the security salt file.
     * 
     * Also checks if file exists and, if not, creates it.
     *
     * @return string
     */
    public static function get(): string 
    {
        $file = self::file();
        $path = self::path();
        // Check if file exists and, if not, create
        self::test($path, $file);
        // Read and return
        return md5(file_get_contents($path.'\\'.$file));
    }

    /**
     * Checks environment variables for security salt filename.
     *
     * @return string
     */
    private static function file(): string 
    {
        $file = getenv('SALT_FILE');
        if ($file !== '' && $file !== false) return $file;
        return '__SALT';
    }

    /**
     * Resolves the path for the security salt file.
     *
     * @return string
     */
    private static function path(): string 
    {
        $path = (defined('API_ROOT')) 
            ? API_ROOT.'\data' : dirname(dirname(dirname(__DIR__))).'\data';
        if (!is_dir($path)) mkdir($path);
        return $path;
    }

    /**
     * Checks if the security salt file exists and, if not, creates
     * a new one.
     * 
     * @param string $path
     *      Path to the save directory
     * @param string $file 
     *      Security salt file name
     * @return void
     */
    private static function test(string $path, string $file) 
    {
        if (!file_exists($path.'\\'.$file)) {
            // All characters available for randomization
            $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
                  . '0123456789"!@#$%¨&*()_+\'-=`{}^?:><|´[~];/.,\\¹²³£¢¬'
                  . '§ªº°';

            // Will hold the security salt strings
            $list = [];

            // Security salt header
            $list[] = '- SECURITY SALT :: DO NOT EDIT -';

            // Security salt body
            for ($n = 0; $n < 16; $n++) {
                $rand = '';
                for ($i = 0; $i < 32; $i++) {
                    $rand.= $char[rand(0, strlen($char) -1)];
                }
                $list[] = $rand;
            }

            // Security salt footer
            $list[] = md5(sha1(serialize($list)).sha1(date('c')));
            $list[] = '- SECURITY SALT :: DO NOT EDIT -';
            $list = utf8_encode(implode("\r\n", $list));

            // Save and create backups
            file_put_contents($path.'\\'.$file, $list);
            file_put_contents($path.'\\'.$file.'.001', $list);
            file_put_contents($path.'\\'.$file.'.002', $list);
        }
    }
}
