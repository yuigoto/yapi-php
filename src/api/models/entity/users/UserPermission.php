<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI/SLIM : API\Models\Entity\Users\UserPermission
 * ----------------------------------------------------------------------
 * Handles individual user permission names.
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 *
 * @Entity
 * @Table(name="user_permission")
 * @HasLifecycleCallbacks
 */
class UserPermission extends BaseEntity
{
    /**
     * Permission name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $name;
    
    /**
     * Permission slug.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $slug;
    
    /**
     * Roles that have this permission associated with.
     *
     * @var Collection
     * @OneToMany(targetEntity="API\Models\Entity\Users\UserRolePermission",mappedBy="permissions")
     */
    protected $roles;
    
    /**
     * UserPermission constructor.
     */
    public function __construct()
    {
        // Parent constructor
        parent::__construct();
        
        // Set attributes
        $this->roles = new ArrayCollection();
    }
}
