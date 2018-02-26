<?php
namespace API\Models\Entity\Users;

use API\Core\BaseEntity;
use API\Core\Salt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI : API\Models\Entity\Users\User
 * ----------------------------------------------------------------------
 * User entity.
 * 
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 * 
 * @Entity
 * @Table(name="user")
 * @HasLifeCycleCallbacks
 */
class User extends BaseEntity 
{
    // Properties
    // ------------------------------------------------------------------

    /**
     * Username/login name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $username;

    /**
     * Hashed password
     *
     * @var string
     * @Column(type="string",length=128)
     */
    protected $password;

    /**
     * E-mail address.
     *
     * @var string
     * @Column(type="string",length=255,unique=true)
     */
    protected $email;

    /**
     * Old database ID.
     *
     * @var int
     * @Column(type="integer",nullable=true)
     */
    protected $old_id;

    // Relationships
    // ------------------------------------------------------------------

    /**
     * Attributes assigned to this user.
     *
     * @var Collection
     * @OneToMany(targetEntity="API\Models\Entity\Users\UserAttribute",mappedBy="user")
     */
    protected $attributes;

    /**
     * Groups related to this entity.
     *
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\UserGroup",inversedBy="users")
     * @JoinTable(
     *      name="user_groups_list", 
     *      joinColumns={
     *          @JoinColumn(name="user_id",referencedColumnName="id")
     *      }, 
     *      inverseJoinColumns={
     *          @JoinColumn(name="group_id",referencedColumnName="id")
     *      }
     * )
     */
    protected $groups;

    /**
     * User role.
     *
     * @var UserRole
     * @ManyToOne(targetEntity="API\Models\Entity\Users\UserRole",inversedBy="users")
     * @JoinColumn(name="role_id",referencedColumnName="id")
     */
    protected $role;

    // Constructor
    // ------------------------------------------------------------------

    /**
     * User constructor.
     */
    public function __construct() 
    {
        // Set collections
        $this->attributes = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }
    
    // Getters
    // ------------------------------------------------------------------

    /**
     * Retrieves the username.
     *
     * @return string
     */
    public function getUsername(): string 
    {
        return $this->username;
    }
    
    /**
     * Retrieves the hashed password.
     *
     * @return string
     */
    public function getPassword(): string 
    {
        return $this->password;
    }

    /**
     * Retrieves the e-mail address.
     *
     * @return string
     */
    public function getEmail(): string 
    {
        return $this->email;
    }

    /**
     * Retrieves the old database ID.
     *
     * @return string
     */
    public function getOldId(): int 
    {
        return $this->old_id;
    }

    /**
     * Returns a collection of related attributes.
     *
     * @return Collection
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * Returns a collection of related groups.
     *
     * @return Collection
     */
    public function getGroups(): Collection 
    {
        return $this->groups;
    }

    /**
     * Returns the user role.
     *
     * @return UserRole
     */
    public function getRole(): UserRole 
    {
        return $this->role;
    }

    /**
     * Returns all data for this user as an associative array, for use 
     * with JSON Web Token payloads.
     *
     * @return array
     */
    public function getTokenPayload(): array 
    {
        $data = $this->toArray();
        
        // Fetch all attributes
        $attr = [];
        foreach ($data['attributes'] as $attribute) {
            $attr[$attribute->getName()] = $attribute->getValue();
        }

        // Fetch all groups basic data
        $groups = [];
        foreach ($data['groups'] as $group) {
            $groups[] = [
                'id' => $group->getId(), 
                'name' => $group->getName(), 
                'slug' => $group->getSlug()
            ];
        }

        // Fetch role and permissions
        $role = [
            'name' => $data['role']->getName(), 
            'slug' => $data['role']->getSlug(), 
            'permissions' => []
        ];
        foreach ($this->role->getPermissions() as $permission) {
            $role['permissions'][] = $permission->getSlug();
        }

        // Replace all attributes
        $data['attributes'] = $attr;
        $data['groups'] = $groups;
        $data['role'] = $role;

        // DO NOT RETURN PASSWORD
        unset($data['password']);

        return $data;
    }
    
    // Setters
    // ------------------------------------------------------------------

    /**
     * Sets the username, when trying to set up a new username for a user 
     * that's already registered, throws an error.
     *
     * @param string $username 
     * @return $this
     * @throws \Exception
     */
    public function setUsername(string $username) 
    {
        if ($this->username !== null && $this->username !== '') {
            throw new \Exception('Username cannot be changed', 412);
        }
        $this->username = $username;
        return $this;
    }

    /**
     * Updates the password only if not empty.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password) 
    {
        if (trim($password) !== '') {
            $this->password = \password_hash(
                $password, 
                PASSWORD_DEFAULT
            ).'.'.Salt::get();
        }
        return $this;
    }

    /**
     * Updates the e-mail address.
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email) 
    {
        if ($email !== '' && is_string($email)) {
            $this->email = $email;
        }
        return $this;
    }

    /**
     * Sets the old database ID value.
     *
     * @param integer $old_id
     * @return $this
     */
    public function setOldId(int $old_id) 
    {
        $this->old_id = $old_id;
        return $this;
    }

    /**
     * Sets the user role associated with the user.
     *
     * @param UserRole $role
     * @return void
     */
    public function setRole(UserRole $role) 
    {
        $this->role = $role;
        return $this;
    }

    // Collection Managers
    // ------------------------------------------------------------------

    /**
     * Adds an attribute to this user.
     *
     * @param UserAttribute $attr
     * @return $this
     */
    public function addAttribute(UserAttribute $attr) 
    {
        $this->attributes[] = $attr;
        return $this;
    }

    /**
     * Associate this user with a group.
     *
     * @param UserGroup $group
     * @return $this
     */
    public function addGroup(UserGroup $group) 
    {
        $this->groups[] = $group;
        return $this;
    }
}
