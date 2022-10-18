<?php
namespace YAPI\Core\Base;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

class BaseEntity extends Mappable
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @var string
     * @ORM\Column(type="guid", unique=true)
     */
    protected string $uuid;

    /**
     * @var string|DateTime|mixed
     * @ORM\Column(type="datetime")
     */
    protected string $created_at;

    /**
     * @var string|DateTime|mixed
     * @ORM\Column(type="datetime")
     */
    protected string $updated_at;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected bool $deleted = false;

    /**
     * @return int
     */
    public function getId (): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUuid (): string
    {
        return $this->uuid;
    }

    /**
     * @return string|mixed
     */
    public function getCreatedAt ()
    {
        return $this->created_at;
    }

    /**
     * @return string|mixed
     */
    public function getUpdatedAt ()
    {
        return $this->updated_at;
    }

    /**
     * @return bool
     */
    public function getDeleted (): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     * @return BaseEntity
     */
    public function setDeleted ( bool $deleted ): BaseEntity
    {
        $this->deleted = ($deleted === true);
        return $this;
    }

    /**
     * @return $this
     */
    public function toggleDeleted (): BaseEntity
    {
        $this->deleted = !$this->deleted;
        return $this;
    }

    /**
     * @param string|DateTime|mixed $date
     * @return $this
     */
    protected function setCreatedAt ( DateTime $date): BaseEntity
    {
        $this->created_at = $date;
        return $this;
    }

    /**
     * @param string|DateTime|mixed $date
     * @return $this
     */
    protected function setUpdatedAt ( DateTime $date ): BaseEntity
    {
        $this->updated_at = $date;
        return $this;
    }

    /**
     * Runs during insert.
     *
     * @return void
     * @ORM\PrePersist
     */
    public function defineUuid ()
    {
        $uuid = Uuid::uuid4();
        $this->uuid = $uuid;
    }

    /**
     * @return void
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimeStamps ()
    {
        $this->setUpdatedAt( new DateTime( 'now' ) );
        if ( $this->getCreatedAt() === null ) {
            $this->setCreatedAt( new DateTime( 'now' ) );
        }
    }
}
