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
     * User group name.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $name;
    
    /**
     * User group slug.
     *
     * @var string
     * @Column(type="string",length=128,unique=true)
     */
    protected $slug;
    
    /**
     * User group description.
     *
     * @var string
     * @Column(type="string")
     */
    protected $description;
    
    /**
     * User group image.
     *
     * @var string
     * @Column(type="string",length=128)
     */
    protected $image;
    
    /**
     * Defines if the group is editable or no.
     *
     * Usually, only the first user group (Administrators) would be
     * protected.
     *
     * @var bool
     * @Column(type="boolean",nullable=false)
     */
    protected $protected = true;
    
    /**
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
}
