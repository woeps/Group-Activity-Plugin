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


/**
 * Use this SkeletonController and this skeleton plugin as the basis for your
 * new plugins if you want.
 */
class GroupActivityController extends PluginController
	{

    public function __construct()
		{
		// Security (If user isn't logged in redirect to login)
		AuthUser::load();
		if(!AuthUser::isLoggedIn())
			{
			redirect(get_url('login'));
			}
		// load Backend-Leyout and Sidebar
        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View('../../plugins/group_activity/views/sidebar'));
	    }

    public function index()
		{
        $this->manage();
	    }

	public function manage($archiv=false)
		{
		if($archiv)
			{
			$where = '(dateFrom < '.time().' OR dateFrom IS NULL)';
			$data['archive'] = true;
			}
		else
			{
			$where = 'dateFrom > '.time();
			$data['archive'] = false;
			}
		if(AuthUser::hasPermission('group_activity_admin'))	// If admin show every actual activity
			//$data['events'] = GroupActivityEvent::find(array('where'=>'dateFrom > '.time()));
			$data['events'] = GroupActivityEvent::find(array('where'=>$where));
		else	// show actual, (internal) published or own activities
			//$data['events'] = GroupActivityEvent::find(array('where'=>'dateFrom > '.time().' AND status <> 0 OR dateFrom > '.time().' AND user = '.AuthUser::getId()));
			$data['events'] = GroupActivityEvent::find(array('where'=>$where.' AND status <> 0 OR '.$where.' AND user = '.AuthUser::getId()));
		$this->display('group_activity/views/manage', $data);
		}

    public function documentation()
		{
        $this->display('group_activity/views/documentation');	// display documentation
    	}
	
	public function add()
		{
		// Prepare error- & message-array
		$messageList = array();
		if(!isset($_POST['add']))	// If form wasn't sent yet
			{
			$this->display('group_activity/views/add');
			}
		else	// If form was sent allready
			{
			$errorList = $this->_validateActivity($_POST);
			if(count($errorList)>0)	// If validation brought up an error
				{
				$data['errors'] = $errorList;	// forward errorList to the view file
				$data['post'] = $_POST;	// forward the post-vars to the view file
				$this->display('group_activity/views/add', $data);
				}
			else	//try to save the new activity
				{
				// Do Save-Attempt
				$newActivity = new GroupActivityEvent();	// create new instance of GroupActivityEvent-Class (child of Record) and set values
				$newActivity->name = html_encode($_POST['activityName']);
				$newActivity->status = $_POST['state'];
				$newActivity->user = $_POST['uid'];
				$newActivity->dateFrom = strtotime($_POST['dateFromY'].'-'.$_POST['dateFromM'].'-'.$_POST['dateFromD']);
				$newActivity->dateTo = strtotime($_POST['dateToY'].'-'.$_POST['dateToM'].'-'.$_POST['dateToD']);
				$newActivity->created = time();
				$newActivity->approved = (AuthUser::hasPermission('group_activity_admin'))?$_POST['approve']:0;	//only use submitted value if user is admin
				
				$newActivityMisc = new GroupActivityEventMisc();	// create new instance of GroupActivityEventMisc-Class (child of Record) and set values
				$newActivityMisc->location = html_encode($_POST['location']);
				$newActivityMisc->info = html_encode($_POST['info']);
				
				$save = ($newActivity->save() && $newActivityMisc->save())?true:false;	// check if GroupActivityEvent(Misc)-Instance saved successfully and set $save according to the results
				
				if($save)	// If successfully saved
					{
					Flash::set('success', __('Activity successfully added.'));
					redirect(get_url('plugin/group_activity/show/'.$newActivity->lastInsertId()));	// Show the new activity
					}
				else	// If an error occured during saving
					{
					$messageList[] = 'An error occured during the saving-process! - Please try again.';
					$data['messages'] = $messageList;	// forward messageList to the view file
					$data['post'] = $_POST;	// forward the post-vars to the view file
					$this->display('group_activity/views/add', $data);	// Show form filled with post-vars
					}
				}
			}
		}
	
	public function show($id)
		{
		if(!is_numeric($id)){redirect(get_url('plugin/group_activity'));}	//If Parameter is missing or isn't a number
		
		$data['event'] = GroupActivityEvent::findByIdFrom('GroupActivityEvent', $id);
		if(!$data['event']){redirect(get_url('plugin/group_activity'));} //if Id doesn't exist
		$data['event_misc'] = GroupActivityEventMisc::findByIdFrom('GroupActivityEventMisc', $id);
		$this->display('group_activity/views/show', $data);
		}
	
	public function edit($id)	// !!! TODO !!!
		{
		if(!is_numeric($id)) redirect(get_url('plugin/group_activity'));	//If Parameter is missing or isn't a number
		
		$data['event'] = GroupActivityEvent::findByIdFrom('GroupActivityEvent', $id);	// get Activity-Data for the view
		if(!$data['event'])
			redirect(get_url('plugin/group_activity'));	//if Id doesn't exist -> redirect
		else
			$data['event_misc'] = GroupActivityEventMisc::findByIdFrom('GroupActivityEventMisc', $id);	// get addition Activity-Data for the view
		if(AuthUser::hasPermission('group_activity_admin') || $data['event']->user == AuthUser::getId())	// If user is admin or has created the activity
			{
			$data['post']['activityName']	= html_decode($data['event']->name);
			$data['post']['state']			= $data['event']->status;
			$data['post']['uid']			= $data['event']->user;
			$data['post']['dateFromD']		= date('d', $data['event']->dateFrom);
			$data['post']['dateFromM']		= date('m', $data['event']->dateFrom);
			$data['post']['dateFromY']		= date('Y', $data['event']->dateFrom);
			$data['post']['dateToD']		= date('d', $data['event']->dateFrom);
			$data['post']['dateToM']		= date('m', $data['event']->dateFrom);
			$data['post']['dateToY']		= date('Y', $data['event']->dateFrom);
			$data['post']['approve']		= $data['event']->approved;
			
			$data['post']['location']		= html_decode($data['event_misc']->location);
			$data['post']['info']			= html_decode($data['event_misc']->info);
			if(isset($_POST['save']))
				{
				$errorList = $this->_validateActivity($_POST);
				if(count($errorList)>0)	// If validation brought up an error
					{
					$data['errors'] = $errorList;	// forward errorList to the view file
					$data['post'] = $_POST;	// forward the post-vars to the view file
					$this->display('group_activity/views/edit', $data);
					}
				else
					{
					//Do save-actions
					$data['event']->name = html_encode($_POST['activityName']);
					$data['event']->status = $_POST['state'];
					$data['event']->user = $_POST['uid'];
					$dateFrom = $_POST['dateFromY'].'-'.$_POST['dateFromM'].'-'.$_POST['dateFromD'];
					$data['event']->dateFrom = strtotime($dateFrom);
					$dateTo = $_POST['dateToY'].'-'.$_POST['dateToM'].'-'.$_POST['dateToD'];
					$data['event']->dateTo = strtotime($dateTo);
					$data['event']->edited = time();
					$data['event']->approved = (AuthUser::hasPermission('group_activity_admin'))?$_POST['approve']:0;	//only use submitted value if user is admin

					$data['event_misc']->location = html_encode($_POST['location']);
					$data['event_misc']->info = html_encode($_POST['info']);

					$save = ($data['event']->save() && $data['event_misc']->save())?true:false;	// check if GroupActivityEvent(Misc)-Instance saved successfully and set $save according to the results

					if($save)	// If successfully saved
						{
						Flash::set('success', __('Activity successfully edited.'));
						redirect(get_url('plugin/group_activity/show/'.$id));	// Show the new activity
						}
					else	// If an error occured during saving
						{
						$messageList[] = 'An error occured during the saving-process! - Please try again.';
						$data['messages'] = $messageList;	// forward messageList to the view file
						$data['post'] = $_POST;	// forward the post-vars to the view file
						$this->display('group_activity/views/edit', $data);	// Show form filled with post-vars
						}
					}
				}
			else	// If the form wasn't submited yet
				{				
				$this->display('group_activity/views/edit', $data);
				}
			}
		else
			{
			redirect(get_url('plugin/group_activity/show/'.$id));
			}
		}
	
	public function remove($id)
		{
		if(!is_numeric($id) || !AuthUser::hasPermission('group_activity_admin') && GroupActivityEvent::findByIdFrom('GroupActivityEvent', $id)->user!=AuthUser::getId())
			redirect(get_url('plugin/group_activity'));	//If Parameter is missing or isn't a number
		if(!isset($_POST['remove']))	// If form has NOT been submitted
			{
			$data['event'] = GroupActivityEvent::findByIdFrom('GroupActivityEvent', $id);	// Forward Activity-data to view-file
			$data['id'] = $id;	// Forward selected ID to view-file
			if($data['event'])	// If Activity with given id exists 
				{
				$this->display('group_activity/views/remove', $data);	// Aks for deleting
				}
			else	// If no Activity with given id exists
				{
				Flash::set('error', __('An Error occurred!'));
				redirect(get_url('plugin/group_activity'));
				}
			}
		else	// If form has been submitted
			{
			$error = false;	// Prepare error-notice
			if(GroupActivityEvent::existsIn('GroupActivityEvent', 'id='.$id))	// If Activity with given id exists
				{
				if(!GroupActivityEvent::deleteWhere('GroupActivityEvent', 'id='.$id))	// Do delete activity and check if successfull
					$error = true;	// Notice error if activity couldn't be deleted
				}
			else	// Notice error if no Activity with given id exists
				$error = true;
			
			if(!$error)	// If NO errors has been noticed
				{
				Flash::set('success', __('Successfully removed Activity.'));
				redirect(get_url('plugin/group_activity'));
				}
			else	// If errors has been noticed
				{
				Flash::set('success', __('An Error occurred.'));
				redirect(get_url('plugin/group_activity/remove/'.$id));
				}
			}
		}

	public function approve($id=false)
		{
		if(AuthUser::hasPermission('group_activity_admin'))	// If admin
			{
			if(!isset($_POST['id']) || !isset($_POST['approve']) || !isset($_POST['do'])) // if form wasn't submitted
				{
				if(is_numeric($id))	// If Parameter is set and is a number (means: single Activity was selected for approvement)
					{
					// Approvement for 1 single activity (given by id)
					$data['event'] = GroupActivityEvent::findByIdFrom('GroupActivityEvent', $id);	// Get Data of selected Activity
					if($data['event'])	// If successfully retrieved Activity-Data
						{
						$data['id'] = $id;	// Prepare ID-Data for view-file
						$this->display('group_activity/views/approveSingle', $data);
						}
					else	// If Activity-Data couldn't be retrieved
						{
						redirect(get_url('plugin/group_activity'));
						}
					}
				else	// If no parameter was set (means: this is the default to manage multiple activities)
					{
					// Approvement for multiple activities
					$data['notApprovedEvents'] = GroupActivityEvent::find(array('where'=>'approved=0'));	// Get not approved activities
					$data['approvedEvents'] = GroupActivityEvent::find(array('where'=>'approved=1'));	// Get approved activites
					$this->display('group_activity/views/approve', $data);
					}
				}
			else	// If form was submitted
				{
				// If there is only a single activity to update: put it in an array to use the same routine, the "multi-update" uses
				if(!is_array($_POST['id']))	// If submitted id is an array of ids
					$ids = array($_POST['id']); // put single activity into array
				else
					$ids = $_POST['id'];
				
				if(is_numeric($_POST['approve']) && $_POST['approve']>=0 && $_POST['approve']<=1)	// Validate approved-setting 
					{
					$error = false;
					foreach($ids as $id)	// Update every activity with the given id's of the submitted form
						{
						if(is_numeric($id) && !GroupActivityEvent::update('GroupActivityEvent', array('approved'=>$_POST['approve']), 'id='.$id))	// Update Activity
							{
							$error = true;	// If activity couldn't be updated
							}
						}
					}
				if(isset($error) && !$error)	// If approved-setting got successfully validated and there was no error during the activity-update
					{
					Flash::set('success', __('Succesfully updated Activity'));
					redirect(get_url('plugin/group_activity'.((is_array($_POST['id']))?'/approve':'')));
					}
				else	// If approved-setting has unallowed values or an error occured during the activity-update
					{
					Flash::set('error', __('Couldn\'t update Activity!'));
					redirect(get_url('plugin/group_activity/approve'.(count($_POST['id'])==1)?'/'.$_POST['id'][0]:''));
					}
				}
			}
		else	// If user isn't admin
			{
			redirect(get_url('plugin/group_activity'));
			}
		}
	
	public function attend($id)
		{
		if(!is_numeric($id))	// If Parameter is missing or isn't numeric
			{
			redirect(get_url('plugin/group_activity'));
			}
		else	// If Parameter is set and numeric
			{
			$event = GroupActivityEvent::findByIdFrom('GroupActivityEvent', $id);	// Get activity-data of selected id
			if($event->status==0 && $event->user!=AuthUser::getId() && !AuthUser::hasPermission('group_activity_admin'))	// If user hasn't the right to view or attend to the activity (means: activity isn't published and user didn't create the activity // activity with given id doesn't exist)
				{
				redirect(get_url('plugin/group_activity'));
				}
			}
		if($event->dateFrom > time())
			{
			if(GroupActivityEventMisc::attend($id))	// Check if attend-status successfully changed
				Flash::set('success', __('Successfully changed your attending-state.'));
			else
				Flash::set('error', __('An Error occurred!'));
			}
		redirect(get_url('plugin/group_activity/show/'.$id));
		}

    function settings()	// !!! TODO !!!
		{	
        /** You can do this...
        $tmp = Plugin::getAllSettings('skeleton');
        $settings = array('my_setting1' => $tmp['setting1'],
                          'setting2' => $tmp['setting2'],
                          'a_setting3' => $tmp['setting3']
                         );
        $this->display('comment/views/settings', $settings);
         *
         * Or even this...
         */
        $this->display('group_activity/views/settings', Plugin::getAllSettings('group_activity'));
    	}

	function _validateActivity($_POST)
		{
		$errors = array();
		use_helper('Validate');	// load helper
		//VALIDATE Post-Vars
		if(!isset($_POST['activityName']) || empty($_POST['activityName']))	// Validate data, retrieved of the submitted form
			$errors[] = 'Name';	// every errorList-array-element gets inserted into i18n-magic-quotes

		if(!isset($_POST['uid']) || empty($_POST['uid']) || !is_numeric($_POST['uid']) || !User::findById($_POST['uid']))
			$errors[] = 'User';
			
		if(isset($_POST['dateFromD']) && isset($_POST['dateFromM']) && isset($_POST['dateFromY']))	// Check if full date was submitted
			{
			$dateFrom = $_POST['dateFromY'].'-'.$_POST['dateFromM'].'-'.$_POST['dateFromD'];	// Prepare submitted data for strtotime()-function
			if(!Validate::date($dateFrom) || date('m', strtotime($dateFrom)) != $_POST['dateFromM'])	// Check date-format and if it's a possible date (if a higher day than possible in selected month got selected: strtotime() returns the next month)
				$errors[] = 'Date (From)';
			}
		else
			$errors[] = 'Date (From)';
			
		if(isset($_POST['dateToD']) && isset($_POST['dateToM']) && isset($_POST['dateToY']))	// Check if full date was submitted
			{
			$dateTo = $_POST['dateToY'].'-'.$_POST['dateToM'].'-'.$_POST['dateToD'];	// Prepare submitted data for strtotime()-function
			if(!Validate::date($dateTo) || date('m', strtotime($dateTo)) != $_POST['dateToM'])	// Check date-format and if it's a possible date (if a higher day than possible in selected month got selected: strtotime() returns the next month)
				$errors[] = 'Date (To)';
			}
		else
			$errors[] = 'Date (To)';
			
		if(strtotime($dateTo) < strtotime($dateFrom))	// Check if the from-date is before the to-date
			$errors[] = 'Date';
			
		if(!isset($_POST['location']) || empty($_POST['location']))
			$errors[] = 'Location';
			
		if(!isset($_POST['info']) || empty($_POST['info']))
			$errors[] = 'Info';
			
		if(!isset($_POST['state']) || !is_numeric($_POST['state']) || $_POST['state']<0 || $_POST['state']>2)
			$errors[] = 'State';
			
		if(!isset($_POST['approve']) || !is_numeric($_POST['approve']) || $_POST['approve']<0 || $_POST['approve']>1)	// Validate submitted approve-setting
			if(AuthUser::hasPermission('group_activity_admin'))
				$errors[] = 'Approve';	// only print error if user has admin-rights (no-admins can't set this value)
		//VALIDATE - END
		return $errors;
		}
}