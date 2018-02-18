<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
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
     * Entity name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $name;
    
    /**
     * Entity slug.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $slug;
    
    /**
     * Permissions associated with this role.
     *
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\UserPermission",inversedBy="roles")
     * @JoinTable(name="user_role_permission")
     */
    protected $permissions;
    
    /**
     * Users associated with this role.
     *
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
    
    // GETTERS
    // ------------------------------------------------------------------
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->name;
    }
    
    /**
     * Returns the permissions associated with this role.
     *
     * @param bool $slug_only
     *      If true, returns only the permissions' slug
     * @return array
     */
    public function getPermissions($slug_only = false)
    {
        $list = array();
        foreach ($this->permissions as $permission) {
            if (true === $slug_only) {
                $list[] = $permission->getSlug();
            } else {
                $list[] = json_decode(json_encode($permission), true);
            }
        }
        return $list;
    }
    
    /**
     * Returns the users associated with this role.
     *
     * @return ArrayCollection|Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    // SETTERS
    // ------------------------------------------------------------------

    /**
     * Sets the name.
     * 
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the slug.
     * 
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
        return $this;
    }
    
    /**
     * Assigns a permission to the role.
     *
     * @param UserPermission $permission
     *      UserPermission to assign to this role
     * @return $this
     */
    public function addPermission(UserPermission $permission)
    {
        $this->permissions[] = $permission;
        return $this;
    }
}
