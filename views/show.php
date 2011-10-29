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
<h1><?php echo __('Show Activity'); ?></h1>

<p>
<h3><?=$event->name;?> <font size="-1">(<?php
switch($event->status)
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
?>)
<?php
	if(AuthUser::hasPermission('group_activity_admin') || AuthUser::getId() == $event->user)
		{
		echo '<a href="'.get_url('plugin/group_activity/edit/'.$event->id).'"><img src="'.ICONS_URI.'page-16.png" alt="'.__('Edit').' icon"/></a> <a href="'.get_url('plugin/group_activity/remove/'.$event->id).'"><img src="'.ICONS_URI.'delete-16.png" alt="'.__('Remove').' icon"/></a>';
		if(AuthUser::hasPermission('group_activity_admin'))
			{
			echo ' <a href="'.get_url('plugin/group_activity/approve/'.$event->id).'"><img src="'.ICONS_URI.'open-16.png" alt="'.__('Approve').' icon"/></a>';
			}
		}
?>
</font></h3>
<table border="0" class="eventTable">
<tr>
	<td align="right">
		<?=__('Created By User');?>:&nbsp;
	</td>
	<td align="left">
		<?php
		$user = User::findByIdFrom('User', $event->user);
		if($user){echo $user->name;}else{echo __('unknown');}
		?>
	</td>
</tr>
<tr>
	<td align="right">
		<?=__('From');?>:&nbsp;
	</td>
	<td align="left">
		<?=date('d.m.Y', $event->dateFrom);?>
	</td>
</tr>
<tr>
	<td align="right">
		<?=__('To');?>:&nbsp;
	</td>
	<td align="left">
		<?=($event->dateTo>0)?date('d.m.Y', $event->dateTo):date('d.m.Y', $event->dateFrom);?>
	</td>
</tr>
<tr>
	<td align="right">
		<?=__('Location');?>:&nbsp;
	</td>
	<td align="left">
		<?=$event_misc->location;?>
	</td>
</tr>
<tr>
	<td align="right" valign="top">
		<?=__('Info');?>:&nbsp;
	</td>
	<td align="left">
		<span class="eventInfo"><?=$event_misc->info;?></span>
	</td>
</tr>
<tr>
	<td align="right" valign="top">
		<?=__('Attending User');?>:&nbsp;
	</td>
	<td align="left">
		<?php
		$attendingUserIds = explode(', ', $event_misc->attendingUser);
		$x=1;
		foreach($attendingUserIds as $aui)
			{
			$aui2add = User::findByIdFrom('User', $aui);
			if($aui2add)
				{
				$aui2add = $aui2add->name;
				if(!empty($aui2add) && strlen($aui2add) > 1)
					{
					echo $aui2add;
					if($x<count($attendingUserIds)) echo ', ';
					}
				else
					{
					echo '<i>unknown</i>';
					if($x<count($attendingUserIds)) echo ', ';
					}
				}
			else
				{
				echo '<i>'.__('none').'</i>';
				}
			$x++;
			}
		?>
	</td>
</tr>
<?php if($event->dateFrom > time()): ?>
<tr>
	<td align="center" colspan="2">
		<form action="<?=get_url('plugin/group_activity/attend/'.$event->id);?>" method="post">
			<input type="submit" class="button" value="<?=(in_array(AuthUser::getId(), $attendingUserIds))?__('Do Not Attend'):__('Attend');?>">
		</form>
	</td>
</tr>
<?php endif; ?>
</table>
</p>