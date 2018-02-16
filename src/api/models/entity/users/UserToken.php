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
 * YAPI/SLIM : API\Models\Entity\Users\UserToken
 * ----------------------------------------------------------------------
 * User token entity.
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 * 
 * @Entity
 * @Table(name="user_token")
 * @HasLifecycleCallbacks
 */
class UserToken extends BaseEntity
{
    /**
     * @var string
     * @Column(type="text",nullable=false)
     */
    protected $token;
    
    /**
     * @var int
     * @Column(type="integer",nullable=false)
     */
    protected $expires;
    
    /**
     * @var boolean
     * @Column(type="boolean",nullable=false)
     */
    protected $is_valid = true;
    
    /**
     * @var User
     * @ManyToOne(targetEntity="API\Models\Entity\Users\User")
     * @JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $user;
    
    /**
     * Returns the JWT token, encoded.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Returns the expire date of this token as a UNIX timestamp.
     *
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
    }
    
    /**
     * Returns the validation status of this token.
     *
     * @return bool
     */
    public function getIsValid()
    {
        return $this->is_valid;
    }
    
    /**
     * Returns the user entity associated with this token.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
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
     * Sets expiration date for this token.
     *
     * @param int $expires
     * @return $this
     */
    public function setExpires(int $expires)
    {
        $this->expires = $expires;
        return $this;
    }
    
    /**
     * Sets validity of this token.
     *
     * @param bool $valid
     * @return $this
     */
    public function setIsValid(bool $valid)
    {
        $this->is_valid = ($valid === true);
        return $this;
    }
    
    /**
     * Assigns a user account to this token.
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
