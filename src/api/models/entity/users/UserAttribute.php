<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI/SLIM : API\Models\Entity\Users\UserAttribute
 * ----------------------------------------------------------------------
 * Handles user attributes in an EAV model.
 *
 * Do not register searchable or highly requestes attributes in this table,
 * as it hinders performance!
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 *
 * @Entity
 * @Table(name="user_attribute")
 * @HasLifecycleCallbacks
 */
class UserAttribute extends BaseEntity
{
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
     * @Column(type="string",length=4096)
     */
    protected $value;
    
    /**
     * User this entity is assigned to.
     *
     * @var User
     * @ManyToOne(targetEntity="API\Models\Entity\Users\User",inversedBy="attributes")
     * @JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $user;
    
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
     * Returns the value.
     *
     * @return string
     */
    public function getValue() 
    {
        return $this->value;
    }
    
    /**
     * Returns the attribute as a [name => value] array.
     *
     * @return array
     */
    public function getAttribute()
    {
        return [
            $this->name => $this->value
        ];
    }
    
    /**
     * Gets the user assigned to this attribute.
     *
     * @return User
     */
    public function getUser() 
    {
        return $this->user;
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
     * Assigns a user to this attribute.
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
