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
     * User login name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $username;
    
    /**
     * User password.
     *
     * @var string
     * @Column(type="string",length=64)
     */
    protected $password;
    
    /**
     * User e-mail address.
     *
     * @var string
     * @Column(type="string",length=225,unique=true)
     */
    protected $email;
    
    /**
     * Attributes assigned to this user.
     *
     * @var Collection
     * @OneToMany(targetEntity="API\Models\Entity\Users\UserAttribute",mappedBy="user")
     */
    protected $attributes;
    
    /**
     * User role associated with this user.
     *
     * @var UserRole
     * @ManyToOne(targetEntity="API\Models\Entity\Users\UserRole",inversedBy="users")
     * @JoinColumn(name="role_id",referencedColumnName="id")
     */
    protected $role;
    
    /**
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\UserGroup",inversedBy="users")
     * @JoinTable(name="user_group_list")
     */
    protected $groups;
    
    /**
     * @var int
     * @Column(type="integer")
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
     * Returns the old database ID for this user.
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
     * @return Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Sets the entity's username, but only works on creation.
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
     * Sets the entity's password.
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
     * Sets the entity's e-mail address.
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
     * Sets the old database ID for this user.
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
