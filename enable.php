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

//Security
if (!AuthUser::isLoggedIn())
	{
    redirect(get_url('login'));
	}
else if (!AuthUser::hasPermission('admin_edit'))
	{
    Flash::set('error', __('You do not have permission to activate or use this plugin!'));
    Plugin::deactivate('group_activity');
    redirect(get_url());
	}

$error = array();
$sucess = array();

//Prepare To Create Needed Tables
$PDO = Record::getConnection();
$driver = strtolower($PDO->getAttribute(Record::ATTR_DRIVER_NAME));

// setup the table
if($driver == 'mysql')
	{
	$table1 = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."group_activity_event` (
		`id` int(5) unsigned NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL DEFAULT '',
		`status` int(1) DEFAULT NULL,
		`user` varchar(255) DEFAULT NULL,
		`dateFrom` int(10) DEFAULT NULL,
		`dateTo` int(10) DEFAULT NULL,
		`created` int(10) DEFAULT NULL,
		`edited` int(10) DEFAULT NULL,
		`approved` tinyint(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`),
		UNIQUE KEY `id` (`id`)
		) ENGINE=MyISAM;";

	$table2 = "CREATE TABLE IF NOT EXISTS`".TABLE_PREFIX."group_activity_event_misc` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`location` varchar(255) DEFAULT '',
		`info` mediumtext,
		`attendingUser` varchar(255) DEFAULT '',
		PRIMARY KEY (`id`),
		UNIQUE KEY `id` (`id`)
		) ENGINE=MyISAM;";
	}

$dberr = false;
// Create Tables
if(!$PDO->exec($table1))
	$dberr = true;
if(!$PDO->exec($table2))
	$dberr = true;

if($dberr)
	$error[] = __('DB-Tables couldn\'t be created.');
else
	{
	$success[] = __('DB-Tables were successfully created.');
	}

// Check if Plugin is allready installed
$newPerm = Permission::findByName('group_activity_admin');
$cau = Role::findByName('GroupActivityAdmin');
if(!$newPerm || !$cau)	// if not installed
	{			
	$permerr = false;	// Set var to check for errors
	// add group_activity_admin-permission
	if(!$newPerm)	// If permission hasn't been set before
		{
		//Add Permission to Database
		$newPerm = new Permission();
		$newPerm->name = 'group_activity_admin';
		if(!$newPerm->save())
			$permerr = true;
		}
	else	// If permissions exists in db
		{
		$error[] = __('Permission has allready been set!');
		}
	
	// add GroupActivityAdmin-Role	
	if(!$cau)	// If Role hasn't been created before
		{
		// Create New Role GroupActivityAdmin)
		$cau = new Role();
		$cau->name = 'GroupActivityAdmin';
		if(!$cau->save())
			$permerr = true;
		}
	else	// IF Role exists in db
		{
		$error[] = __('Role has allready been created!');
		}
	
	// add permission to role
	if(!$cau->hasPermission('group_activity_admin'))
		{
		$perms[] = Permission::findByName('admin_view');	// load admin_view-permission
		$perms[] = $newPerm;
		if(!RolePermission::savePermissionsFor($cau->id, $perms))
			$permerr = true;
		}
	else
		{
		$error[] = __('Permission was allready applied to role.');
		}
	
	if($permerr)
		$error[] = __('An error occured during the set up of the GroupActivityAdmin-Role or the permissions.');
	else
		$success[] = __('Plugin-Installation successfully finished.');
	}
else
	{
	$success[] = __('Plugin has been successfully enabled.');
	}

if(count($error)>0)
	{
	$errors = '';
	foreach($error as $err)
		$errors .= $err.'<br/>';
	Flash::set('error', $errors);
	}

if(count($success)>0)
	{
	$successs = '';
	foreach($success as $suc)
		$successs .= $suc.'<br/>';
	Flash::set('success', $successs);
	}
	

exit();