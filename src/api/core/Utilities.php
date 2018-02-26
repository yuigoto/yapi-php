<?php
namespace API\Core;

/**
 * YAPI : API\Core\Utilities
 * ----------------------------------------------------------------------
 * Provides helper static methods for tasks and other small stuff.
 * 
 * @package     API\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class Utilities 
{
    /**
     * Validates the Brazilian Legal Entity Registry (CNPJ) number.
     *
     * @param string $cnpj 
     *      Number to check
     * @return boolean
     */
    public static function cnpjValidation(string $cnpj): bool 
    {
        if ($cnpj === null || $cnpj === "") return false;

        // Sanitize string
        $cnpj = preg_replace("/\D/", "", $cnpj);

        // Check length
        if (strlen($cnpj) > 14) return false;
        if (strlen($cnpj) < 14) $cnpj = sprintf("%014s", $cnpj);

        // Check repetition
        for ($i = 0; $i < 10; $i++) {
            if (preg_match("/^{$i}{14}$/", $cnpj) !== 0) return false;
        }

        // Validate first digit
        $sum = 0;
        $val = 5;
        for ($n = 0; $n < 12; $n++) {
            $sum += ($cnpj[$n]) * $val;
            $val = ($val - 1 === 1) ? 9 : $val - 1;
        }
        $val = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
        if ((int) $cnpj[12] !== $val) return false;

        // Validate second digit
        $sum = 0;
        $val = 6;
        for ($n = 0; $n < 13; $n++) {
            $sum += ($cnpj[$n]) * $val;
            $val = ($val - 1 === 1) ? 9 : $val - 1;
        }
        $val = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
        if ((int) $cnpj[13] !== $val) return false;

        return true;
    }
    
    /**
     * Validates the Brazilian Natural Person Registry (CPF) number.
     *
     * @param string $cpf 
     *      Number to check
     * @return boolean
     */
    public static function cpfValidation(string $cpf): bool 
    {
        if ($cpf === null || $cpf === "") return false;

        // Sanitize string
        $cpf = preg_replace("/\D/", "", $cpf);

        // Check length
        if (strlen($cpf) > 11) return false;
        if (strlen($cpf) < 11) $cpf = sprintf("%011s", $cpf);

        // Check repetition
        for ($i = 0; $i < 10; $i++) {
            if (preg_match("/^{$i}{11}$/", $cpf) !== 0) return false;
        }

        // Validate first digit
        $sum = 0;
        for ($n = 0; $n < 9; $n++) $sum += $cpf[$n] * (10 - $n);
        $val = 11 - ($sum % 11);
        if ($val === 10 || $val === 11) $val = 0;
        if ((int) $cpf[9] !== $val) return false;

        // Validate second digit
        $sum = 0;
        for ($n = 0; $n < 10; $n++) $sum += $cpf[$n] * (11 - $n);
        $val = 11 - ($sum % 11);
        if ($val === 10 || $val === 11) $val = 0;
        if ((int) $cpf[10] !== $val) return false;

        return true;
    }

    /**
     * Returns the name of the status code provided.
     *
     * @param integer $code
     *      HTTP status code
     * @return string
     */
    public static function httpStatusName(int $code): string 
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
}