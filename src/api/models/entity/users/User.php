<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
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
use YAPI\Core\Utilities;

/**
 * YAPI/SLIM : API\Models\Entity\Users\User
 * ----------------------------------------------------------------------
 * User entity.
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 *
 * @Entity
 * @Table(name="user")
 * @HasLifecycleCallbacks
 */
class User extends BaseEntity
{
    /**
     * Entity username/login name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $username;
    
    /**
     * Entity password.
     *
     * @var string
     * @Column(type="string",length=64)
     */
    protected $password;
    
    /**
     * Entity primary e-mail address.
     *
     * @var string
     * @Column(type="string",length=225,unique=true)
     */
    protected $email;
    
    /**
     * Entity attributes.
     *
     * @var Collection
     * @OneToMany(targetEntity="API\Models\Entity\Users\UserAttribute",mappedBy="user")
     */
    protected $attributes;
    
    /**
     * Entity role.
     *
     * @var UserRole
     * @ManyToOne(targetEntity="API\Models\Entity\Users\UserRole",inversedBy="users")
     * @JoinColumn(name="role_id",referencedColumnName="id")
     */
    protected $role;
    
    /**
     * Groups this entity is associated with.
     *
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\UserGroup",inversedBy="users")
     * @JoinTable(name="user_group_list")
     */
    protected $groups;
    
    /**
     * Old ID associated with this entity (from migration).
     *
     * @var int
     * @Column(type="integer",nullable=true)
     */
    protected $old_id;
    
    /**
     * User constructor.
     */
    public function __construct()
    {
        // Parent constructor
        parent::__construct();
        
        // Set attributes
        $this->attributes = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }
    
    // GETTERS
    // ------------------------------------------------------------------
    
    /**
     * Returns the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Returns the password (use it for comparison with an encoded one).
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Returns the e-mail.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Returns the old database ID for a migrated user.
     *
     * @return int
     */
    public function getOldId()
    {
        return $this->old_id;
    }
    
    /**
     * Returns a payload to send to the tokenizer.
     *
     * @return array
     */
    public function getTokenPayload()
    {
        // Build payload
        $payload = [
            'username'      => $this->username,
            'email'         => $this->email,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'role'          => $this->role->getValues(),
            'groups'        => $this->groups,
            'is_deleted'    => $this->deleted,
            'is_public'     => false,
            'display_name'  => null,
            'uuid'          => $this->uuid
        ];
        return $payload;
    }
    
    /**
     * Returns the user's attributes.
     *
     * @return ArrayCollection|Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    // SETTERS
    // ------------------------------------------------------------------
    
    /**
     * Sets the username.
     *
     * Works only when inserting a new user.
     *
     * @param string $username
     *      Sets the entity's username
     * @return $this
     * @throws \Exception
     */
    public function setUsername(string $username)
    {
        if ($this->username !== null && $this->username !== "") {
            throw new \Exception(
                'Username cannot be changed',
                412
            );
        }
        $this->username = $username;
        return $this;
    }
    
    /**
     * Sets the password.
     *
     * @param string $password
     *      New entity password
     * @return $this
     */
    public function setPassword(string $password = '')
    {
        if (trim($password) !== '') {
            $this->password = Utilities::passwordHash($password);
        }
        return $this;
    }
    
    /**
     * Sets the e-mail address.
     *
     * @param string $email
     *      New e-mail address
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
     * Sets the user role.
     *
     * @param UserRole $role
     * @return $this
     */
    public function setRole(UserRole $role) 
    {
        $this->role = $role;
        return $this;
    }
    
    /**
     * Adds an attribute.
     *
     * @param UserAttribute $attr
     *      New attribute
     * @return $this
     */
    public function addAttribute(UserAttribute $attr) 
    {
        $this->attributes[] = $attr;
        return $this;
    }
    
    /**
     * Assigns this user to a group.
     *
     * @param UserGroup $group
     * @return $this
     */
    public function addGroup(UserGroup $group)
    {
        $this->groups[] = $group;
    }
    
    /**
     * Sets the old database ID.
     *
     * @param int $old_id
     *      Old ID
     * @return $this
     */
    public function setOldId(int $old_id)
    {
        $this->old_id = $old_id;
        return $this;
    }
}
