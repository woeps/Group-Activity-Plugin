<?php
/**
 * @package Plugins
 * @subpackage group_activity
 *
 * @author Christoph Wanasek <christoph.wanasek@hotmail.com>
 * @copyright Christoph Wanasek 2011
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */

if (!defined('IN_CMS')) { exit(); }

 
class GroupActivityEventMisc extends Record
	{
    const TABLE_NAME = 'group_activity_event_misc';
	public $id;
	public $location;
	public $info;
	public $attendingUser;

	public function attend($id, $uid=false)	// Change the attending-value of the given activity (id) for the given user (uid) to the opposite
		{
		if(!$uid)
			$uid = AuthUser::getId();
		if(User::findById($uid))
			{
			$attendingUser = GroupActivityEventMisc::findOneFrom('GroupActivityEventMisc', 'id='.$id)->attendingUser;	// get the value of attendingUser-Column of the activity with given id
			$attendingUserArr = explode(', ', $attendingUser);	// convert attendingUser-value to array
			if(in_array($uid, $attendingUserArr)) // If user has allready attended
				{
				for($x=0; count($attendingUserArr)>$x; $x++)	// Loop through whole attendingUserArr
					{
					if($attendingUserArr[$x] == $uid)	// If value of attendingUserArr-Element is equal to the userId
						{
						unset($attendingUserArr[$x]);	// Remove the userId from the list of attending users
						}
					}
				if(GroupActivityEventMisc::update('GroupActivityEventMisc', array('attendingUser'=>implode(', ', $attendingUserArr)), 'id='.$id))	// If attendingUser-value updated successfully
					return true;
				else
					return false;
				}
			else	// If user hasn't allready attended
				{
				if(strlen($attendingUser>0))	// If user is NOT the first to attend
					{
					$attendingUserNew = $attendingUser.', '.$uid;	// Add actual userId to the list of attending user
					}
				else	// IF user is the first to attend
					{
					$attendingUserNew = $uid;	// set list of attending user to actual userId
					}
				if(GroupActivityEventMisc::update('GroupActivityEventMisc', array('attendingUser'=>$attendingUserNew), 'id='.$id))	// If attendingUser-value updated successfully
					return true;
				else
					return false;
				}
			}
		else
			return false;
		}

	}