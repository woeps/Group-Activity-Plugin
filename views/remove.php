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
<h1><?php echo __('Remove Activity'); ?></h1>

<p>
<?=__('Remove following Activity?');?>
<br/>
<p><?=$event->name.' ('.date('d.m.Y', $event->dateFrom).' - '.date('d.m.Y', $event->dateTo).')';?></p>
<br/>
<form action="" method="post">
	<input class="button" name="remove" type="submit" accesskey="r" value="<?=__('Remove');?>"/>
	<?php echo __('or'); ?> <a href="<?php echo get_url('plugin/group_activity'); ?>"><?php echo __('Cancel');?></a>
</form>
</p>