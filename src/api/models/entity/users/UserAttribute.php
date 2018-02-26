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
 * YAPI : API\Models\Entity\Users\UserAttribute
 * ----------------------------------------------------------------------
 * UserAttribute entity.
 * 
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 * 
 * @Entity
 * @Table(name="user_attribute")
 * @HasLifeCycleCallbacks
 */
class UserAttribute extends BaseEntity 
{
    // Properties
    // ------------------------------------------------------------------

    /**
     * Entity name.
     * 
     * @var string
     * @Column(type="string",length=128,nullable=false)
     */
    protected $name;

    /**
     * Entity value.
     * 
     * @var string
     * @Column(type="string",length=4096,nullable=true)
     */
    protected $value;

    // Relationships
    // ------------------------------------------------------------------

    /**
     * Which user this entity's assigned to.
     * 
     * @var User
     * @ManyToOne(targetEntity="API\Models\Entity\Users\User",inversedBy="attributes")
     * @JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $user;

    // Getters
    // ------------------------------------------------------------------

    /**
     * Retrieves the name.
     *
     * @return string
     */
    public function getName(): string  
    {
        return $this->name;
    }

    /**
     * Retrieves the value.
     *
     * @return string
     */
    public function getValue(): string  
    {
        return $this->value;
    }

    /**
     * Returns the attribute as a [name => value] array.
     *
     * @return array
     */
    public function getAttribute(): array 
    {
        return [$this->name => $this->value];
    }

    /**
     * Returns the user this attribute is assigned to.
     *
     * @return User
     */
    public function getUser(): User 
    {
        return $this->user;
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
     * Sets the value.
     *
     * @param string $value
     * @return $this
     */
    public function setValue(string $value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Assigns to an user.
     *
     * @param User $user
     * @return $this
     */
    public function setUser(User $user) 
    {
        $this->user = $user;
        return $this;
    }
}
