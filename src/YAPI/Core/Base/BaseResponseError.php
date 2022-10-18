<?php
namespace YAPI\Core\Base;

class BaseResponseError extends Mappable
{
    protected $code;
    protected $description;
    protected $data = null;

    public function __construct (
        $code,
        $description,
        $data = null
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->data = $data;
    }
}
