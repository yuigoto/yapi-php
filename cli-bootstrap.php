<?php
use API\Api;
use API\Models\Entity\Users\User;
use API\Models\Entity\Users\UserAttribute;
use API\Models\Entity\Users\UserGroup;
use API\Models\Entity\Users\UserPermission;
use API\Models\Entity\Users\UserRole;
use Cocur\Slugify\Slugify;

/**
 * YAPI : CLI Bootstrap
 * ----------------------------------------------------------------------
 * Used by `bootstrap-data.bat` to initialize data, you must have PHP 
 * set up in your environment to use it.
 * 
 * It loads all base data from the JSON files and also builds the root  
 * user for the application, based on the `.env` data.
 * 
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */

// Require composer autoload
require_once 'vendor/autoload.php';

// Fire application and get container
$container = (new Api())->getContainer();

// Set entity manager
$em = $container->get('em');

// Get query builder
$qb = $em->createQueryBuilder();

// Create an instance of slugify
$slugify = new Slugify();

// Load bootstraper data
$list = [
    'groups' => json_decode(
        file_get_contents(API_ROOT.'\bootstrap\user.groups.json'), true
    ), 
    'permissions' => json_decode(
        file_get_contents(API_ROOT.'\bootstrap\user.permissions.json'), true
    ), 
    'role' => json_decode(
        file_get_contents(API_ROOT.'\bootstrap\user.roles.json'), true
    )
];

// Stop the script if already initialized
$has_data = [
    'permissions' => $em
        ->getRepository("API\Models\Entity\Users\UserPermission")
        ->findAll(),
    'role' => $em
        ->getRepository("API\Models\Entity\Users\UserRole")
        ->findAll(),
    'groups' => $em
        ->getRepository("API\Models\Entity\Users\UserGroup")
        ->findAll(),
    'user' => $em
        ->getRepository("API\Models\Entity\Users\User")
        ->findAll()
];

// If any data was initialized, STOP!
foreach ($has_data as $items) {
    if (count($items) > 0) {
        print "\r\n";
        print "\x1b[1m\x1b[31m-- BASE DATA ALREADY INITIALIZED\x1b[0m\r\n";
        print "\x1b[1m\x1b[33m-- --------------------------------------------------\x1b[0m\r\n";
        print "-- Please, wipe the data in your database if you want\r\n";
        print "-- to start from a clean slate.\r\n";
        print "\x1b[1m\x1b[33m-- --------------------------------------------------\x1b[0m\r\n";
        return false;
    }
}

// Permissions
// ----------------------------------------------------------------------

// Add permissions
foreach ($list['permissions'] as $k => $v) {
    // Create permission
    $permission = (new UserPermission())
        ->setName($v['name'])
        ->setSlug($slugify->slugify($v['slug']));
    
    // Persist
    $em->persist($permission);
}

// Flush
$em->flush();

// Roles
// ----------------------------------------------------------------------

// Add roles
foreach ($list['role'] as $role) {
    // Create Role
    $user_role = (new UserRole())
        ->setName($role['name'])
        ->setSlug($slugify->slugify($role['slug']));

    // Assign permissions
    foreach ($role['permissions'] as $perm_name) {
        // Search for permission
        $role_perm = $em
            ->getRepository("API\Models\Entity\Users\UserPermission")
            ->findBy(['slug' => $slugify->slugify($perm_name)]);
        
        if (count($permission) > 0) {
            $role_perm = $role_perm[0];
            $user_role->addPermission($role_perm);
        }
    }

    // Persist role
    $em->persist($user_role);
}

// Flush
$em->flush();

// Groups
// ----------------------------------------------------------------------

// Add Groups
foreach ($list['groups'] as $group) {
    $user_group = (new UserGroup())
        ->setName($group['name'])
        ->setSlug($slugify->slugify($group['slug']))
        ->setDescription($group['description'])
        ->setImage($group['image'])
        ->setProtected($group['protected']);

    // Persist group
    $em->persist($user_group);
}

// Flush
$em->flush();

// Users
// ----------------------------------------------------------------------

// Define initial user list
$user_list = [
    // Super Administrator
    [
        'username' => getenv('INIT_USER_USER'), 
        'password' => getenv('INIT_USER_PASS'), 
        'email' => getenv('INIT_USER_EMAIL'), 
        'attributes' => [
            'f_name' => getenv('INIT_USER_FNAME'), 
            'm_name' => getenv('INIT_USER_MNAME'), 
            'l_name' => getenv('INIT_USER_LNAME'), 
            'display_name' => getenv('INIT_USER_FNAME')
                .' '.getenv('INIT_USER_LNAME'), 
            'birthday' => getenv('INIT_USER_BIRTH'), 
        ], 
        'role' => 'Super Administrator', 
        'groups' => [
            'Administrators', 'Managers', 'Users'
        ], 
        'old_id' => null
    ]
];

// Add Users
foreach ($user_list as $user) {
    // Create user
    $new_user = (new User())
        ->setUsername($user['username'])
        ->setPassword($user['password'])
        ->setEmail($user['email']);
    
    // Get user role
    $role = $em->getRepository("API\Models\Entity\Users\UserRole")
        ->findBy(['slug' => $slugify->slugify($user['role'])]);
    
    // If found, assign
    if (count($role) > 0) $new_user->setRole($role[0]);

    $em->persist($new_user);
    $em->flush();

    // Assign attributes
    foreach ($user['attributes'] as $k => $v) {
        // Create the attribute
        $attr = (new UserAttribute())
            ->setName($k)
            ->setValue($v)
            ->setUser($new_user);
        
        // Persist
        $em->persist($attr);
    }

    // Assign groups
    foreach ($user['groups'] as $group) {
        $group = $em->getRepository("API\Models\Entity\Users\UserGroup")
            ->findBy(['slug' => $slugify->slugify($group)]);
        // Assign, if found
        if (count($group) > 0) $new_user->addGroup($group[0]);
    }

    // Final persist
    $em->persist($new_user);
}

// Flush
$em->flush();

// Print data
print "\r\n";
print "\x1b[1m\x1b[31m-- SUCCESSFULLY INITIALIZED BASE DATA\x1b[0m\r\n";
print "\x1b[1m\x1b[33m-- --------------------------------------------------\x1b[0m\r\n";
print "-- All right! Now you're good to go!\r\n";
print "-- \r\n";
print "-- Try authenticating into the '/api/auth' endpoint and use\r\n";
print "-- the returned token to validate a request.\r\n";
print "-- \r\n";
print "-- If it's working correctly, then GET DEVELOPING!\r\n";
print "\x1b[1m\x1b[33m-- --------------------------------------------------\x1b[0m\r\n";
return true;
