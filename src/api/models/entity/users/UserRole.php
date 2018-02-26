<?php
namespace API\Models\Entity\Users;

use API\Core\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI : API\Models\Entity\Users\UserRole
 * ----------------------------------------------------------------------
 * UserRole entity.
 * 
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 * 
 * @Entity
 * @Table(name="user_role")
 * @HasLifeCycleCallbacks
 */
class UserRole extends BaseEntity 
{
    // Properties
    // ------------------------------------------------------------------

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
     * Role short description (optional).
     *
     * @var string
     * @Column(type="string",length=255,nullable=true)
     */
    protected $description;
    
    // Relationships
    // ------------------------------------------------------------------

    /**
     * Permissions associated with this role.
     * 
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\UserPermission",inversedBy="Roles")
     * @JoinTable(
     *      name="user_role_permission", 
     *      joinColumns={
     *          @JoinColumn(name="role_id",referencedColumnName="id")
     *      }, 
     *      inverseJoinColumns={
     *          @JoinColumn(name="permission_id",referencedColumnName="id")
     *      }
     * )
     */
    protected $permissions;

    /**
     * Users associated with this role.
     *
     * @var Collection
     * @OneToMany(targetEntity="API\Models\Entity\Users\User",mappedBy="role")
     */
    protected $users;

    // Constructor
    // ------------------------------------------------------------------

    /**
     * UserRole constructor.
     */
    public function __construct() 
    {
        // Set collections
        $this->permissions = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * Returns the description.
     *
     * @return string
     */
    public function getDescription(): string 
    {
        return $this->description;
    }

    /**
     * Returns a collection of permissions associated with this role.
     *
     * @return Collection
     */
    public function getPermissions(): Collection 
    {
        return $this->permissions;
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

    /**
     * Defines the description.
     *
     * @param string $description 
     * @return $this
     */
    public function setDescription(string $description) 
    {
        $this->description = $description;
        return $this;
    }

    // Collection Managers
    // ------------------------------------------------------------------

    /**
     * Adds a permission to this user role.
     *
     * @param UserPermission $permission
     * @return $this
     */
    public function addPermission(UserPermission $permission) 
    {
        $this->permissions[] = $permission;
        return $this;
    }
}
