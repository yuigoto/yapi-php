<?php
/**
 * YAPI/SLIM : Bootstrapper Script
 * ----------------------------------------------------------------------
 * Bootstraps basic permissions, user roles, groups and the super admin user.
 *
 * Execute this, then take it off the web root for safety reasons!
 *
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
use Cocur\Slugify\Slugify;
use API\Models\Entity\Users\User;
use API\Models\Entity\Users\UserAttribute;
use API\Models\Entity\Users\UserGroup;
use API\Models\Entity\Users\UserPermission;
use API\Models\Entity\Users\UserRole;

// Require Composer autoload
require_once '../vendor/autoload.php';

// Fire up application and get the container
$container = (new Api())->getContainer();

// Set entityManager
$em = $container->get('em');

// Get query builder
$qb = $em->createQueryBuilder();

// Set slugify
$slugify = new Slugify();

// START BOOTSTRAPPING
// ----------------------------------------------------------------------

// USER PERMISSIONS
// ----------------------------------------------------------------------

// User permission array (slug => name)
$permissions = [
    // Administrator Privileges
    'Super Admin' => 'Super Administrator',
    'Administrator' => 'Administrator',

    // User Management
    'User Manage' => 'Manage Users',
    'User Create' => 'Create Users',
    'User Edit' => 'Edit Users',
    'User Delete' => 'Delete Users',
    'User Browse' => 'Browse Users',
    
    // Group Management
    'Group Manage' => 'Manage Groups',
    'Group Create' => 'Create Groups',
    'Group Edit' => 'Edit Groups',
    'Group Delete' => 'Delete Groups',
    'Group Browse' => 'Browse Groups',

    // Content Management
    'Content Manage' => 'Manage Contents',
    'Content Create' => 'Create Contents',
    'Content Edit' => 'Edit Contents',
    'Content Delete' => 'Delete Contents',
    'Content Browse' => 'Browse Contents',

    // Publishing Contents
    'Article Manage' => 'Manage Articles',
    'Article Publish' => 'Publish Articles',
    'Article Edit' => 'Edit Articles',
    'Article Delete' => 'Delete Articles',
    'Article Browse' => 'Browse Articles'
];

// Create permissions
foreach ($permissions as $k => $v) {
    $user_permission = (new UserPermission())
        ->setName($v)
        ->setSlug($slugify->slugify($k));
    
    // Persist permission
    $em->persist($user_permission);
}
$em->flush();

// USER ROLES
// ----------------------------------------------------------------------

// User role list (permissions are the slugs)
$roles = [
    [
        'name' => 'Super Administrator',
        'slug' => $slugify->slugify('Super Administrator'),
        'permissions' => [
            'Super Admin',
            'Administrator',
            'User Manage',
            'User Create',
            'User Edit',
            'User Delete',
            'User Browse',
            'Group Manage',
            'Group Create',
            'Group Edit',
            'Group Delete',
            'Group Browse',
            'Content Manage',
            'Content Create',
            'Content Edit',
            'Content Delete',
            'Content Browse',
            'Article Manage',
            'Article Publish',
            'Article Edit',
            'Article Delete',
            'Article Browse'
        ]
    ],
    [
        'name' => 'Administrator',
        'slug' => $slugify->slugify('Administrator'),
        'permissions' => [
            'Administrator',
            'User Manage',
            'User Create',
            'User Edit',
            'User Delete',
            'User Browse',
            'Group Manage',
            'Group Create',
            'Group Edit',
            'Group Delete',
            'Group Browse',
            'Content Manage',
            'Content Create',
            'Content Edit',
            'Content Delete',
            'Content Browse',
            'Article Manage',
            'Article Publish',
            'Article Edit',
            'Article Delete',
            'Article Browse'
        ]
    ],
    [
        'name' => 'Manager',
        'slug' => $slugify->slugify('Manager'),
        'permissions' => [
            'User Manage',
            'User Create',
            'User Edit',
            'User Delete',
            'User Browse',
            'Group Manage',
            'Group Create',
            'Group Edit',
            'Group Delete',
            'Group Browse',
            'Content Manage',
            'Content Create',
            'Content Edit',
            'Content Delete',
            'Content Browse',
            'Article Manage',
            'Article Publish',
            'Article Edit',
            'Article Delete',
            'Article Browse'
        ]
    ],
    [
        'name' => 'Collaborator',
        'slug' => $slugify->slugify('Collaborator'),
        'permissions' => [
            'User Manage',
            'User Create',
            'User Edit',
            'User Browse',
            'Group Manage',
            'Group Create',
            'Group Edit',
            'Group Browse',
            'Content Manage',
            'Content Create',
            'Content Edit',
            'Content Browse',
            'Article Manage',
            'Article Publish',
            'Article Edit',
            'Article Browse'
        ]
    ],
    [
        'name' => 'User',
        'slug' => $slugify->slugify('User'),
        'permissions' => [
            'Group Create',
            'Group Edit',
            'Group Browse',
            'Content Create',
            'Content Edit',
            'Content Browse',
            'Article Manage',
            'Article Publish',
            'Article Edit',
            'Article Browse'
        ]
    ]
];

// Create user roles
foreach ($roles as $role) {
    // New role
    $user_role = (new UserRole())
        ->setName($role['name'])
        ->setSlug($role['slug']);
    
    // Assign permissions to this role
    foreach ($role['permissions'] as $p_name) {
        // Search for permission by the slug
        $permission = $em->getRepository("API\Models\Entity\Users\UserPermission")
            ->findBy(['slug' => $slugify->slugify($p_name)]);
        
        // If found, assign!
        if (count($permission) > 0) {
            $perm = $permission[0];
            $user_role->addPermission($perm);
        }
    }
    
    // Persist user role
    $em->persist($user_role);
}
$em->flush();

// GROUPS
// ----------------------------------------------------------------------

// User group list
$group_list = [
    [
        'name' => 'Administrators',
        'slug' => $slugify->slugify('Administrators'),
        'description' => 'System administrators group.',
        'image' => null,
        'protected' => true
    ],
    [
        'name' => 'Managers',
        'slug' => $slugify->slugify('Managers'),
        'description' => 'System managers group.',
        'image' => null,
        'protected' => true
    ],
    [
        'name' => 'Users',
        'slug' => $slugify->slugify('Users'),
        'description' => 'Users group.',
        'image' => null,
        'protected' => true
    ]
];

// Add groups
foreach ($group_list as $group) {
    $new_group = (new UserGroup())
        ->setName($group['name'])
        ->setSlug($group['slug'])
        ->setDescription($group['description'])
        ->setImage($group['image'])
        ->setProtected($group['protected']);
    $em->persist($new_group);
}
$em->flush();

// USERS
// ----------------------------------------------------------------------

// Initial User Lists
$user_list = [
    [
        'username' => getenv('INITUSER_USER'),
        'password' => getenv('INITUSER_PASS'),
        'email' => getenv('INITUSER_EMAIL'),
        'attributes' => [
            'f_name' => getenv('INITUSER_F_NAME'),
            'm_name' => getenv('INITUSER_M_NAME'),
            'l_name' => getenv('INITUSER_L_NAME'),
            'display_name' => getenv('INITUSER_F_NAME').' '.getenv('INITUSER_L_NAME'),
            'birthday' => getenv('INITUSER_BIRTH')
        ], 
        'role' => 'Super Administrator',
        'groups' => [
            'Administrators', 'Managers', 'Users'
        ],
        'old_id' => null
    ]
];

// Adds users
foreach ($user_list as $user) {
    // Create new user
    $new_user = (new User())
        ->setUsername($user['username'])
        ->setPassword($user['password'])
        ->setEmail($user['email']);

    // Search for the role
    $rl = $em->getRepository('API\Models\Entity\Users\UserRole')
        ->findBy(['slug' => $slugify->slugify($user['role'])]);
    // Assign, if found
    if (count($rl) > 0) $new_user->setRole($rl[0]);
    
    // Persist the new user, so he gets an ID
    $em->persist($new_user);
    $em->flush();
    
    // Assign attributes
    foreach ($user['attributes'] as $k => $v) {
        // Create the attribute
        $attr = (new UserAttribute())
            ->setName($k)
            ->setValue($v)
            ->setUser($new_user);
        
        // Persist, then add
        $em->persist($attr);
        $new_user->addAttribute($attr);
    }
    $em->flush();
    
    // Assign groups
    foreach ($user['groups'] as $group) {
        $gp = $em->getRepository('API\Models\Entity\Users\UserGroup')
            ->findBy(['slug' => $slugify->slugify($group)]);
        // Assign, if found
        if (count($gp) > 0) $new_user->addGroup($gp[0]);
    }
    
    // Persist the user
    $em->persist($new_user);
}
$em->flush();

// Finished, no exceptions and errors!
echo 'Bootstrapping finished!';
