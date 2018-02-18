<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToMany;
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
     * Roles associated with this permission.
     *
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\UserRole",mappedBy="permissions")
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
        return $this->slug;
    }
    
    /**
     * Returns roles associated with the permission.
     *
     * @param bool $slug_only
     *      If true, returns only the roles' slug
     * @return array
     */
    public function getRoles($slug_only = false)
    {
        $list = array();
        foreach ($this->roles as $role) {
            if (true === $slug_only) {
                $list[] = $role->getSlug();
            } else {
                $list[] = json_decode(json_encode($role), true);
            }
        }
        return $list;
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
     * Assigns a permission to this role.
     *
     * @param UserRole $role
     *      UserRole this permission will be assigned to
     * @return $this
     */
    public function setRole(UserRole $role)
    {
        $this->roles[] = $role;
        return $this;
    }
}
