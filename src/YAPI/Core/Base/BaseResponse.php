<?php
namespace YAPI\Core\Base;

class BaseResponse extends Mappable
{
    protected $code;
    protected $result;
    protected $client;
    protected $error = null;

    public function __construct(
        int $code,
        $result,
        bool $client = false
    ) {
        $this->code = $code;
        $this->result = $result;
        $this->client = ($client === true)
            ? (new ClientInformation())->toArray()
            : [];
    }

    public function setError ( BaseResponseError $error ) {
        $this->error = $error;
    }

    public function toArray (): array
    {
        $data = parent::toArray();

        if ( $data[ 'error' ] === null ) {
            unset( $data[ 'error' ] );
        }

        return $data;
    }
}
