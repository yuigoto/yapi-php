<?php
namespace API\Models\Entity\Users;

use API\Core\BaseEntity;
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
 * YAPI : API\Models\Entity\Users\UserToken
 * ----------------------------------------------------------------------
 * User token entity.
 * 
 * @package     API\Models\Entity\Users\UserToken
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 * 
 * @Entity
 * @Table(name="user_token")
 * @HasLifecycleCallbacks
 */
class UserToken extends BaseEntity 
{
    // Properties
    // ------------------------------------------------------------------

    /**
     * Token payload.
     *
     * @var string
     * @Column(type="text",nullable=false)
     */
    protected $token;

    /**
     * Expiration date as a UNIX timestamp.
     *
     * @var int
     * @Column(type="integer",nullable=false)
     */
    protected $expires;

    /**
     * Validity status.
     *
     * @var bool
     * @Column(type="boolean",nullable=false)
     */
    protected $is_valid = true;
    
    // Relationships
    // ------------------------------------------------------------------
    
    /**
     * User assigned to this token.
     *
     * @var User 
     * @ManyToOne(targetEntity="API\Models\Entity\Users\User")
     * @JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $user;
    
    // Getters
    // ------------------------------------------------------------------

    /**
     * Returns the token.
     *
     * @return string
     */
    public function getToken(): string 
    {
        return $this->token;
    }

    /**
     * Returns expiration date.
     *
     * @return integer
     */
    public function getExpires(): int 
    {
        return $this->expires;
    }

    /**
     * Returns validation status.
     *
     * @return boolean
     */
    public function getIsValid(): bool 
    {
        return $this->is_valid;
    }

    /**
     * Returns the user associated with this token.
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
     * Sets the user token data.
     *
     * @param string $token
     * @return $this
     */
    public function setToken(string $token) 
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Sets expiration date.
     *
     * @param integer $expires
     * @return $this
     */
    public function setExpires(int $expires) 
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * Sets validity status.
     *
     * @param integer $is_valid
     * @return $this
     */
    public function setIsValid(int $is_valid) 
    {
        $this->is_valid = $is_valid;
        return $this;
    }

    /**
     * Assigns this token to a user.
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
