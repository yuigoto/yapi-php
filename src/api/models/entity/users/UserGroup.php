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
 * YAPI : API\Models\Entity\Users\UserGroup
 * ----------------------------------------------------------------------
 * UserGroup entity.
 * 
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 * 
 * @Entity
 * @Table(name="user_groups")
 * @HasLifeCycleCallbacks
 */
class UserGroup extends BaseEntity 
{
    // Properties
    // ------------------------------------------------------------------

    /**
     * Group name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $name;

    /**
     * Group slug, generated automatically from the group's name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $slug;

    /**
     * Group description.
     *
     * @var string
     * @Column(type="string")
     */
    protected $description;

    /**
     * Group image file name.
     *
     * @var string 
     * @Column(type="string",length=128,nullable=true)
     */
    protected $image = null;

    /**
     * Identifies groups as editable/not-editable.
     * 
     * Used mostly for base groups and/or admin groups.
     *
     * @var boolean
     * @Column(type="boolean",nullable=false)
     */
    protected $protected = false;

    // Relationships
    // ------------------------------------------------------------------

    /**
     * Collection holding users associated with this group.
     *
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\User",mappedBy="groups")
     */
    protected $users;

    // Constructor
    // ------------------------------------------------------------------

    /**
     * UserGroup constructor.
     */
    public function __construct() 
    {
        // Set collections
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
     * Returns the image file name.
     *
     * @return string
     */
    public function getImage(): string 
    {
        return $this->image;
    }

    /**
     * Returns the protection status.
     *
     * @return boolean
     */
    public function getProtected(): bool 
    {
        return $this->protected;
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
     * Sets the description.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description) 
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Sets the image file name.
     *
     * @param string $image
     * @return $this
     */
    public function setImage(string $image = null) 
    {
        $this->image = ($image !== '') ? $image : null;
        return $this;
    }

    /**
     * Sets protection status.
     *
     * @param bool $protected
     * @return $this
     */
    public function setProtected(bool $protected) 
    {
        $this->protected = $protected;
        return $this;
    }

    /**
     * Toggles protection status.
     * 
     * @return $this
     */
    public function toggleProtected() 
    {
        $this->protected = !$this->protected;
        return $this;
    }

    // Collection Managers
    // ------------------------------------------------------------------

    /**
     * Associates a user with this group.
     *
     * @param User $user
     * @return $this
     */
    public function addUser(User $user) 
    {
        $this->users[] = $user;
        return $this;
    }
}
