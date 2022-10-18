<?php
namespace YAPI\Entities\Users;

use Doctrine\ORM\Mapping as ORM;
use YAPI\Core\Base\BaseEntity;

class User extends BaseEntity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $email;
}