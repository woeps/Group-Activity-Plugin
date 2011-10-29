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
<h1><?php echo __('Settings'); ?></h1>
<p>
<?=__('There are no settings available yet in this version of the plugin!');?>
</p>

<script type="text/javascript">
// <![CDATA[
    function setConfirmUnload(on, msg) {
        window.onbeforeunload = (on) ? unloadMessage : null;
        return true;
    }

    function unloadMessage() {
        return '<?php echo __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
    }

    $(document).ready(function() {
        // Prevent accidentally navigating away
        $(':input').bind('change', function() { setConfirmUnload(true); });
        $('form').submit(function() { setConfirmUnload(false); return true; });
    });
// ]]>
</script>