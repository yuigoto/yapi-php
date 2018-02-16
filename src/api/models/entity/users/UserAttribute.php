<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI/SLIM : API\Models\Entity\Users\UserAttribute
 * ----------------------------------------------------------------------
 * Handles user attributes in an EAV model.
 *
 * Do not register searchable or highly requestes attributes in this table,
 * as it hinders performance!
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 *
 * @Entity
 * @Table(name="user_attribute")
 * @HasLifecycleCallbacks
 */
class UserAttribute extends BaseEntity
{
    /**
     * @var string
     * @Column(type="string",length=128,nullable=false)
     */
    protected $name;
    
    /**
     * @var string
     * @Column(type="string",length=4096)
     */
    protected $value;
    
    /**
     * @var User
     * @ManyToOne(targetEntity="API\Models\Entity\Users\User",inversedBy="attributes")
     * @JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $user;
}
