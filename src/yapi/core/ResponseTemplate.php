<?php
namespace YAPI\Core;

/**
 * YAPI/SLIM : YAPI\Core\ResponseTemplate
 * ----------------------------------------------------------------------
 * ResponseTemplate object.
 *
 * @package     YAPI\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class ResponseTemplate implements \JsonSerializable
{
    /**
     * Response status indicator. Should be either "SUCCESS" or "ERROR".
     *
     * If `$status` is ERROR, but no error attribute is present on the payload
     * in `$result`, then an unknown error (\Core\ResponseError) is added to
     * the payload.
     *
     * @var string
     */
    public $status;
    
    /**
     * Response data payload, should be an associative array where the keys
     * specify each item in the payload to be returned.
     *
     * @var array
     */
    public $result;
    
    /**
     * Message string. Used mainly for debugging or error checking, the value
     * returned in this parameter is usually empty.
     *
     * @var string
     */
    public $message;
    
    /**
     * ISO date string, indicates the time the response was created.
     *
     * The value of this parameter should be defined internally.
     *
     * @var string
     */
    public $date;
    
    /**
     * User client-related information.
     *
     * @var ClientInformation
     */
    public $client;
    
    /**
     * ResponseTemplate constructor.
     *
     * @param string $status
     * @param array $result
     * @param string $message
     */
    public function __construct(
        string $status,
        array $result,
        string $message = ""
    ) {
        // Set initial response data
        $this->status   = $this->validateStatus(trim($status));
        $this->result   = $result;
        $this->message  = $message;
        $this->date     = date('c');
        $this->client   = new ClientInformation();
        
        // Checks error
        $this->validateErrorStatus();
    }
    
    /**
     * Overwrites/sets the message.
     *
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }
    
    /**
     * Overwrites/sets the result payload.
     *
     * @param mixed $result
     */
    public function setResult(mixed $result)
    {
        $this->result = $result;
    }
    
    /**
     * Overwrites/sets status string.
     *
     * @param bool $is_error
     */
    public function setStatusType(bool $is_error)
    {
        $this->status = (false === $is_error) ? "ERROR" : "SUCCESS";
    }
    
    /**
     * Validates the error status and the payload.
     *
     * If the payload doesn't contain an instance of `\YX\Core\ResponseError`,
     * then one is created and added to the result.
     */
    private function validateErrorStatus()
    {
        if ($this->status === 'ERROR') {
            // No error?
            if (
                !isset($this->result['error'])
                || !($this->result['error'] instanceof ResponseError)
            ) {
                // Define error
                $this->result['error'] = new ResponseError(
                    418,
                    "Unknown Error",
                    "There was an error while processing your request.",
                    [
                        'info' => 'I\'m a Teapot!',
                        'more' => 'Have some with me! C|_|'
                    ]
                );
            }
        }
    }
    
    /**
     * Validates status indicator.
     *
     * @param string $status
     * @return string
     */
    private function validateStatus($status)
    {
        if ($status !== 'SUCCESS' && $status !== 'ERROR') return 'ERROR';
        return $status;
    }
    
    /**
     * Specifies which entity's data should be serialized to JSON.
     *
     * In this case, all of the object's properties.
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        // Get all object vars
        $list = get_object_vars($this);
        
        // Remove generated vars from Doctrine
        foreach ($list as $k => $v) {
            if (preg_match("/^\_\_/", $k)) unset($list[$k]);
        }
        
        // Return it
        return $list;
    }
}
