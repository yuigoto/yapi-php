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
 * YAPI/SLIM : API\Models\Entity\Users\UserGroup
 * ----------------------------------------------------------------------
 * Basic user group handling.
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 *
 * @Entity
 * @Table(name="user_group")
 * @HasLifecycleCallbacks
 */
class UserGroup extends BaseEntity
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
     * Entity description.
     *
     * @var string
     * @Column(type="string")
     */
    protected $description;
    
    /**
     * Entity image.
     *
     * @var string
     * @Column(type="string",length=128,nullable=true)
     */
    protected $image;
    
    /**
     * Defines entity as editable or not editable.
     *
     * Usually, only two groups are protected: 'Administrators' and 'Users'.
     *
     * @var bool
     * @Column(type="boolean",nullable=false)
     */
    protected $protected = true;
    
    /**
     * Users associated with this group.
     *
     * @var Collection
     * @ManyToMany(targetEntity="API\Models\Entity\Users\User",mappedBy="groups")
     */
    protected $users;
    
    /**
     * UserGroup constructor.
     */
    public function __construct()
    {
        // Parent constructor
        parent::__construct();
        
        // Set attributes
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
        return $this->slug;
    }
    
    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Returns the image name.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Returns the protected status.
     *
     * @return bool
     */
    public function getProtected()
    {
        return $this->protected;
    }
    
    /**
     * Returns the users associated with the group.
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
     * Sets the image name.
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     * Assigns a user to this group.
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
