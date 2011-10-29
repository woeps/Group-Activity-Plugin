<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
/**
 * @package Plugins
 * @subpackage group_activity
 *
 * @author Christoph Wanasek <christoph.wanasek@hotmail.com>
 * @copyright Christoph Wanasek 2011
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */

$err = array();
//Security
if (!AuthUser::isLoggedIn())
	{
    redirect(get_url('login'));
	}
else if (!AuthUser::hasPermission('admin_edit'))
	{
    $err[] = __('You do not have permission to uninstall this plugin!');
    redirect(get_url());
	}

// remove tables from db
$PDO = Record::getConnection();
if ($PDO->exec('DROP TABLE IF EXISTS '.TABLE_PREFIX.'group_activity_event') === false)
	{
    $err[] = __('Couldn\'t remove group_activity_event table.');
    redirect(get_url('setting'));
	}
if ($PDO->exec('DROP TABLE IF EXISTS '.TABLE_PREFIX.'group_activity_event_misc') === false)
	{
	$err[] = __('Couldn\'t remove group_activity_event_misc table.');
    redirect(get_url('setting'));
	}

// remove role from every user
foreach(User::findAll() as $user)	// get every user
	{
	$user_roles = $user->roles();	// get user's roles
//echo '<p><pre>'.print_r($user->name, true).': '.print_r($user_roles, true).'</<pre></p>';
	while(current($user_roles))	// loop through user's roles
		{
		if(is_object(current($user_roles)) && current($user_roles)->name == 'GroupActivityAdmin')	// check for GroupActivityAdmin-Role
			unset($user_roles[key($user_roles)]);	// if found, romve Role from list
		next($user_roles);
		}
	
	$newRoles = array();	// prepare role-array for setPermissionsFor-Function
	foreach($user_roles as $user_role)
		if(is_object($user_role))
			$newRoles[$user_role->name] = $user_role->id;
	UserRole::setPermissionsFor($user->id, array_values($newRoles));	// save changed user's roles (this function doesn't return a value)
	$checkRoles = array();
	if($checkRoles = Role::findByUserId($user->id))	// check if there is a corresponding role for actual checked user
		{
		foreach($checkRoles as $checkRole)	// check if User still have assigned the role or was deleted successfully
			{
			if($checkRole->name == 'GroupActivityAdmin')
				{
				$err[] = __('Couldn\'t remove role from the user.');
				}
			}
		}
	}

if(count($err)==0)	// If role could be removed from all users
	{
	// remove role-permission connection from db
//TODO: If Role::findByName() & Permission::findByName doesn't return anything -> build my own function to get rid of the old role_permission entries.
	if(Role::findByName('GroupActivityAdmin') && !RolePermission::deleteWhere('RolePermission', 'role_id = '.Role::findByName('GroupActivityAdmin')->id) || Permission::findByName('group_activity_admin') && !RolePermission::deleteWhere('RolePermission', 'permission_id = '.Permission::findByName('group_activity_admin')->id))
		{
		$err[] = __('Couldn\'t clean role_permission table in db.');
		}
	
	// remove role from db
	if(Role::findByName('GroupActivityAdmin') && !Role::deleteWhere('Role', 'id = '.Role::findByName('GroupActivityAdmin')->id))	// remove role from db
		{
		$err[] = __('Couldn\'t remove role from db.');
		}
	
	// remove permission from db
	if(Permission::findByName('group_activity_admin') && !Permission::deleteWhere('Permission', 'id = '.Permission::findByName('group_activity_admin')->id))
		{
		$err[] = __('Couldn\'t remove permission from db.');
		}
	}

if(count($err)==0)	// if no error showed up during the process
	{
	Flash::set('success', __('Plugin was successfully uninstalled.'));
	if(Plugin::isEnabled('group_activity'))	// disable Plugin if still enabled
		Plugin::deactivate('group_activity');
	redirect(get_url('setting'));
	}
else
	{
//		$this->manage();
	$err[] = __('PLugin couldn\'t be removed.');
//If any errors showed up -> output them!
	$errors = '';
	foreach($err as $error)
		$errors .= $error.'<br/>';
	Flash::set('error', $errors);
	redirect(get_url('setting'));
	}

exit();