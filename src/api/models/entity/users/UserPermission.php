<?php
namespace API\Models\Entity\Users;

use API\Core\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI : API\Models\Entity\Users\UserPermission
 * ----------------------------------------------------------------------
 * UserPermission entity.
 * 
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 * 
 * @Entity
 * @Table(name="user_permission")
 * @HasLifecycleCallbacks
 */
class UserPermission extends BaseEntity 
{
    // Properties
    // ------------------------------------------------------------------

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
    
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Roles associated with this permission.
     *
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\UserRole",mappedBy="permissions")
     */
    protected $roles;

    // Constructor
    // ------------------------------------------------------------------

    /**
     * UserRole constructor.
     */
    public function __construct() 
    {
        // Set collections
        $this->roles = new ArrayCollection();
    }
    
    // Getters
    // ------------------------------------------------------------------

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string 
    {
        return $this->name;
    }

    /**
     * Returns the slug.
     *
     * @return string
     */
    public function getSlug(): string 
    {
        return $this->slug;
    }
    
    /**
     * Returns the collection of roles associated with this permission.
     *
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    // Setters
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

    // Collection Managers
    // ------------------------------------------------------------------

    /**
     * Associates a role with this permission.
     *
     * @param UserRole $role
     * @return $this
     */
    public function addRole(UserRole $role) 
    {
        $this->roles[] = $role;
        return $this;
    }
}
