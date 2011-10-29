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
?>
<h1><?=__('Manage Activities');?></h1>

<p>
<b><?php if($archive): ?><a href="<?=get_url('plugin/group_activity/manage');?>"><?=__('Show Actual');?></a><?php else: ?><a href="<?=get_url('plugin/group_activity/manage/archive');?>"><?=__('Show Archive');?></a><?php endif; ?></b><br/>
<div class="group_activity_eventListFrame">
<table class="group_activity_eventList">
<tr class="group_activity_eventListRow">
	<td class="group_activity_eventListItem" align="center">
		<?=__('Name');?>
	</td>
	<td class="group_activity_eventListItem" align="center">
		<?=__('Status');?>
	</td>
	<td class="group_activity_eventListItem" align="center">
		<?=__('Date');?>
	</td>
	<td colspan="3" align="center">
		<?=__('Actions');?>
	</td>
</tr>

<?php
    foreach ($events as $e)
		{
		echo '<tr class="group_activity_eventListRow '.odd_even().'">
				<td class="group_activity_eventListItem">
					<b><a href="'.get_url('plugin/group_activity/show/'.$e->id).'">'.$e->name.'</a></b>
				</td>
				<td class="group_activity_eventListItem" align="center">';
			switch($e->status)
				{
				case 0:
					echo __('not published');
					break;
				case 1:
					echo __('internal published');
					break;
				case 2:
					echo __('published');
					break;
				default:
					echo __('unknown');
					break;
				}
			if(AuthUser::hasPermission('group_activity_admin'))
				{
				echo ' (';
				echo ($e->approved==1)?__('not approved'):__('approved');
				echo ')';
				}
			echo	'</td>
					<td class="group_activity_eventListItem">'.
						date('d.m.Y',$e->dateFrom)
					.'</td>';
					if(AuthUser::hasPermission('group_activity_admin') || $e->user == AuthUser::getId())
						echo '<td class="group_activity_eventListItem"><a href="'.get_url('plugin/group_activity/edit/'.$e->id).'"><img src="'.ICONS_URI.'/page-16.png" alt="'.__('Edit').' Icon"/></a></td><td class="group_activity_eventListItem"><a href="'.get_url('plugin/group_activity/remove/'.$e->id).'"><img src="'.ICONS_URI.'/delete-16.png" alt="'.__('Remove').' icon"/></a></td>';
					else
						echo '<td>&nbsp;</td><td>&nbsp;</td>';
					if(AuthUser::hasPermission('group_activity_admin'))
						{
						echo '<td class="group_activity_eventListItem"><a href="'.get_url('plugin/group_activity/approve/'.$e->id).'"><img src="'.ICONS_URI.'/open-16.png" alt="'.__('Approve').' Icon"/></a></td>';
						}
					else
						echo '<td>&nbsp;</td>';
			echo '</tr>';
	    }
?>
</table>
</div>
</p>