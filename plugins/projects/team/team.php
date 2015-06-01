<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Projects team
 */
class plgProjectsTeam extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Store redirect URL
	 *
	 * @var	   string
	 */
	protected $_referer = NULL;

	/**
	 * Store output message
	 *
	 * @var	   array
	 */
	protected $_message = NULL;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var	   array
	 */
	protected $_msg = NULL;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas($alias = NULL)
	{
		$area = array(
			'name'    => 'team',
			'title'   => Lang::txt('PLG_PROJECTS_TEAM_TAB_TEAM'),
			'submenu' => NULL,
			'show'    => true
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param      object  $model 		Project
	 * @param      integer &$counts
	 * @return     array   integer
	 */
	public function &onProjectCount( $model )
	{
		$database = JFactory::getDBO();

		$counts['team'] = count($model->team());

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      object  $model           Project model
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onProject ( $model, $action = '', $areas = null )
	{
		$returnhtml = true;

		$arr = array(
			'html'     =>'',
			'metadata' =>'',
			'msg'      =>'',
			'referer'  =>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}

		// Check that project exists
		if (!$model->exists())
		{
			return $arr;
		}

		// Check authorization
		if (!$model->access('member'))
		{
			return $arr;
		}

		// Model
		$this->model = $model;

		// Are we returning HTML?
		if ($returnhtml)
		{
			// Set vars
			$this->_task 		= $action ? $action : Request::getVar('action','');
			$this->_database 	= JFactory::getDBO();
			$this->_uid 		= User::get('id');
			$this->_config      = $model->config();

			switch ($this->_task)
			{
				case 'edit':
				case 'setup':
					$arr['html'] = $this->display( 1 );
					break;

				case 'delete':
				case 'removeowner':
					$arr['html'] = $this->delete();
					break;

				case 'changerole':
				case 'assignrole':
					$arr['html'] = $this->_changeRole();
					break;

				case 'save':
					$arr['html'] = $this->_save();
					break;

				case 'quit':
					$arr['html'] = $this->_quit();
					break;

				case 'view':
				default:
					$arr['html'] = $this->display();
					break;

				case 'select':
				case 'newauthor':
					$arr['html'] = $this->select();
					break;

				case 'editauthors':
				case 'saveauthors':
					$arr['html'] = $this->_publicationAuthors();
					break;
			}
		}

		$arr['referer'] = $this->_referer;
		$arr['msg']     = $this->_message;

		// Return data
		return $arr;
	}

	/**
	 * Event call to get side content for main project page
	 * Will show recent visits and who is online
	 *
	 * @return
	 */
	public function onProjectMiniList($model)
	{
		if (!$model->exists() || !$model->access('content'))
		{
			return false;
		}

		// Get team
		$team = $model->team();

		// Display team visits only on multiple-member projects
		if (count($team) == 1)
		{
			return false;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'team',
				'name'    => 'mini'
			)
		);
		$view->team  = $team;
		$view->model = $model;
		return $view->loadTemplate();
	}

	/**
	 * Event call to get content for public project page
	 *
	 * @return
	 */
	public function onProjectPublicList($model)
	{
		if (!$model->exists() || !$model->access('content') || !$model->isPublic())
		{
			return false;
		}
		if (!$model->params->get('team_public', 0))
		{
			return false;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'team',
				'name'    => 'view',
				'layout'  => 'horizontal'
			)
		);

		// Get team
		$view->team  = $model->team($filters = array('status' => 1));
		$view->model = $model;
		return $view->loadTemplate();
	}

	/**
	 * View team
	 *
	 * @param      integer  	$edit
	 *
	 * @return     string
	 */
	public function display( $edit = 0 )
	{
		$layout = $edit ? 'edit' : 'view';
		$setup  = $this->_task == 'setup' ? 1 : 0;

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'   =>'projects',
				'element'  =>'team',
				'name'     => $layout
			)
		);

		$view->total = count($this->model->team());

		// Instantiate project owner
		$view->filters['limit']    = Request::getInt('limit', Config::get('list_limit'));
		$view->filters['start']    = Request::getInt( 'limitstart', 0);
		$view->filters['sortby']   = Request::getVar( 'sortby', 'name');
		$view->filters['sortdir']  = Request::getVar( 'sortdir', 'ASC');
		$view->filters['status']   = Request::getVar( 'status', 'active');
		if (!$edit)
		{
			$view->filters['online']   = 1;
		}

		// Get all active team members
		$view->team = $this->model->team($view->filters, true);

		// Get managers count
		$view->managers_count = count($this->model->table('Owner')->getIds($this->model->get('id'), $role = 1));

		// Get count of project groups
		$groups = $this->model->table('Owner')->getProjectGroups( $this->model->get('id') );
		$view->count_groups = $groups ? count($groups) : 0;

		$view->params 		= $this->model->params;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->model 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->setup 		= $setup;
		$view->config 		= $this->_config;
		$view->task 		= $this->_task;
		$view->edit 		= $edit;
		$view->title		= $this->_area['title'];

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Browser within publications NEW
	 *
	 * @return     string
	 */
	public function select()
	{
		// Incoming
		$props  = Request::getVar( 'p', '' );
		$ajax   = Request::getInt( 'ajax', 0 );
		$pid    = Request::getInt( 'pid', 0 );
		$vid    = Request::getInt( 'vid', 0 );

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0]) && in_array($parts[0], array('content', 'extras'))) ? $parts[0] : 'authors';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;

		$layout = $this->_task == 'newauthor' || $this->model->isProvisioned() ? 'newauthor' : 'default';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'team',
				'name'		=>'selector',
				'layout'	=> $layout
			)
		);

		$view->publication = new \Components\Publications\Models\Publication( $pid, NULL, $vid );

		if (!$view->publication->exists())
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_NO_PUBID'));
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'files',
					'name'		=>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( $this->getError() );
			return $view->loadTemplate();
		}

		// Set curation
		$view->publication->setCuration();

		// Make sure block exists, else use default
		if (!$view->publication->_curationModel->setBlock( $block, $step ))
		{
			$block = 'authors';
		}

		// Get css
		if (!$ajax)
		{
			\Hubzero\Document\Assets::addPluginStylesheet('projects', 'team','selector');
		}

		// Instantiate project owner
		$objO = $this->model->table('Owner');
		$view->filters['limit']    		=  0;
		$view->filters['start']    		= Request::getInt( 't_limitstart', 0);
		$view->filters['sortby']   		= Request::getVar( 't_sortby', 'name');
		$view->filters['sortdir']  		= Request::getVar( 't_sortdir', 'ASC');
		$view->filters['status']   		= 'active';
		$view->filters['pub_versionid'] = $vid;

		// Get all active team members
		$view->team = $objO->getOwners($this->model->get('id'), $view->filters);

		// Get current authors
		$pa = $view->publication->table('Author');
		$view->authors = $pa->getAuthors($vid);

		// Exclude any owners?
		$view->exclude = array();

		$view->mc = Event::trigger( 'hubzero.onGetSingleEntry', array(array('members', 'uid', 'uid')) );

		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->model 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->ajax			= $ajax;
		$view->task			= $this->_task;
		$view->block		= $block;
		$view->step 		= $step;
		$view->props		= $props;

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Save member
	 *
	 * @return     void, redirect
	 */
	protected function _save()
	{
		// Incoming
		$members 	= urldecode(trim(Request::getVar( 'newmember', '', 'post'  )));
		$groups 	= urldecode(trim(Request::getVar( 'newgroup', '' )));
		$role 		= Request::getInt( 'role', 0 );

		// Result collectors
		$m_added 	= 0; // count of individual members added
		$m_invited 	= 0; // count of individuals invited
		$g_added 	= 0; // count of members from new group
		$uids 		= array(); // ids/emails of added people
		$names 		= array(); // names/emails of added people
		$invalid 	= array(); // collector for invalid names

		// Setup stage?
		$setup = $this->model->inSetup();

		// Get owner class
		$objO = $this->model->table('Owner');

		// Instantiate a new registration object
		include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'registration.php');
		$xregistration = new MembersModelRegistration();

		// Owner names not supplied
		if (!$members && !$groups)
		{
			if (!$setup)
			{
				$this->setError( Lang::txt('PLG_PROJECTS_TEAM_NO_NAMES_SUPPLIED') );
			}
		}
		else
		{
			if ($members)
			{
				$newm = explode(',', $members);

				// Do we have new authors?
				if ($newm)
				{
					for ($i=0, $n=count( $newm ); $i < $n; $i++)
					{
						$cid = strtolower(trim($newm[$i]));
						$uid = 0;
						if ($cid == '')
						{
							continue;
						}
						$parts =  preg_split("/[(]/", $cid);
						if (count($parts) == 2)
						{
							$name = $parts[0];
							$uid = preg_replace('/[)]/', '', $parts[1]);
						}
						elseif (intval($cid) && $validUser = User::getInstance( $cid ))
						{
							$uid = $cid;
						}
						else
						{
							$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
							if (preg_match($regex, $cid))
							{
								// This is an email - check if user with the email exists
								$uid = $xregistration->getEmailId($cid);
								if (!$uid)
								{
									// Make sure we aren't inviting twice
									$invitee = $objO->checkInvited($this->model->get('id'), $cid);
									if (!$invitee)
									{
										// Generate invitation code
										$code = \Components\Projects\Helpers\Html::generateCode();

										// Add invitee record
										if ($objO->saveInvite ($this->model->get('id'), $cid, $code, '', $role))
										{
											$uids[] = $cid;
											$m_invited++;
											if (!$setup && $this->_config->get('messaging') == 1)
											{
												$this->sendInviteEmail(0, $cid, $code, $role);
											}
										}
									}
									elseif ($objO->load($invitee))
									{
										// Previously deleted invite
										if ($objO->status == 2)
										{
											$objO->status = 0;
											$objO->role = $role;
											$uids[] = $cid;
											$objO->store();
											$m_invited++;
											if (!$setup && $this->_config->get('messaging') == 1)
											{
												$this->sendInviteEmail(0, $cid, $objO->invited_code, $objO->role);
											}
										}
									}
								}
							}
							else
							{
								$invalid[] = $cid;
							}
						}

						if (!$uid or !is_numeric($uid))
						{
							continue;
						}
						else
						{
							if (!User::getInstance( $uid ))
							{
								$invalid[] = $uid;
								continue;
							}
						}

						// Save new author
						$native = ($this->model->access('owner')) ? 1 : 0;
						if ($objO->saveOwners($this->model->get('id'), $this->_uid, $uid,
							0, $role, $status = 1, $native))
						{
							$uids[] = $uid;
						}
					}
				}
			}
			if ($groups)
			{
				// Save new authors from group
				$g_added = $objO->saveOwners($this->model->get('id'), $this->_uid, 0, $groups, $role, $status = 1, $native = 0);
				if ($objO->getError())
				{
					$this->setError($objO->getError());
				}

				if ($g_added)
				{
					$uids = array_merge($uids, $g_added);
				}
			}
		}

		// Did we add anyone new?
		$uids = array_unique($uids);
		if (count($uids) > 0)
		{
			$this->_msg = Lang::txt('PLG_PROJECTS_TEAM_SUCCESS_ADDED_OR_INVITED') . ' ' . count($uids) . ' ' . Lang::txt('PLG_PROJECTS_TEAM_NEW') . ' ' . Lang::txt('PLG_PROJECTS_TEAM_TEAM_MEMBERS');

			if (count($invalid) > 0)
			{
				$this->_msg .= '<br />' . Lang::txt('PLG_PROJECTS_TEAM_TEAM_MEMBERS_INVALID_NAMES');
			}

			if (!$setup) {

				$note  = strtolower(Lang::txt('PLG_PROJECTS_TEAM_SUCCESS_ADDED_OR_INVITED')) . ' ';
				for ( $i=0; $i< count($uids); $i++)
				{
					$uu = $uids[$i];
					if ($uu && is_numeric($uu))
					{
						$xuser = User::getInstance( $uids[$i] );
						$note .= is_numeric($uids[$i]) && is_object($xuser)
							? $xuser->get('name') : $uids[$i];
					}
					else
					{
						$note  .= $uids[$i];
					}

					if ($i > 1)
					{
						$left = count($uids) - 3;
						if ($left)
						{
							$note .= ' '.Lang::txt('PLG_PROJECTS_TEAM_AND') . ' ' . $left . ' ' . Lang::txt('PLG_PROJECTS_TEAM_MORE') . ' ';
							$note .= $left == 1 ? Lang::txt('PLG_PROJECTS_TEAM_ACTIVITY_PERSON')
								: Lang::txt('PLG_PROJECTS_TEAM_ACTIVITY_PERSONS');
						}
						break;
					}
					$note  .= $i == (count($uids) - 1) ? '' : ', ';
				}
				$note .= ' ' . Lang::txt('PLG_PROJECTS_TEAM_TO_PROJECT_TEAM');

				// Send out emails
				if ($this->_config->get('messaging') == 1)
				{
					foreach ($uids as $user)
					{
						$this->sendInviteEmail( $user, '', '', $role );
					}
				}
			}

			// Sync with system group
			$objO->sysGroup($this->model->get('alias'), $this->_config->get('group_prefix', 'pr-'));
		}
		elseif (count($invalid) > 0)
		{
			 $this->setError(Lang::txt('PLG_PROJECTS_TEAM_TEAM_MEMBERS_INVALID_NAMES')
				. '<br />' . Lang::txt('PLG_PROJECTS_TEAM_TEAM_MEMBERS_INVALID_NAMES_EXPLAIN'));
		}

		// Pass success or error message
		if ($this->getError())
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		$url = $setup
				? Route::url($this->model->link('setup') . '&section=team')
				: Route::url($this->model->link('edit') . '&section=team');
		$this->_referer = $url;
		return;
	}

	/**
	 * Delete member(s)
	 *
	 * @return     void, redirect
	 */
	protected function delete()
	{
		// Incoming
		$checked = Request::getVar( 'owner', '', 'request', 'array' );
		$groups  = Request::getVar( 'group', '', 'request', 'array' );

		// Are we setting up project?
		$setup = $this->model->inSetup() ? 1 : 0;

		// Instantiate project owner
		$objO = $this->model->table('Owner');

		if (!empty($checked))
		{
			// Get owners info from owner id(s)
			$selected = $objO->getInfo($this->model->get('id'), $checked, $groups);
		}
		else
		{
			$selected = array();
			$this->setError(Lang::txt('PLG_PROJECTS_TEAM_ERROR_NOONE_TO_DELETE'));
		}

		if ($this->_task == 'delete')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder' =>'projects',
					'element'=>'team',
					'name'   =>'delete'
				)
			);

			$view->selected 	= $selected;
			$view->checked 		= $checked;
			$view->option 		= $this->_option;
			$view->model 		= $this->model;
			$view->uid 			= $this->_uid;
			$view->setup 		= $setup;
			$view->aid 			= $objO->getOwnerID($this->model->get('id'), $this->_uid);
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		else
		{
			// Get all managers
			$all = $objO->getIds($this->model->get('id'), $role = 1);
			$remaining = array_diff($all, $checked);
			$deleted = 0;

			// Cannot delete if no managers remain
			if ($remaining)
			{
				// Perform delete
				$deleted = $objO->removeOwners ($this->model->get('id'), $checked, 1);
				if ($deleted)
				{
					$this->_msg = Lang::txt('PLG_PROJECTS_TEAM_OWNERS_DELETED');
				}
			}
			else
			{
				if (count($all) > 0)
				{
					$left = array_diff($checked, array($all[0])); // leave one manager
					$deleted = $objO->removeOwners ($this->model->get('id'), $left, 1);
				}
				$this->setError( Lang::txt('PLG_PROJECTS_TEAM_OWNERS_DELETE_NOMANAGERS') );
			}

			if ($deleted)
			{
				// Sync with system group
				$objO->sysGroup($this->model->get('alias'), $this->_config->get('group_prefix', 'pr-'));
			}

			// Pass success or error message
			if ($this->getError())
			{
				$this->_message = array('message' => $this->getError(), 'type' => 'error');
			}
			elseif (isset($this->_msg) && $this->_msg)
			{
				$this->_message = array('message' => $this->_msg, 'type' => 'success');
			}

			$url = 'index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&task=';
			$url .= $setup ? 'setup' : 'edit';
			$url .= '&active=team';
			$this->_referer = Route::url($url);
			return;
		}
	}

	/**
	 * Quit project
	 *
	 * @return     void, redirect
	 */
	protected function _quit()
	{
		$html = '';

		// Incoming
		$confirm = Request::getInt( 'confirm', 0, 'post' );

		// Instantiate project owner
		$objO = $this->model->table('Owner');

		// Check to make sure we are not deleting last manager
		$onlymanager = 0;
		if ($this->model->access('manager'))
		{
			$managers = $objO->getIds($this->model->get('id'), $role = 1);
			if (count($managers) == 1)
			{
				$onlymanager = 1;
			}
		}

		if ($confirm && !$onlymanager)
		{
			$deleted = $objO->removeOwners($this->model->get('id'), array($this->_uid));
			if ($deleted)
			{
				$this->_msg = Lang::txt('PLG_PROJECTS_TEAM_TEAM_MEMBER_QUIT_SUCCESS');

				// Record activity
				$aid = $this->model->recordActivity(
					Lang::txt('PLG_PROJECTS_TEAM_TEAM_PROJECT_QUIT'), 0, '', '', 'team', 0
				);

				// Sync with system group
				$objO->sysGroup($this->model->get('alias'), $this->model->config('group_prefix', 'pr-'));
			}
		}
		else
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'team',
					'name'    =>'quit'
				)
			);

			$objO->loadOwner($this->model->get('id'), $this->_uid);
			$view->group 		= $objO->groupid;
			$view->onlymanager 	= $onlymanager;
			$view->option 		= $this->_option;
			$view->database 	= $this->_database;
			$view->model 		= $this->model;
			$view->uid 			= $this->_uid;
			$view->config 		= $this->_config;
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';
			$view->title		= $this->_area['title'];
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}

		// Pass success or error message
		if ($this->getError())
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		$this->_referer = Route::url('index.php?option=' . $this->_option);
		return;
	}

	/**
	 * Change member role
	 *
	 * @return     void, redirect
	 */
	protected function _changeRole()
	{
		// Incoming
		$checked 	= Request::getVar( 'owner', '', 'request', 'array' );
		$groups 	= Request::getVar( 'group', '', 'request', 'array' );
		$owner 		= Request::getVar( 'owner', '');
		$role 		= Request::getInt ( 'role', 0 );

		if ($owner)
		{
			$checked = array($owner);
		}

		// Are we setting up project?
		$s = $this->model->inSetup() ? 1 : 0;

		// Instantiate project owner
		$objO = $this->model->table('Owner');

		if (!empty($checked))
		{
			// Get owners info from owner id(s)
			$selected = $objO->getInfo($this->model->get('id'), $checked, $groups);
		}
		else
		{
			$selected = array();
			$this->setError(Lang::txt('PLG_PROJECTS_TEAM_ERROR_NOONE_TO_REASSIGN_ROLE'));
		}

		if ($this->_task == 'changerole')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'team',
					'name'    =>'role'
				)
			);

			$view->selected 	= $selected;
			$view->checked 		= $checked;
			$view->option 		= $this->_option;
			$view->model 		= $this->model;
			$view->uid 			= $this->_uid;
			$view->aid 			= $objO->getOwnerID($this->model->get('id'), $this->_uid);
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		else
		{
			// Changing role(s) to collaborator
			$left = $checked;
			if ($role == 0)
			{
				// Get all managers
				$all = $objO->getIds($this->model->get('id'), 1);
				$remaining = array_diff($all, $checked);
				if (!$remaining && count($all) > 0)
				{
					$left = array_diff($checked, array($all[0])); // leave one manager
					$this->setError(Lang::txt('PLG_PROJECTS_TEAM_OWNERS_REASSIGN_NOMANAGERS'));
				}
			}

			if ($objO->reassignRole ( $this->model->get('id'), $left, 1, $role))
			{
				$this->_msg = Lang::txt('PLG_PROJECTS_TEAM_OWNERS_ROLE_CHANGED');

				// Sync with system group
				$objO->sysGroup($this->model->get('alias'), $this->_config->get('group_prefix', 'pr-'));
			}

			$this->_task = $s ? 'setup' : 'edit';
			return $this->display ( $edit = 1 );
		}
	}

	//----------------------------------------
	// Messaging
	//----------------------------------------

	/**
	 * Send invite email
	 *
	 * @param  integer 	$uid
	 * @param  string 	$email
	 * @param  string 	$code
	 * @param  integer 	$role
	 * @param  object 	$project
	 *
	 * @return boolean True on success
	 */
	public function sendInviteEmail(
		$uid = 0, $email = '', $code = '',
		$role = 0, $model = '', $option = ''
	)
	{
		$uid   = $uid ? $uid : User::get('id');
		$email = $email ? $email : User::get('email');

		if (!$email || (!$uid && !$code))
		{
			return false;
		}

		$option  = $option ? $option : $this->_option;
		$model   = $model ? $model : $this->model;

		if (!$model->exists())
		{
			return false;
		}

		$database = JFactory::getDBO();

		// Validate email
		$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
		if (!preg_match($regex, $email))
		{
			return false;
		}

		// Set up email config
		$from = array();
		$from['name']  = Config::get('sitename') . ' ' . Lang::txt(strtoupper($option));
		$from['email'] = Config::get('mailfrom');

		// Email message subject
		if ($model->isProvisioned())
		{
			$objPub = new \Components\Publications\Tables\Publication($database);
			$pub 	= $objPub->getProvPublication($model->get('id'));

			if (!$pub || !$pub->id)
			{
				return false;
			}

			$subject 	= $uid
						? Lang::txt('PLG_PROJECTS_TEAM_EMAIL_SUBJECT_ADDED_PROV')
						: Lang::txt('PLG_PROJECTS_TEAM_EMAIL_SUBJECT_INVITE_PROV');
		}
		else
		{
			$subject = $uid
					? Lang::txt('PLG_PROJECTS_TEAM_EMAIL_SUBJECT_ADDED') . ' ' . $model->get('alias')
					: Lang::txt('PLG_PROJECTS_TEAM_EMAIL_SUBJECT_INVITE') . ' ' . $model->get('alias');
		}

		// Message body for HUB user
		$eview = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'team',
				'name'		=>'emails',
				'layout'	=>'invite_plain'
			)
		);

		$eview->option 			= $option;
		$eview->model 		    = $model;
		$eview->code 			= $code;
		$eview->email 			= $email;
		$eview->uid			    = $uid;
		$eview->role			= $role;
		$eview->pub 			= isset($pub) ? $pub : '';
		$eview->delimiter  		= '';

		$message['plaintext'] 	= $eview->loadTemplate();
		$message['plaintext'] 	= str_replace("\n", "\r\n", $message['plaintext']);

		// HTML email
		$eview->setLayout('invite_html');
		$message['multipart'] 	= $eview->loadTemplate();
		$message['multipart'] 	= str_replace("\n", "\r\n", $message['multipart']);

		if ($uid)
		{
			// Send HUB message
			if (Event::trigger( 'xmessage.onSendMessage', array( 'projects_member_added',
				$subject, $message, $from,
				array($uid), $option ))) {
				return true;
			}
		}
		else
		{
			if (\Components\Projects\Helpers\Html::email($email, Config::get('sitename') . ': ' . $subject, $message, $from))
			{
				return true;
			}
		}

		return false;
	}
}