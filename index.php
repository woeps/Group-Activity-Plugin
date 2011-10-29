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

Plugin::setInfos(array(
    'id'			=> 'group_activity',
    'title'			=> __('Group Activity'),
    'description'	=> __('Planning & Administrating Group-Activities'),
    'version'		=> '0.1',
   	'license'		=> 'GPL',
	'author'		=> 'Christoph Wanasek',
    'website'		=> '',
    #'update_url'	=> '',
    'require_wolf_version'	=> '0.7.5',
	#'type'			=> 'both'
));

if(Plugin::isEnabled('group_activity'))
	{
	// Add Controller
	Plugin::addController('group_activity', __('Group Activity'), 'admin_view', true);
	// Load Models
	AutoLoader::addFile('GroupActivityEvent', CORE_ROOT.'/plugins/group_activity/models/GroupActivityEvent.php');
	AutoLoader::addFile('GroupActivityEventMisc', CORE_ROOT.'/plugins/group_activity/models/GroupActivityEventMisc.php');
	}