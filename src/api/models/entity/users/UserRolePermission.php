<?php
namespace API\Models\Entity\Users;

use YAPI\Core\BaseEntity;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * YAPI/SLIM : API\Models\Entity\Users\UserRolePermission
 * ----------------------------------------------------------------------
 * Handles user roles and permission associations as collections.
 *
 * @package     API\Models\Entity\Users
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 *
 * @Entity
 * @Table(name="user_role_permission")
 * @HasLifecycleCallbacks
 */
class UserRolePermission extends BaseEntity
{
    /**
     * User role associated with the permission provided.
     *
     * @var UserRole
     * @ManyToOne(targetEntity="API\Models\Entity\Users\UserRole",inversedBy="permissions")
     * @JoinColumn(name="role_id",referencedColumnName="id")
     */
    protected $role;
    
    /**
     * User permission associated with the role provided.
     *
     * @var UserPermission
     * @ManyToOne(targetEntity="API\Models\Entity\Users\UserPermission",inversedBy="roles")
     * @JoinColumn(name="permission_id",referencedColumnName="id")
     */
    protected $permission;
}
