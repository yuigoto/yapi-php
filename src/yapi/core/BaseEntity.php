<?php
namespace YAPI\Core;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Ramsey\Uuid\Uuid;

/**
 * YAPI/SLIM : YAPI\Core\BaseEntity
 * ----------------------------------------------------------------------
 * Base entity with only basic fields, you can extend this class to create
 * other entities with these basic properties.
 *
 * @package     YAPI\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class BaseEntity implements \JsonSerializable
{
    /**
     * Numeric ID of the entity (Primary Key).
     *
     * @var int
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    
    /**
     * Unique ID for this entity.
     *
     * @var string
     * @Column(type="guid",unique=true)
     */
    protected $uuid;
    
    /**
     * Creation date.
     *
     * @var string
     * @Column(type="datetime")
     */
    protected $created_at;
    
    /**
     * Update date.
     *
     * @var string
     * @Column(type="datetime")
     */
    protected $updated_at;
    
    /**
     * Soft delete status.
     *
     * @var bool
     * @Column(type="boolean")
     */
    protected $deleted = false;
    
    /**
     * BaseEntity constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Returns the entity's ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Returns the entity's Unique ID.
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
    
    /**
     * Returns the entity's creation date.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Returns the entity's update date.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    
    /**
     * Returns the current "soft delete" status.
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
    
    /**
     * Returns an array with all the object's values.
     *
     * @return array
     */
    public function getValues()
    {
        // Get a list of all the object's properties
        $list = get_object_vars($this);
    
        // Removed any generated variables that might've appeared
        foreach ($list as $k => $v) {
            if (preg_match("/^\_\_/", $k)) unset($list[$k]);
        }
        
        return $list;
    }
    
    /**
     * Sets the soft delete status.
     *
     * @param bool $deleted
     * @return $this
     */
    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }
    
    /**
     * Toggles deleted status.
     *
     * @return $this
     */
    public function toggleDelete()
    {
        $this->deleted = !$this->deleted;
        return $this;
    }
    
    /**
     * On creation, sets both created and updated date values. When updating,
     * only sets the updated value.
     *
     * @PrePersist
     * @PreUpdate
     */
    public function updateTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
    
    /**
     * On creation, defines a UUID for this entity.
     *
     * @PrePersist
     */
    public function defineUuid()
    {
        $uuid = Uuid::uuid4();
        $this->uuid = $uuid;
    }
    
    /**
     * Specifies which of the entity's parameters should be serialized to
     * JSON, in case it's needed.
     *
     * It escapes and removes any property that Doctrine might've created.
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        // Get a list of all the object's properties
        $list = get_object_vars($this);
        
        // Removed any generated variables that might've appeared
        foreach ($list as $k => $v) {
            if (preg_match("/^\_\_/", $k)) unset($list[$k]);
        }
        return $list;
    }
    
    /**
     * Set creation date.
     *
     * @param \DateTime $date
     *      DateTime object
     * @return $this
     */
    protected function setCreatedAt(\DateTime $date)
    {
        $this->created_at = $date;
        return $this;
    }
    
    /**
     * Set the updated date.
     *
     * @param \DateTime $date
     *      DateTime object
     * @return $this
     */
    protected function setUpdatedAt(\DateTime $date)
    {
        $this->updated_at = $date;
        return $this;
    }
}
