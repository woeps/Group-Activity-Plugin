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
<h1><?php echo __('Edit Activity'); ?></h1>

<p>
	<?php
	if(isset($errors) || isset($messages))
		{
	?>
		<div class="errorList">
			<?php
			if(isset($errors))
				{
				echo __('Please check the following data again:').'<br/>';
				foreach($errors as $error)
					{
					echo '<div class="errorListItem">'.__($error).'</div>';
					}
				}
			?>
			<?php
			if(isset($errors) && isset($messages)) echo '<br/>';
			if(isset($messages))
				{
				foreach($messages as $message)
					{
					echo '<div class="messageListItem">'.__($message).'</div>';
					}
				}
			?>
		</div>
	<?php
		}
	?>
	<form action="" method="post" id="addForm">
	<table border="0" class="eventTable">
	<tr>
		<td align="right">
			<?=__('Activity Name');?>:&nbsp;
		</td>
		<td>
			<input type="text" name="activityName" id="activityName" size="50px" <?=(isset($post['activityName']))?'value="'.$post['activityName'].'"':'';?> />
		</td>
	</tr>
	<tr>
		<td align="right">
			<?=__('Created By User');?>:&nbsp;
		</td>
		<td align="left">
			<?php
			if(AuthUser::hasPermission('group_activity_admin'))
				{
				echo '<select name="uid" id="uid" />';
				foreach(User::findAll(array('order'=>'name ASC')) as $usr)
					{
					echo '<option value="'.$usr->id.'" ';
					if(isset($post['uid']) && $post['uid'] == $usr->id) echo 'SELECTED="SELECTED"';
					echo'>'.$usr->name.'</option>';
					}
				echo '</select>';
				}
			else
				{
				$userid		= AuthUser::getId();
				$username	= User::findById($userid)->name;
				echo $username.'<input type="hidden" name="uid" value="'.$userid.'"/>';
				}
			?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?=__('From');?>:&nbsp;
		</td>
		<td align="left">
			<select name="dateFromD" id="dateFromD" onChange="getElementById('dateToD').value=this.value">
				<?php
				for($x=1; $x<=31; $x++)
					{
					echo '<option ';
					if(isset($post['dateFromD']) && $post['dateFromD'] == $x) echo 'SELECTED="SELECTED"';
					echo '>'.$x.'</option>';
					}
				?>
			</select>
			.
			<select name="dateFromM" id="dateFromM" onChange="getElementById('dateToM').value=this.value">
				<?php
				for($x=1; $x<=12; $x++)
					{
					echo '<option ';
					if(isset($post['dateFromM']) && $post['dateFromM'] == $x) echo 'SELECTED="SELECTED"';
					echo'>'.$x.'</option>';
					}
				?>
			</select>
			.
			<select name="dateFromY" id="dateFromY" onChange="getElementById('dateToY').value=this.value">
				<?php
				for($x=date('Y', time()); $x<=(date('Y', time())+10); $x++)
					{
					echo '<option ';
					if(isset($post['dateFromY']) && $post['dateFromY'] == $x) echo 'SELECTED="SELECTED"';
					echo '>'.$x.'</option>';
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?=__('To');?>:&nbsp;
		</td>
		<td align="left">
			<select name="dateToD" id="dateToD">
				<?php
				for($x=1; $x<=31; $x++)
					{
					echo '<option ';
					if(isset($post['dateToD']) && $post['dateToD'] == $x) echo 'SELECTED="SELECTED"';
					echo '>'.$x.'</option>';
					}
				?>
			</select>
			.
			<select name="dateToM" id="dateToM">
				<?php
				for($x=1; $x<=12; $x++)
					{
					echo '<option ';
					if(isset($post['dateToM']) && $post['dateToM'] == $x) echo 'SELECTED="SELECTED"';
					echo '>'.$x.'</option>';
					}
				?>
			</select>
			.
			<select name="dateToY" id="dateToY">
				<?php
				for($x=date('Y', time()); $x<=(date('Y', time())+10); $x++)
					{
					echo '<option ';
					if(isset($post['dateToY']) && $post['dateToY'] == $x) echo 'SELECTED="SELECTED"';
					echo '>'.$x.'</option>';
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?=__('Location');?>:&nbsp;
		</td>
		<td align="left">
			<input type="text" name="location" size="50px" maxlength="255" <?=(isset($post['location']))?'value="'.$post['location'].'"':'';?> />
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<?=__('Info');?>:&nbsp;
		</td>
		<td align="left">
			<textarea name="info" rows="10" cols="20"><?=(isset($post['info']))?$post['info']:'';?></textarea>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?=__('State');?>:&nbsp;
		</td>
		<td align="left">
			<select name="state" id="state">
				<option value="0" <?=(isset($post['state']) && $post['state']==0)?'SELECTED="SELECTED"':'';?> ><?=__('not published');?></option>
				<option value="1" <?=(isset($post['state']) && $post['state']==1)?'SELECTED="SELECTED"':'';?> ><?=__('internal published');?></option>
				<option value="2" <?=(isset($post['state']) && $post['state']==2)?'SELECTED="SELECTED"':'';?> ><?=__('published');?></option>
			</select>
		</td>
	</tr>
	<?php
	if(AuthUser::hasPermission('group_activity_admin'))
		{
	?>
	<tr>
		<td align="right">
			<?=__('Approve');?>:&nbsp;
		</td>
		<td align="left">
			<select name="approve" id="approve">
				<option value="0" <?=(isset($post['approve']) && $post['approve']==0)?'SELECTED="SELECTED"':'';?> ><?=__('Approved');?></option>
				<option value="1" <?=(isset($post['approve']) && $post['approve']==1)?'SELECTED="SELECTED"':'';?> ><?=__('Not Approved');?></option>
			</select>
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td colspan="2" align="center">
			<input class="button" name="save" type="submit" accesskey="a" value="<?php echo __('Save'); ?>" />
		    <?php echo __('or'); ?> <a href="<?php echo get_url('plugin/group_activity'); ?>"><?php echo __('Cancel'); ?></a>
		</td>
	</tr>
	</table>
	</form>
</p>