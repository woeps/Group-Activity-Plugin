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
<div class="box">
<p class="button">
    <a href="<?php echo get_url('plugin/group_activity/manage'); ?>">
		<img src="<?php echo ICONS_URI; ?>file-folder-32.png" align="middle" alt="management icon" />
        <?php echo __('Manage Activities'); ?>
    </a>
</p>
<p class="button">
    <a href="<?php echo get_url('plugin/group_activity/add'); ?>">
        <img src="<?php echo ICONS_URI; ?>add-page-32.png" align="middle" alt="new icon" />
        <?php echo __('New Activity'); ?>
    </a>
</p>
<?php if(AuthUser::hasPermission('group_activity_admin')){ ?>
<p class="button">
    <a href="<?php echo get_url('plugin/group_activity/approve'); ?>">
        <img src="<?php echo ICONS_URI; ?>open-32.png" align="middle" alt="approve icon" />
        <?php echo __('Approve Activities'); ?>
    </a>
</p>
<p class="button">
    <a href="<?php echo get_url('plugin/group_activity/settings'); ?>">
        <img src="<?php echo ICONS_URI; ?>settings-32.png" align="middle" alt="settings icon" />
        <?php echo __('Settings'); ?>
    </a>
</p>
<?php } ?>
<p class="button">
    <a href="<?php echo get_url('plugin/group_activity/documentation'); ?>">
        <img src="<?php echo URL_PUBLIC; ?>wolf/plugins/group_activity/images/documentation.png" align="middle" alt="documentation icon" />
        <?php echo __('Documentation'); ?>
    </a>
</p>