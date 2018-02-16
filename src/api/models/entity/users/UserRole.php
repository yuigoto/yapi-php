<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI/SLIM : API\Models\Entity\Users\UserRole
 * ----------------------------------------------------------------------
 * Handles user roles.
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 *
 * @Entity
 * @Table(name="user_role")
 * @HasLifecycleCallbacks
 */
class UserRole extends BaseEntity
{
    /**
     * Role name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $name;
    
    /**
     * Role slug.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $slug;
    
    /**
     * Permissions associated with this role.
     *
     * @var Collection
     * @OneToMany(targetEntity="API\Models\Entity\Users\UserRolePermission",mappedBy="role")
     */
    protected $permissions;
    
    /**
     * @var Collection
     * @OneToMany(targetEntity="API\Models\Entity\Users\User",mappedBy="role")
     */
    protected $users;
    
    /**
     * UserRole constructor.
     */
    public function __construct()
    {
        // Parent constructor
        parent::__construct();
        
        // Set collections
        $this->permissions = new ArrayCollection();
        $this->users = new ArrayCollection();
    }
}
