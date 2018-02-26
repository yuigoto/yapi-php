<?php
namespace API\Core;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Ramsey\Uuid\Uuid;

/**
 * YAPI : API\Core\BaseEntity
 * ----------------------------------------------------------------------
 * Serializable and extendable base entity, defines only the basic fields 
 * most entities use, like the ID and creation/update date.
 * 
 * Inherits and passes down anything from `Mappable`.
 * 
 * @package     API\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class BaseEntity extends Mappable 
{
    /**
     * Numeric ID (Primary Key).
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

    // Getters
    // ------------------------------------------------------------------

    /**
     * Returns the ID
     *
     * @return int
     */
    public function getId(): int 
    {
        return $this->id;
    }
    
    /**
     * Returns the unique identifier.
     *
     * @return string
     */
    public function getUuid(): string 
    {
        return $this->uuid;
    }

    /**
     * Returns the creation date.
     *
     * @return string|mixed
     */
    public function getCreatedAt()   
    {
        return $this->created_at;
    }

    /**
     * Returns the update date.
     *
     * @return string|mixed
     */
    public function getUpdatedAt()  
    {
        return $this->updated_at;
    }

    /**
     * Returns the soft delete status.
     *
     * @return bool
     */
    public function getDeleted(): bool 
    {
        return $this->deleted;
    }

    // Setters
    // ------------------------------------------------------------------

    /**
     * Sets the soft delete status.
     *
     * @param boolean $deleted 
     *      Soft delete status 
     * @return $this 
     */
    public function setDeleted(bool $deleted) 
    {
        $this->deleted = ($deleted === true);
        return $this;
    }

    /**
     * Toggles the soft delete status value.
     *
     * @return $this 
     */
    public function toggleDeleted() 
    {
        $this->deleted = !$this->deleted;
        return $this;
    }

    // Protected Setters
    // ------------------------------------------------------------------

    /**
     * Sets the creation date.
     *
     * @param \DateTime $date 
     *      DateTime instance to be set
     * @return $this 
     */
    protected function setCreatedAt(\DateTime $date) 
    {
        $this->created_at = $date;
        return $this;
    }

    /**
     * Sets the update date.
     *
     * @param \DateTime $date 
     *      DateTime instance to be set
     * @return $this 
     */
    protected function setUpdatedAt(\DateTime $date) 
    {
        $this->updated_at = $date;
        return $this;
    }

    // Lifecycle Callbacks
    // ------------------------------------------------------------------

    /**
     * Runs on insert only, defines a random UUID for this entity.
     *
     * @return void
     * @PrePersist
     */
    public function defineUuid() 
    {
        $uuid = Uuid::uuid4();
        $this->uuid = $uuid;
    }

    /**
     * Sets the created and updated dates on an insert. If updating, it 
     * will set only the updated date.
     * 
     * @return void
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
}
