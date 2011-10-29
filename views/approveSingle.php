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
<?=__('Approve following Activity?');?>
<br/>
<p><?=$event->name.' ('.date('d.m.Y', $event->dateFrom).' - '.date('d.m.Y', $event->dateTo).')';?></p>
<br/>
<form action="<?=get_url('plugin/group_activity/approve/'.$id);?>" method="post">
	<input type="hidden" name="id" id="id" value="<?=$id;?>"/>
	<select name="approve" id="approve">
		<option value="0"<?=($event->approved==1)?' SELECTED="SELECTED"':'';?>><?=__('Approve');?></option>
		<option value="1"<?=($event->approved==0)?' SELECTED="SELECTED"':'';?>><?=__('Do Not Approve');?></option>
	</select>&nbsp;
	<input class="button" name="do" type="submit" accesskey="a" value="<?=__('Approve');?>"/>
</form>
</p>