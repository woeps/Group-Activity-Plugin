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
<h1><?php echo __('Approve Activities'); ?></h1>

<p>
<div style="width:48%; display:inline-block;">
	<form action="" method="post">
		<fieldset style="padding:10px 0px;">
			<legend><?=__('not approved');?>:</legend>
		<select name="id[]" id="id[]" size="10" multiple="multiple" style="width:100%;">
			<?php
			foreach($notApprovedEvents as $event)
				{
				echo '<option value="'.$event->id.'">'.$event->name.' ('.date('d.m.Y', $event->dateFrom).' - '.date('d.m.Y', $evemt->dateTo).')</option>';
				}
			?>
		</select>
		<br/>
		<input type="hidden" name="approve" value="1"/>
		<input type="submit" name="do" id="do" value="<?=__('Approve');?>"/>
		</fieldset>
	</form>
</div>
<div style="width:48%; display:inline-block;">
	<form action="" method="post">
		<fieldset style="padding:10px 0px;">
			<legend><?=__('approved');?>:</legend>
		<select name="id[]" id="id[]" size="10" multiple="multiple" style="width:100%;">
			<?php
			foreach($approvedEvents as $event)
				{
				echo '<option value="'.$event->id.'">'.$event->name.' ('.date('d.m.Y', $event->dateFrom).' - '.date('d.m.Y', $evemt->dateTo).')</option>';
				}
			?>
		</select>
		<br/>
		<input type="hidden" name="approve" value="0"/>
		<input type="submit" name="do" id="do" value="<?=__('Do Not Approve');?>"/>
		</fieldset>
	</form>
</div>
</p>