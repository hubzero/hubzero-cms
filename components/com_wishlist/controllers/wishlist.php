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
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wishlist controller class
 */
class WishlistController extends JObject
{
	/**
	 * Description for '_name'
	 * 
	 * @var string
	 */
	private $_name  = NULL;

	/**
	 * Description for '_data'
	 * 
	 * @var array
	 */
	private $_data  = array();

	/**
	 * Description for '_task'
	 * 
	 * @var string
	 */
	private $_task  = NULL;

	/**
	 * Description for '_error'
	 * 
	 * @var unknown
	 */
	private $_error = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $config Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($config=array())
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';

		// Set the controller name
		if (empty($this->_name)) 
		{
			if (isset($config['name'])) 
			{
				$this->_name = $config['name'];
			} 
			else 
			{
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) 
				{
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower($r[1]);
			}
		}

		// Set the component name
		$this->_option = 'com_' . $this->_name;
	}

	/**
	 * Short description for 'setVar'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function setVar($property, $value)
	{
		$this->$property = $value;
	}

	/**
	 * Short description for 'getVar'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getVar($property)
	{
		return $this->$property;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	/**
	 * Short description for 'getTask'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function getTask()
	{
		$task = JRequest::getVar('task', '', 'post');
		if (!$task) 
		{
			$task = JRequest::getVar('task', '', 'get');
		}
		$this->_task = $task;

		return $task;
	}

	/**
	 * Determine task and execute
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function execute()
	{
		// Load the component config
		$component =& JComponentHelper::getComponent($this->_option);
		if (!trim($component->params)) 
		{
			return $this->abort();
		} 
		else 
		{
			$config =& JComponentHelper::getParams($this->_option);
		}
		$this->config = $config;

		$database =& JFactory::getDBO();
		$objWishlist = new Wishlist($database);

		// Check if main wishlist exists, create one if missing
		$this->mainlist = $objWishlist->get_wishlistID(1, 'general');
		if (!$this->mainlist) 
		{
			$this->mainlist = $objWishlist->createlist('general', 1);
		}

		$this->admingroup = $this->config->get('group') ? $this->config->get('group') : 'hubadmin';

		// are we using banking functions?
		$upconfig =& JComponentHelper::getParams('com_members');
		$banking = $upconfig->get('bankAccounts');
		$this->banking = $this->config->get('banking') && $banking ? $this->config->get('banking') : 0;

		if ($banking) 
		{
			ximport('Hubzero_Bank');
		}

		switch($this->getTask())
		{
			case 'wishlist':    $this->wishlist();      break;
			case 'settings':    $this->settings();  	break;
			case 'savesettings':$this->savesettings(); 	break;
			//case 'newlist':     $this->createlist();    break;
			case 'search':      $this->wishlist();    	break;

			case 'wish':     	$this->wish();    		break;
			case 'add':     	$this->addwish();       break;
			case 'savewish':    $this->savewish();      break;
			case 'addbonus':  	$this->addbonus();  	break;
			case 'deletewish':  $this->deletewish();  	break;
			case 'withdraw':  	$this->deletewish();  	break;
			case 'movewish':    $this->movewish();  	break;
			case 'editprivacy': $this->editwish();  	break;
			case 'grantwish':   $this->editwish();  	break;
			case 'editwish':    $this->editwish();  	break;

			// Implementation Plan
			case 'saveplan':    $this->saveplan();  	break;

			// Comments and ratings
			case 'rateitem':   	$this->rateitem();    	break;
			case 'savevote':    $this->savevote();      break;
			case 'savereply':   $this->savereply();   	break;
			case 'reply':      	$this->reply();  	  	break;
			case 'deletereply': $this->deletereply();   break;

			// Autocompleter - called via AJAX
			case 'autocomplete': $this->autocomplete(); break;

			case 'upload':     $this->upload();     break;
			case 'download':     $this->download();     break;

			default: $this->wishlist(); break;
		}
	}

	/**
	 * Perform a redirect
	 * 
	 * @return     void
	 */
	public function redirect()
	{
		if ($this->_redirect != NULL) 
		{
			$app =& JFactory::getApplication();
			$app->redirect($this->_redirect, $this->_message, $this->_messageType);
		}
	}

	/**
	 * Push CSS to the document head
	 * 
	 * @param      string $option Parameter description (if any) ...
	 * @param      string $css Parameter description (if any) ...
	 * @return     void
	 */
	public function _getStyles($option='', $css='')
	{
		ximport('Hubzero_Document');
		if ($option) 
		{
			Hubzero_Document::addComponentStylesheet($option, $css);
		} 
		else 
		{
			Hubzero_Document::addComponentStylesheet($this->_option);
		}
	}

	/**
	 * Push scripts to the document head
	 * 
	 * @param      string $option Parameter description (if any) ...
	 * @param      string $name Parameter description (if any) ...
	 * @return     void
	 */
	public function _getScripts($option='',$script='')
	{
		$document = JFactory::getDocument();

		//$option = ($option) ? $option : $this->_option;
		//$script = ($script) ? $script : $this->_name;

		$path = DS . 'components' . DS . $option . DS . 'assets' . DS . 'js' . DS . $script . '.js';
		$pathAlt = null;

		$document = JFactory::getDocument();
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$pathAlt = DS . 'components' . DS . $option . DS . 'assets' . DS . 'js' . DS . $script . '.jquery.js';
		}

		if ($pathAlt && is_file(JPATH_ROOT . $pathAlt))
		{
			$document->addScript($pathAlt);
		}
		else if (is_file(JPATH_ROOT . $path))
		{
			$document->addScript($path);
		}
	}

	/**
	 * Build the page title
	 * 
	 * @return     void
	 */
	public function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_subtitle) 
		{
			$this->_title .= ' - ' . $this->_subtitle;
		}
		else if ($this->_task) 
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Build the breadcrumbs
	 * 
	 * @param      object $wishlist Wishlist
	 * @return     void
	 */
	private function _buildPathway($wishlist)
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		$comtitle  = JText::_(strtoupper($this->_option));
		$comtitle .= $this->_list_title ? ' - ' . $this->_list_title : '';

		if (count($pathway->getPathWay()) <= 0) 
		{
			$this->startPath($wishlist, $comtitle, $pathway);
		}

		if ($this->_task) 
		{
			switch ($this->_task)
			{
				case 'wish':
					$pathway->addItem(
						$this->_wishtitle,
						$this->_wishpath
					);
				break;
				case 'add':
				case 'savewish':
				case 'editwish':
					$pathway->addItem(
						$this->_taskname,
						$this->_taskpath
					);
				break;
				case 'settings':
					$pathway->addItem(
						JText::_(strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=settings&id=' . $this->_listid
					);
				break;
				case 'view':
				case 'cancel':
				case 'reply':
				case 'rateitem':
				case 'savereply':
				case 'savevote':
				case 'saveplan':
				case 'movewish':
				case 'editprivacy':
				case 'grantwish':
				case 'deletewish':
				case 'withdraw':
				case 'addbonus':
				case 'wishlist':
					// nothing
				break;

				default:
					// XSS fix, passing raw user supplied/maniuplatable data to function that creates link. See ticket 1420
					$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . htmlentities($this->_task)
					);
				break;
			}
		}
	}

	/**
	 * Short description for 'startPath'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $wishlist Parameter description (if any) ...
	 * @param      unknown $title Parameter description (if any) ...
	 * @param      object $pathway Parameter description (if any) ...
	 * @return     void
	 */
	public function startPath($wishlist, $title, $pathway) 
	{
		// build return path to resource
		if (isset($wishlist->resource) && isset($wishlist->resource->typetitle)) 
		{
			$typenorm = preg_replace("/[^a-zA-Z0-9]/", '', $wishlist->resource->typetitle);
			$typenorm = strtolower($typenorm);

			$pathway->addItem(JText::_('Resources'), 'index.php?option=com_resources');
			$pathway->addItem(ucfirst(JText::_($wishlist->resource->typetitle)), JRoute::_('index.php?option=com_resources&type='.$typenorm));
			$pathway->addItem(stripslashes($wishlist->resource->title),JRoute::_('index.php?option=com_resources&id='.$wishlist->referenceid));
			$pathway->addItem(JText::_(strtoupper($this->_name)), 'index.php?option=' . $this->_option . '&task=wishlist&category=' . $wishlist->category . '&rid='.$wishlist->referenceid);
		}
		else 
		{
			if ($wishlist->category == 'group') 
			{
				ximport('Hubzero_Group');
				$group = Hubzero_Group::getInstance($wishlist->referenceid);
				$pathway->addItem('Groups','index.php?option=com_groups');
				$pathway->addItem($group->get('description'), 'index.php?option=com_groups&cn='.$group->get('cn'));
				$pathway->addItem('Wishlist', 'index.php?option=com_groups&cn='.$group->get('cn').'&active=wishlist');
			} 
			else 
			{
				$pathway->addItem($title, 'index.php?option=' . $this->_option . '&task=wishlist&category=' . $wishlist->category . '&rid='.$wishlist->referenceid);
			}
		}
	}

	/**
	 * Display an error message if the wishlist isn't configured
	 * 
	 * @return     void
	 */
	protected function abort()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)));

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}

		$this->setError(JText::_('Wishlist not configured.'));

		// Output HTML
		$view = new JView(array('name'=>'error'));
		$view->title = JText::_(strtoupper($this->_name));
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Display a login form
	 * 
	 * @return     void
	 */
	public function login()
	{
		if (JFactory::getUser()->get('guest')) 
		{
			$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->getTask(), false, true), 'server'));
			JFactory::getApplication()->redirect(
				JRoute::_('index.php?option=com_login&return=' . $return),
				$this->_msg,
				'warning'
			);
			return;
		}
	}

	/**
	 * Show a list of entries for this list
	 * 
	 * @return     void
	 */
	public function wishlist()
	{
		ximport('Hubzero_Group');

		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Incoming
		$id 	= JRequest::getInt('id', 0);
		$refid  = JRequest::getInt('rid', 0);
		$cat   	= JRequest::getVar('category', '');
		$saved  = JRequest::getInt('saved', 0);

		if (empty($id) && empty($cat) && empty($refid)) 
		{
			$id = $this->mainlist;
		}

		// are we viewing this from within a plugin?
		$plugin = (isset($this->plugin) && $this->plugin!='') ? $this->plugin : '';
		$id = ($this->listid && !$id)  ? $this->listid : $id;

		$obj = new Wishlist($database);

		if ($this->category && $this->refid && !$id) 
		{
			$cat = $this->category;
			$refid = $this->refid;
		}

		$cats   = $this->config->get('categories');
		$cats   = $cats ? $cats : 'general, resource';
		if ($cat && !preg_replace("/" . $cat . "/", '', $cats) && !$plugin) 
		{
			// oups, this looks like a wrong URL
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Create a new list
		if (!$id && $refid) 
		{
			$id = $obj->get_wishlistID($refid, $cat);

			// Is this a list for an existing resource?
			if (!$id && $cat == 'resource') 
			{
				// get resource title
				$resource = new ResourcesResource($database);
				$resource->load ($refid);

				if ($resource->title && $resource->standalone == 1  && $resource->published == 1) 
				{
					$rtitle = ($resource->type=='7'  && isset($resource->alias)) ? JText::_('COM_WISHLIST_NAME_RESOURCE_TOOL').' '.$resource->alias : JText::_('COM_WISHLIST_NAME_RESOURCE_ID').' '.$resource->id;
					$id = $obj->createlist($cat, $refid, 1, $rtitle, $resource->title);
				}
			}
			else if (!$id && $cat == 'user') 
			{
				// create private list for user
				$id = $obj->createlist($cat, $refid, 0, JText::_('COM_WISHLIST_NAME_MY_WISHLIST'));
			}
			else if (!$id && $cat == 'group') 
			{
				// create private list for group
				if (Hubzero_Group::exists($refid)) 
				{
					$group = Hubzero_Group::getInstance($refid);
					$id = $obj->createlist($cat, $refid, 0, $group->cn.' '.JText::_('COM_WISHLIST_NAME_GROUP'));
				}
			}
		}

		// cannot find this list
		if (!$id) 
		{
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $refid, $cat);

		if (!$wishlist) 
		{
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		else 
		{
			// remember list id for plugin use
			$this->listid = isset($this->listid) ? $this->listid : $id;

			// get admin priviliges
			self::authorize_admin();

			// Add the CSS to the template
			self::_getStyles();

			// Thumbs voting CSS & JS
			self::_getStyles('com_answers', 'vote.css');

			// Push some scripts to the template
			self::_getScripts();

			// who are list owners?
			$objOwner = new WishlistOwner($database);
			$objG 	  = new WishlistOwnerGroup($database);
			$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);
			$wishlist->owners = $owners['individuals'];
			$wishlist->groups = $owners['groups'];
			$wishlist->advisory = $owners['advisory'];

			// Authorize list owners
			if (!$juser->get('guest')) 
			{
				if (in_array($juser->get('id'), $wishlist->owners)) 
				{
					$this->_admin = 2;
				}
				else if (in_array($juser->get('id'), $wishlist->advisory)) 
				{
					$this->_admin = 3;
				}
			}

			// Set page title
			$this->_list_title = ($wishlist->public or (!$wishlist->public && $this->_admin==2)) ? $wishlist->title : '';
			$this->_subtitle = ($wishlist->public or (!$wishlist->public && $this->_admin==2)) ? $wishlist->title : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);

			// need to log in to private list
			if (!$wishlist->public && $juser->get('guest')) 
			{
				if (!$plugin) 
				{
					$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_PRIVATE_LOGIN_REQUIRED');
					$this->login();
				}
				else 
				{
					// not authorized
					JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
					return;
				}
				return;
			}

			// Get list filters
			$filters = self::getFilters($this->_admin);
			$filters['limit'] = (isset($this->limit)) ? $this->limit : $filters['limit'];

			// Get individual wishes
			$objWish = new Wish($database);
			$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $this->_admin, $juser);
			$total = $objWish->get_count($wishlist->id, $filters, $this->_admin, $juser);

			// Get count of granted wishes
			$sp_filters = $filters;
			$sp_filters['filterby'] = 'granted';
			$wishlist->granted_count = $objWish->get_count($wishlist->id, $sp_filters, $this->_admin, $juser);
			$wishlist->granted_percentage = $total > 0 && $wishlist->granted_count > 0 ?  round(($wishlist->granted_count/$total) * 100, 0) : 0;

			// Some extras
			$wishlist->saved = $saved;
			$wishlist->banking = $this->banking ? $this->banking : 0;
			$wishlist->banking = $wishlist->category=='user' ? 0 : $this->banking; // do not allow points for individual wish lists		
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $filters['start'], $filters['limit']);

		JRequest::setVar('id', $id);

		$view = new JView(array('name'=>'wishlist' , 'base_path' => JPATH_ROOT . DS . 'components' . DS . $this->_option));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->admin = $this->_admin;
		$view->juser = $juser;
		$view->pageNav = $pageNav;
		$view->wishlist = $wishlist;
		$view->filters = $filters;
		$view->database = $database;
		$view->abuse = true;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Show an entry and associated content
	 * 
	 * @return     void
	 */
	public function wish()
	{
		$database   =& JFactory::getDBO();
		$juser 		=& JFactory::getUser();

		$wishid  	= JRequest::getInt('wishid', 0);
		$id  		= JRequest::getInt('id', 0);
		$refid  	= JRequest::getInt('rid', 0);
		$cat   		= JRequest::getVar('category', '');
		$action     = JRequest::getVar('action', '');
		$com   		= JRequest::getInt('com', 0, 'get');
		$canedit 	= false;
		$saved  	= JRequest::getInt('saved', 0);

		$wishid = $this->wishid && !$wishid ? $this->wishid : $wishid;

		// Get data			
		$obj = new Wishlist($database);
		$objWish = new Wish($database);
		$wish = $objWish->get_wish ($wishid, $juser->get('id'), $refid, $cat);

		if (!$wish) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		// Get wishlist info
		$wishlist = $obj->get_wishlist($wish->wishlist, $refid, $cat);
		if (!$wishlist) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}
		else 
		{
			// get admin priviliges
			$this->authorize_admin();

			// who are list owners?
			$objOwner = new WishlistOwner($database);
			$objG 	  = new WishlistOwnerGroup($database);
			$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);
			$wishlist->owners 	= $owners['individuals'];
			$wishlist->advisory = $owners['advisory'];
			$wishlist->groups 	= $owners['groups'];

			// Set page title
			$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7' && isset($wishlist->resource->alias))
							? 'tool "' . $wishlist->resource->alias . '"'
							: $wishlist->title;
			if (!$wishlist->public && !$this->_admin) 
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_wishpath  = 'index.php?option=' . $this->_option . '&task=wish&category=' . $cat . '&rid=' . $refid . '&wishid='.$wishid;
			$this->_wishtitle = Hubzero_View_Helper_Html::shortenText($wish->subject, 80, 0);
			$this->_buildPathway($wishlist);

			// Push some styles to the template
			$this->_getStyles();
			$this->_getStyles('com_answers', 'vote.css');
			$this->_getStyles('com_events', 'calendar.css');

			// Push some scripts to the template
			$this->_getScripts();

			// Go through some access checks
			if ($juser->get('guest') && $action) 
			{
				$this->_msg = ($action=="addbonus") ? JText::_('COM_WISHLIST_MSG_LOGIN_TO_ADD_POINTS') : '';
				$this->login();
				return;
			}

			if (!$wishlist->public && $juser->get('guest')) 
			{
				// need to log in to private list
				$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_PRIVATE_LOGIN_REQUIRED');
				$this->login();
				return;
			}

			if ($wish->private && $juser->get('guest')) 
			{
				// need to log in to view private wish
				$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_PRIVATE_WISH');
				$this->login();
				return;
			}

			// Authorize list owners
			if (!$juser->get('guest')) 
			{
				if (in_array($juser->get('id'), $wishlist->owners)) 
				{
					$this->_admin = 2;
				}
				else if (in_array($juser->get('id'), $wishlist->advisory)) 
				{
					$this->_admin = 3;
				}
			}

			// Deleted wish
			if ($wish->status == 2 && !$this->_admin) 
			{
				JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
				return;
			}

			if ($wish->private && !$this->_admin) 
			{
				// need to be admin to view private wish
				JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
				return;
			}

			// Get list filters		
			$filters = self::getFilters($this->_admin);

			// Get the next and previous wishes
			$wish->prev = $objWish->getWishId('prev', $wishid, $wish->wishlist, $this->_admin, $juser->get('id'), $filters);
			$wish->next = $objWish->getWishId('next', $wishid, $wish->wishlist, $this->_admin, $juser->get('id'), $filters);

			// Update average value for importance (this is tricky MySQL)
			$votesplit = $this->config->get('votesplit')  ? trim($this->config->get('votesplit')) : 0;
			if (count($wishlist->advisory) > 0 && $votesplit) 
			{
				$objR = new WishRank ($database);
				$votes = $objR->get_votes($wish->id);

				// first consider votes by list owners
				if ($votes) 
				{
					$imp 		= 0;
					$divisor 	= 0;
					$co_adv 	= 0.8;
					$co_reg 	= 0.2;

					foreach ($votes as $vote) 
					{
						if (in_array($vote->userid, $wishlist->advisory)) 
						{
							$imp += $vote->importance * $co_adv;
							$divisor +=$co_adv;
						}
						else 
						{
							$imp += $vote->importance * $co_reg;
							$divisor +=$co_reg;
						}
					}

					// weighted average 
					$wish->average_imp = $imp/$divisor;
				}
			}

			// Get comments
			$wish->replies = $this->getComments($wishid, $wishid, 'wish', 0, $abuse = true, $wishlist->owners, $this->_admin);

			// Do some text cleanup
			$wish->subject = stripslashes($wish->subject);
			$wish->subject = str_replace('&quote;', '&quot;', $wish->subject);
			$wish->subject = htmlspecialchars($wish->subject);

			$wish->about = stripslashes($wish->about);
			$wish->about = str_replace('&quote;', '&quot;', $wish->about);
			if (!strstr($wish->about, '</p>') && !strstr($wish->about, '<pre class="wiki">')) 
			{
				$wish->about = str_replace("<br />", '', $wish->about);
				$wish->about = nl2br($wish->about);
			}

			// Build owners drop-down for assigning wishes
			$wish->assignlist = $this->userSelect('assigned', $wishlist->owners, $wish->assigned, 1);

			// Do we have a due date?
			$wish->urgent = 0;
			if ($wish->due != '0000-00-00 00:00:00') 
			{
				$delivery = $this->convertTime($wish->average_effort);
				if ($wish->due < $delivery['warning']) 
				{
					$wish->urgent = 1;
				}
				if ($wish->due < $delivery['immediate']) 
				{
					$wish->urgent = 2;
				}
			}

			// check available user funds	
			if ($action == 'addbonus' && $this->banking) 
			{
				$BTL 		= new Hubzero_Bank_Teller($database, $juser->get('id'));
				$balance 	= $BTL->summary();
				$credit 	= $BTL->credit_summary();
				$funds 		= $balance - $credit;
				$funds 		= ($funds > 0) ? $funds : '0';
				$wish->funds = $funds;
			}

			if ($action == 'move') 
			{
				// what wishlist categories are we allowed to have?
				$cats   = $this->config->get('categories');
				$cats   = $cats ? $cats : 'general, resource';
				$wish->cats = $cats;

				if ($wishlist->category=='group') 
				{
					$group = Hubzero_Group::getInstance($wishlist->referenceid);
					$wishlist->cn = $group->cn;
				}
			}

			// Get implementation plan
			$objPlan = new WishlistPlan($database);
			$plan = $objPlan->getPlan($wishid);
			$wish->plan = $plan ? $plan[0] : '';

			// Record some extra actions
			$wish->action = $action;
			$wish->saved = $saved;
			$wish->com = $com;

			// Get tags on this wish
			$tagging = new WishTags($database);
			$wish->tags = $tagging->get_tags_on_object($wish->id, 0, 0, 0);

			$refid = $wishlist->referenceid;
			$cat   = $wishlist->category;
		}

		if (isset($this->comment)) 
		{
			$addcomment =& $this->comment;
		} 
		else 
		{
			$addcomment = NULL;
		}

		if ($this->_task=='reply') 
		{
			$addcomment = new Hubzero_Comment($database);
			$addcomment->referenceid = $this->referenceid;
			$addcomment->category = $this->cat;
		} 
		else 
		{
			$addcomment = NULL;
		}

		// Turn on/off banking	
		$wishlist->banking = $wishlist->category=='user' ? 0 : $this->banking;

		$view = new JView(array('name'=>'wish'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->admin = $this->_admin;
		$view->juser = $juser;
		$view->wishlist = $wishlist;
		$view->wish = $wish;
		$view->addcomment = $addcomment;
		$view->filters = $filters;
		$view->abuse = true;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Save wishlist settings
	 * 
	 * @return     void
	 */
	public function savesettings() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$listid  = JRequest::getInt('listid', 0);
		$action  = JRequest::getVar('action', '');

		// Make sure we have list id
		if (!$listid) 
		{
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
		}

		$obj = new Wishlist($database);
		$wishlist = $obj->get_wishlist($listid);

		$objOwner = new WishlistOwner($database);
		$objG 	  = new WishlistOwnerGroup($database);

		// get admin priviliges
		$this->authorize_admin($listid);

		if (!$this->_admin) 
		{
			JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
			return;
		}

		// Deeleting a user/group
		if ($action == 'delete') 
		{
			$user   = JRequest::getInt('user', 0);
			$group  = JRequest::getInt('group', 0);

			if ($user) 
			{
				$objOwner->delete_owner($listid, $user, $this->admingroup);
			}
			else if ($group) 
			{
				$objG->delete_owner_group($listid, $group, $this->admingroup);
			}

			// update priority on all wishes
			$this->listid = $listid;
			$this->rank($listid);

			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wishlist&category=' . $wishlist->category . '&rid='.$wishlist->referenceid).'?saved=1';
			return;
		}

		$_POST = array_map('trim',$_POST);

		$obj->load($listid);

		if (!$obj->bind($_POST)) 
		{
			JError::raiseError(500, $obj->getError());
			return;
		}
		$obj->description  = rtrim(stripslashes($obj->description));
		$obj->description  = Hubzero_Filter::cleanXss($obj->description);
		$obj->description  = nl2br($obj->description);

		// check content
		if (!$obj->check()) 
		{
			JError::raiseError(500, $obj->getError());
			return;
		}

		// store new content
		if (!$obj->store()) 
		{
			JError::raiseError(500, $obj->getError());
			return;
		}

		// Save new owners
		$newowners = $this->makeArray(rtrim($_POST['newowners']));
		$newgroups = $this->makeArray(rtrim($_POST['newgroups']));

		$allow_advisory = $this->config->get('allow_advisory') ? $this->config->get('allow_advisory') : 0;
		$newadvisory = $allow_advisory ? $this->makeArray(rtrim($_POST['newadvisory'])) : array();

		if (!empty($newowners)) 
		{
			$objOwner->save_owners($listid, $this->config, $newowners);
		}
		if (!empty($newadvisory)) 
		{
			$objOwner->save_owners($listid, $this->config, $newadvisory, 2);
		}
		if (!empty($newgroups)) 
		{
			$objG->save_owner_groups($listid, $this->config, $newgroups);
		}

		// update priority on all wishes
		$this->listid = $listid;
		$this->rank($listid);

		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wishlist&category=' . $wishlist->category . '&rid='.$wishlist->referenceid).'?saved=1';
	}

	/**
	 * Display wishlist settings
	 * 
	 * @return     void
	 */
	public function settings()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// get list id
		$id  = JRequest::getInt('id', 0);

		$obj = new Wishlist($database);
		$wishlist = $obj->get_wishlist($id);

		if (!$wishlist) 
		{
			// list not found
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		// Set page title
		$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7'  && isset($wishlist->resource->alias))
						? 'tool "'. $wishlist->resource->alias.'"'
						: $wishlist->title;
		if (!$wishlist->public && !$this->_admin) 
		{
			$this->_list_title = '';
		}
		$this->_buildTitle();

		// Set the pathway
		$this->_listid = $id;
		$this->_buildPathway($wishlist);

		// Login required
		if ($juser->get('guest')) 
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_MANAGE_SETTINGS');
			$this->login();
			return;
		}

		// who are list owners?
		$objOwner = new WishlistOwner($database);
		$objG 	  = new WishlistOwnerGroup($database);
		$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);
		$wishlist->owners = $owners['individuals'];
		$wishlist->groups = $owners['groups'];
		$wishlist->advisory = $owners['advisory'];

		// get admin priviliges
		$this->authorize_admin();

		// Authorize list owners
		if (!$juser->get('guest')) 
		{
			if (in_array($juser->get('id'), $wishlist->owners)) 
			{
				$this->_admin = 2;
			}
			else if (in_array($juser->get('id'), $wishlist->advisory)) 
			{
				$this->_admin = 3;
			}
		}

		$nativeowners = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist, 1);
		$wishlist->nativeowners = $nativeowners['individuals'];
		$wishlist->nativegroups = $nativeowners['groups'];

		$wishlist->allow_advisory = $this->config->get('allow_advisory') ? $this->config->get('allow_advisory') : 0;

		$view = new JView(array('name'=>'settings'));
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->admin = $this->_admin;
		$view->juser = $juser;
		$view->wishlist = $wishlist;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Save a wish's implementation plan
	 * 
	 * @return     void
	 */
	public function saveplan() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$live_site = rtrim(JURI::base(), '/');

		$wishid = JRequest::getInt('wishid', 0);

		// Make sure we have wish id
		if (!$wishid) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		$obj = new Wishlist($database);
		$objWish = new Wish($database);
		$objWish->load($wishid);

		if (!$objWish->load($wishid)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		$wishlist = $obj->get_wishlist($objWish->wishlist);

		// Login required
		if ($juser->get('guest')) 
		{
			// Set page title
			$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7'  && isset($wishlist->resource->alias))
						? 'tool "'. $wishlist->resource->alias.'"'
						: $wishlist->title;
			if (!$wishlist->public && !$this->_admin) 
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->login();
			return;
		}

		$pageid = JRequest::getInt('pageid', 0, 'post');
		$create_revision = JRequest::getInt('create_revision', 0, 'post');

		// Initiate extended database class
		$page = new WishlistPlan($database);
		if (!$pageid) 
		{
			// New page - save it to the database
			$old = new WishlistPlan($database);

		} 
		else 
		{
			// Existing page - load it up
			$page->load($pageid);

			// Get the revision before changes
			$old = $page;
		}

		$page->version = JRequest::getInt('version', 1, 'post');

		if ($create_revision) 
		{
			$page = new WishlistPlan($database);
			$page->version = $old->version + 1;
		}

		$page->wishid     = $wishid;
		$page->created_by = JRequest::getInt('created_by', $juser->get('id'), 'post');
		$page->created    = date('Y-m-d H:i:s', time());
		$page->approved   = 1;
		$page->pagetext   = rtrim($_POST['pagetext']);

		// Stripslashes just to make sure
		$old->pagetext  = rtrim(stripslashes($old->pagetext));
		$page->pagetext = rtrim(stripslashes($page->pagetext));

		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if ($old->pagetext != $page->pagetext or (!$create_revision && $pageid)) 
		{
			// Transform the wikitext to HTML
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => 'wishlist' . DS . $wishlist->id,
				'pagename' => $objWish->id,
				'pageid'   => $objWish->id,
				'filepath' => '',
				'domain'   => ''
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			$page->pagehtml = $p->parse($page->pagetext, $wikiconfig);

			// Store content
			if (!$page->store()) 
			{
				JError::raiseError(500, $page->getError());
				return;
			}
		}

		// do we have a due date?
		$isdue  = JRequest::getInt('isdue', 0);
		$due    = JRequest::getVar('publish_up', '');

		if ($due) 
		{
			$publishtime = $due.' 00:00:00';
			$due = strftime("%Y-%m-%d %H:%M:%S",strtotime($publishtime));
		}

		//is this wish assigned to anyone?
		$assignedto = JRequest::getInt('assigned', 0);

		$new_assignee = ($assignedto && $objWish->assigned != $assignedto) ? 1 : 0;

		$objWish->due = ($due) ? $due : '0000-00-00 00:00:00';
	    $objWish->assigned = ($assignedto) ? $assignedto : 0;

		// store our due date
		if (!$objWish->store()) 
		{
			JError::raiseError(500, $objWish->getError());
			return;
		}
		else if ($new_assignee) 
		{
			// Build e-mail components
			$jconfig =& JFactory::getConfig();
			$admin_email = $jconfig->getValue('config.mailfrom');

			// to wish assignee
			$subject = JText::_(strtoupper($this->_name)).', '.JText::_('COM_WISHLIST_WISH').' #'.$wishid.' '.JText::_('COM_WISHLIST_MSG_HAS_BEEN_ASSIGNED_TO_YOU');

			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
			$from['email'] = $jconfig->getValue('config.mailfrom');

			$name = JText::_('COM_WISHLIST_UNKNOWN');
			$login = JText::_('COM_WISHLIST_UNKNOWN');
			$ruser =& Hubzero_User_Profile::getInstance($objWish->proposed_by);
			if (is_object($ruser)) 
			{
				$name  = $ruser->get('name');
				$login = $ruser->get('username');
			}
			if ($objWish->anonymous) 
			{
				$name = JText::_('COM_WISHLIST_ANONYMOUS');
			}

			$message  = '----------------------------'.r.n;
			$message .= JText::_('COM_WISHLIST_WISH').' #'.$objWish->id.', '.$wishlist->title.' '.JText::_('WISHLIST').r.n;
			$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($objWish->subject).r.n;
			$message .= JText::_('COM_WISHLIST_PROPOSED_ON').' '.JHTML::_('date',$objWish->proposed, '%d %b, %Y');
			$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
			$message .= $objWish->anonymous ? '' : '('.$login.')';
			$message .= r.n.r.n;

			$message .= '----------------------------'.r.n;
			$url = $live_site.JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid);
			$message  .= JText::_('GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_YOUR_ASSIGNED_WISH').'.';

			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();

			if (!$dispatcher->trigger('onSendMessage', array('wishlist_wish_assigned', $subject, $message, $from, array($objWish->assigned), $this->_option))) 
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
			}
		}

		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid) . '#plan';
	}

	/**
	 * Display a form for creating a wish
	 * 
	 * @param      integer $wishid Wish ID
	 * @return     void
	 */
	public function addwish($wishid=0)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Incoming
		$listid 	= JRequest::getInt('id', 0);
		$refid		= JRequest::getInt('rid', 0);
		$category 	= JRequest::getVar('category', '');

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		$objWishlist = new Wishlist ($database);
		$wish = new Wish ($database);

		if (!$listid && $refid) 
		{
			if (!$category) 
			{
				JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
				return;
			}
			else 
			{
				$listid = $objWishlist->get_wishlistID($refid, $category);
			}

			// Create wishlist for resource 
			if ($category == 'resource' && !$listid) 
			{
				// check if resources exists and get  title
				$resource = new ResourcesResource($database);
				$resource->load ($refid);

				if ($resource->title && $resource->standalone == 1) 
				{
					$listid = $objWishlist->createlist($cat, $refid, 1, $resource->title);
				}
			}
		}

		if ($wishid) 
		{
			// we are editing
			$wish->load($wishid);
			$listid = $wish->wishlist;
		}

		// cannot add a wish to a non-existing list
		if (!$listid) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}
		else 
		{
			$wishlist = $objWishlist->get_wishlist($listid);
		}

		// list not found - seems to be an incorrect id
		if (!$wishlist) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Set page title
		$this->_list_title = ($wishlist->public or (!$wishlist->public && $this->_admin==2)) ? $wishlist->title : '';
		$this->_buildTitle();

		// Set the pathway
		$this->_taskpath = $wishid
							? 'index.php?option=' . $this->_option . '&task=editwish&category=' . $category . '&rid=' . $refid . '&wishid=' . $wishid
							: 'index.php?option=' . $this->_option . '&task=add&category=' . $category . '&rid=' . $refid;
		$this->_taskname = $wishid
							? JText::_('COM_WISHLIST_EDITWISH')
							: JText::_('COM_WISHLIST_ADD');
		$this->_buildPathway($wishlist);

		// Login required
		if ($juser->get('guest')) 
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_ADD');
			$this->login();
			return;
		}

		// get admin priviliges
		$this->authorize_admin($listid);

		// this is a private list, can't add to it
		if (!$wishlist->public && !$this->_admin) 
		{
			JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
			return;
		}

		// Get some defaults
		if (!$wishid) 
		{
			$wish->proposed_by 	= $juser->get('id');
			$wish->status = 0;
			$wish->anonymous  = 0;
			$wish->private = 0;
		}

		 // do not allow points for individual wish lists
		$this->banking = $wishlist->category=='user' ? 0 : $this->banking;

		// Is banking turned on?
		$funds = 0;
		if ($this->banking) 
		{
			$BTL = new Hubzero_Bank_Teller($database, $juser->get('id'));
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$funds   = $balance - $credit;
			$funds   = ($funds > 0) ? $funds : '0';
		}

		// Get URL to page explaining virtual economy
		$aconfig =& JComponentHelper::getParams('com_answers');
		$infolink = $aconfig->get('infolink', '/kb/points/');

		// Get tags on this wish
		$tagging = new WishTags($database);
		$wish->tags = $wishid ? $tagging->get_tag_string($wishid, 0, 0, NULL, 0, 1) : JRequest::getVar('tag', '');

		// Output HTML
		$view = new JView(array('name'=>'editwish'));
		$view->title = $this->_title;
		$view->config = $this->config;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->admin = $this->_admin;
		$view->juser = $juser;
		$view->wishlist = $wishlist;
		$view->wish = $wish;
		$view->infolink = $infolink;
		$view->funds = $funds;
		$view->banking = $this->banking;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Save chanegs to a wish
	 * 
	 * @return     void
	 */
	public function savewish()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$live_site = rtrim(JURI::base(), '/');

		$listid = JRequest::getInt('wishlist', 0);
		$wishid = JRequest::getInt('id', 0);
		$reward = JRequest::getVar('reward', '');
		$funds  = JRequest::getVar('funds', '0');
		$tags   = JRequest::getVar('tags', '');

		// Login required
		if ($juser->get('guest')) 
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_ADD');
			$this->login();
			return;
		}

		// Get wish list info
		$objWishlist = new Wishlist ($database);
		$wishlist = $objWishlist->get_wishlist($listid);

		// trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);

		// initiate class and bind posted items to database fields
		$row = new Wish ($database);
		if (!JRequest::getVar('subject', '')) 
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_NO_SUBJECT'));
			$row->bind($_POST);
		}
		else if (!$row->bind($_POST)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// If we are editing
		$by = JRequest::getVar('by', '', 'post');
		if ($by) 
		{
			$ruser =& JUser::getInstance($by);
			if (is_object($ruser)) 
			{
				$row->proposed_by = $ruser->get('id');
			}
			else 
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_INVALID_USER_NAME'));
			}
		}

		// If offering a reward, do some checks
		if ($reward) 
		{
			// Is it an actual number?
			if (!is_numeric($reward)) 
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_INVALID_AMOUNT'));
			}
			// Are they offering more than they can afford?
			if ($reward > $funds) 
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_NO_FUNDS'));
			}
		}

		// Error view
		if ($this->getError()) 
		{
			// Set the pathway
			$this->_taskpath = $wishid
							? 'index.php?option=' . $this->_option . '&task=editwish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid='.$wishid
							: 'index.php?option=' . $this->_option . '&task=add&category=' . $wishlist->category . '&rid='.$wishlist->referenceid;
			$this->_taskname = $wishid
								? JText::_('COM_WISHLIST_EDITWISH')
								: JText::_('COM_WISHLIST_ADD');
			$this->_buildPathway($wishlist);

			// Get URL to page explaining virtual economy
			$aconfig =& JComponentHelper::getParams('com_answers');
			$infolink = $aconfig->get('infolink', '/kb/points/');

			$view = new JView(array('name'=>'editwish'));
			$view->title = JText::_(strtoupper($this->_name));
			$view->config = $this->config;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->admin = $this->_admin;
			$view->juser = $juser;
			$view->wishlist = $wishlist;
			$view->wish = $row;
			$view->infolink = $infolink;
			$view->funds = $funds;
			$view->banking = $this->banking;
			$view->setError($this->getError());
			$view->display();
			return;
		}

		$row->anonymous 	= JRequest::getInt('anonymous', 0);
		$row->private	    = JRequest::getInt('private', 0);
		$row->about     	= Hubzero_Filter::cleanXss($row->about);
		$row->proposed    	= ($wishid) ? $row->proposed : date('Y-m-d H:i:s', time());

		// check content
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// store new content
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// get (new) wish id
		if (!$row->id) 
		{
			$row->checkin();
		}
		$id = $row->id;

		// Add/change the tags
		$tagging = new WishTags($database);
		$tagging->tag_object($juser->get('id'), $row->id, $tags, 1, 1);

		// send message about a new wish
		if (!$wishid) 
		{
			// Build e-mail components
			$jconfig =& JFactory::getConfig();
			$admin_email = $jconfig->getValue('config.mailfrom');

			// Get author name
			$name 	= JText::_('COM_WISHLIST_UNKNOWN');
			$login 	= JText::_('COM_WISHLIST_UNKNOWN');
			if ($row->anonymous) 
			{
				$name = JText::_('COM_WISHLIST_ANONYMOUS');
			}
			else 
			{
				$ruser 	=& JUser::getInstance($row->proposed_by);
				if (is_object($ruser)) 
				{
					$name = $ruser->get('name');
					$login = $ruser->get('username');
				}
			}

			$subject = JText::_(strtoupper($this->_name)).', '.JText::_('COM_WISHLIST_NEW_WISH').' '.JText::_('COM_WISHLIST_FOR').' '.$wishlist->title.' '.JText::_('from').' '.$name;
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
			$from['email'] = $jconfig->getValue('config.mailfrom');

			// get list owners
			$objOwner = new WishlistOwner($database);
			$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);

			$message  = '----------------------------'."\r\n";
			$message .= JText::_('COM_WISHLIST_WISH').' #'.$row->id.', '.$wishlist->title.' '.JText::_('COM_WISHLIST_WISHLIST')."\r\n";
			$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($row->subject)."\r\n";
			$message .= JText::_('COM_WISHLIST_PROPOSED_ON').' '.JHTML::_('date',$row->proposed, '%d %b, %Y');
			$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
			$message .= $row->anonymous ? '' : '('.$login.')';
			$message .= "\r\n";
			$message .= '----------------------------'."\r\n\r\n";
			if (!$wishid)
			{
				$message .= $row->about;
				$message .= "\r\n\r\n";
			}

			$url = $live_site.JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid='.$row->id);
			$message .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_THIS_WISH').'.';

			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();

			if (!$dispatcher->trigger('onSendMessage', array('wishlist_new_wish', $subject, $message, $from, $owners['individuals'], $this->_option))) 
			{
				$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MESSAGE_OWNERS'));
			}
		}

		if ($reward && $this->banking) 
		{
			// put the  amount on hold
			$BTL = new Hubzero_Bank_Teller($database, $juser->get('id'));
			$BTL->hold($reward, JText::_('COM_WISHLIST_BANKING_HOLD') . ' #' . $row->id . ' ' . JText::_('COM_WISHLIST_FOR') . ' ' . $wishlist->title, 'wish', $row->id);
		}

		$saved = $wishid ? 2 : 3;
		$this->_redirect =JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $id) . '?saved=' . $saved;
	}

	/**
	 * Show a form for editing a wish
	 * 
	 * @return     void
	 */
	public function editwish()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$live_site = rtrim(JURI::base(), '/');

		$wishid = JRequest::getInt('wishid', 0);
		$id     = JRequest::getInt('id', 0);
		$refid  = JRequest::getInt('rid', 0);
		$cat    = JRequest::getVar('category', '');
		$status = JRequest::getVar('status', '');
		$vid    = JRequest::getInt('vid', 0);

		$obj = new Wishlist($database);
		$objWish = new Wish($database);

		if (!$wishid) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}
		// Check if wish exists on this list
		$wishlist = $obj->get_wishlist($id, $refid, $cat);

		if (!$wishlist) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}
		else 
		{
			// load wish
			$objWish->load($wishid);
			$changed = 0;

			// Login required
			if ($juser->get('guest')) 
			{
				// Set page title
				$this->_list_title = ($wishlist->public or (!$wishlist->public && $this->_admin==2)) ? $wishlist->title : '';
				$this->_buildTitle();

				// Set the pathway
				$this->_taskpath = 'index.php?option=' . $this->_option . '&task=editwish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid;
				$this->_taskname = JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
				$this->_buildPathway($wishlist);
				$this->login();
				return;
			}

			// get admin priviliges
			$this->authorize_admin($wishlist->id);

			if (!$this->_admin && $objWish->proposed_by != $juser->get('id')) 
			{
				JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
				return;
			}

			if ($this->_task == 'editprivacy') 
			{
				$private 	= JRequest::getInt('private', 0, 'get');
				if ($objWish->private != $private) 
				{
					$objWish->private = $private;
					$changed = 1;
				}
			}
			if ($this->_task == 'editwish' && $status) 
			{
				$former_status = $objWish->status;
				$former_accepted = $objWish->accepted;
				switch ($status)
				{
					case 'pending':
						$objWish->status = 0;
						$objWish->accepted = 0;
					break;

					case 'accepted':
						$objWish->status = 0;
						$objWish->accepted = 1;
						$objWish->assigned = $juser->get('id'); // assign to person who accepted the wish
					break;

					case 'rejected':
						$objWish->accepted = 0;
						$objWish->status = 3;

						// return bonuses
						if ($this->banking) {
							$WE = new WishlistEconomy($database);
							$WE->cleanupBonus($wishid);
						}
					break;

					case 'granted':
						$objWish->status = 1;
						$objWish->granted = date('Y-m-d H:i:s', time());
						$objWish->granted_by = $juser->get('id');
						$objWish->granted_vid= $vid ? $vid : 0;

						$wish = $objWish->get_wish ($wishid, $juser->get('id'));
						$objWish->points = $wish->bonus;

						if ($this->banking) 
						{
							// Distribute bonus and earned points
							$WE = new WishlistEconomy($database);
							$WE->distribute_points($wishid);
						}
					break;
				}

				$changed = ($former_status!=$objWish->status or $former_accepted!=$objWish->accepted) ? 1 : 0;

				if ($changed) 
				{
					// Build e-mail components
					$jconfig =& JFactory::getConfig();
					$admin_email = $jconfig->getValue('config.mailfrom');

					// to wish author
					$subject1 = JText::_(strtoupper($this->_name)) . ', ' . JText::_('COM_WISHLIST_YOUR_WISH') . ' #' . $wishid . ' is ' . $status;

					// to wish assignee
					$subject2 = JText::_(strtoupper($this->_name)) . ', ' . JText::_('COM_WISHLIST_WISH') . ' #' . $wishid . ' ' . JText::_('COM_WISHLIST_HAS_BEEN') . ' ' . JText::_('COM_WISHLIST_MSG_ASSIGNED_TO_YOU');

					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
					$from['email'] = $jconfig->getValue('config.mailfrom');

					$name = JText::_('COM_WISHLIST_UNKNOWN');
					$login = JText::_('COM_WISHLIST_UNKNOWN');
					$ruser =& Hubzero_User_Profile::getInstance($objWish->proposed_by);
					if (is_object($ruser)) 
					{
						$name = $ruser->get('name');
						$login = $ruser->get('username');
					}
					if ($objWish->anonymous) 
					{
						$name = JText::_('COM_WISHLIST_ANONYMOUS');
					}

					$message  = '----------------------------'."\r\n";
					$message .= JText::_('COM_WISHLIST_WISH').' #'.$objWish->id.', '.$wishlist->title.' '.JText::_('COM_WISHLIST')."\r\n";
					$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($objWish->subject)."\r\n";
					$message .= JText::_('COM_WISHLIST_PROPOSED_ON').' '.JHTML::_('date',$objWish->proposed, '%d %b, %Y');
					$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
					$message .= $objWish->anonymous ? '' : '('.$login.')';
					$message .= "\r\n\r\n";

					$message .= '----------------------------'."\r\n";
					$as_mes = $message;
					if ($status!='pending') 
					{
						$message .= JText::_('COM_WISHLIST_YOUR_WISH').' '.JText::_('COM_WISHLIST_HAS_BEEN').' '.$status.' '.JText::_('COM_WISHLIST_BY_LIST_ADMINS').'.'."\r\n";
					}
					else 
					{
						$message .= JText::_('COM_WISHLIST_MSG_WISH_STATUS_CHANGED_TO').' '.$status.' '.JText::_('COM_WISHLIST_BY_LIST_ADMINS').'.'."\r\n";
					}
					$url = $live_site.JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $cat . '&rid=' . $refid . '&wishid=' . $wishid);
					$message .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_YOUR_WISH').'.';
					$as_mes  .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_YOUR_ASSIGNED_WISH').'.';
				}
			}
			// no status change, only information
			else if ($this->_task == 'editwish') 
			{
				$this->addwish($wishid);
				return;
			}

			if ($changed) 
			{
				// save changes
				if (!$objWish->store()) 
				{
					JError::raiseError(500, $objWish->getError());
					return;
				}
				else if ($this->_task == 'editwish') 
				{
					JPluginHelper::importPlugin('xmessage');
					$dispatcher =& JDispatcher::getInstance();

					if (!$dispatcher->trigger('onSendMessage', array('wishlist_status_changed', $subject1, $message, $from, array($objWish->proposed_by), $this->_option))) 
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
					}

					if ($objWish->assigned && $objWish->proposed_by != $objWish->assigned && $status=='accepted') 
					{
						if (!$dispatcher->trigger('onSendMessage', array('wishlist_wish_assigned', $subject2, $as_mes, $from, array($objWish->assigned), $this->_option))) 
						{
							$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
						}
					}
				}
			}
		}

		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $cat . '&rid=' . $refid . '&wishid=' . $wishid);
	}

	/**
	 * Short description for 'movewish'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function movewish()
	{
		ximport('Hubzero_Group');

		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$live_site = rtrim(JURI::base(), '/');

		$listid   = JRequest::getInt('wishlist', 0);
		$wishid   = JRequest::getInt('wish', 0);
		$category = JRequest::getVar('type', '');
		$refid    = JRequest::getInt('resource', 0);
		$cn       = JRequest::getVar('group', '');

		// some transfer options
		$options = array();
		$options['keepplan']     = JRequest::getInt('keepplan', 0);
		$options['keepcomments'] = JRequest::getInt('keepcomments', 0);
		$options['keepstatus']   = JRequest::getInt('keepstatus', 0);
		$options['keepfeedback'] = JRequest::getInt('keepfeedback', 0);

		// missing wish id 
		if (!$wishid) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}
		// missing or invalid resource ID
		if ($category == 'resource' && (!$refid or !intval($refid))) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_INVALID_RESOURCE_ID'));
			return;
		}
		else if ($category == 'general') 
		{
			$refid = 1; // default to main wish list
		}
		else if ($category == 'group' && !$cn) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_INVALID_GROUP_CN'));
			return;
		}

		if ($category=='question' or $category=='ticket') 
		{
			// move to a question or a ticket
			JPluginHelper::importPlugin('support' , 'transfer');
			$dispatcher =& JDispatcher::getInstance();

			$dispatcher->trigger('transferItem', array(
					'wish',
					$wishid,
					$category,
					$options
				)
			);
		}
		else 
		{
			// moving to another list
			$objWishlist = new Wishlist($database);
			$objWish = new Wish($database);

			// Get group id from cn
			if ($category == 'group') 
			{
				if (Hubzero_Group::exists($cn)) 
				{
					$group = Hubzero_Group::getInstance($cn);
					$refid = $group->gidNumber;
				}
				else 
				{
					JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_INVALID_GROUP_CN'));
					return;
				}
			}

			// Where do we put this wish?
			$newlist = $objWishlist->get_wishlistID($refid, $category);

			// Create wishlist for resource if doesn't exist 
			if ($category == 'resource' && !$newlist) 
			{
				// check if resources exists and get  title
				$resource = new ResourcesResource($database);
				$resource->load($refid);

				if ($resource->title && $resource->standalone == 1) 
				{
					$newlist = $objWishlist->createlist($category, $refid, 1, $resource->title);
				}
				else 
				{
					JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_RESOURCE_ID_NOT_FOUND'));
					return;
				}
			}
			else if ($category == 'group' && !$newlist) 
			{
				// create private list for group
				if (Hubzero_Group::exists($refid)) 
				{
					$group = Hubzero_Group::getInstance($refid);
					$newlist = $obj->createlist($cat, $refid, 0, $group->cn . ' ' . JText::_('COM_WISHLIST_GROUP'));
				}
			}

			// cannot add a wish to a non-found list
			if (!$newlist) 
			{
				JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
				return;
			}
			else if ($listid != $newlist) 
			{
				// Transfer wish
				$objWish->load($wishid);
				$objWish->wishlist = $newlist;
				$objWish->assigned = 0; // moved wish is not assigned to anyone yet
				$objWish->ranking = 0; // zero ranking
				$objWish->due = '0000-00-00 00:00:00';

				// renew state if option chosen
				if (!$options['keepstatus']) 
				{
					$objWish->status   = 0;
					$objWish->accepted = 0;
				}

				if (!$objWish->store()) 
				{
					JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_WISH_MOVE_FAILED'));
					return;
				}
				else 
				{
					// also delete all previous owner votes for this wish
					$objR = new WishRank($database);
					$objR->remove_vote($wishid);

					// delete plan if option chosen
					if (!$options['keepplan']) 
					{
						$plan = new WishlistPlan($database);
						$plan->deletePlan($wishid);
					}
					// delete comments if option chosen
					if (!$options['keepcomments']) 
					{
						$reply = new Hubzero_Comment($database);
						$comments1 = $reply->getResults(array('id'=>$wishid, 'category'=>'wish'));
						if (count($comments1) > 0) 
						{
							foreach ($comments1 as $comment1)
							{
								$comments2 = $reply->getResults(array('id'=>$comment1->id, 'category'=>'wishcomment'));
								if (count($comments2) > 0) 
								{
									foreach ($comments2 as $comment2)
									{
										$comments3 = $reply->getResults(array('id'=>$comment2->id, 'category'=>'wishcomment'));
										if (count($comments3) > 0) 
										{
											foreach ($comments3 as $comment3)
											{
												$reply->delete($comment3->id);
											}
										}
										$reply->delete($comment2->id);
									}
								}
								$reply->delete($comment1->id);
							}
						}
					}

					// delete community votes if option chosen
					if (!$options['keepfeedback']) 
					{
						require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'vote.class.php');
						$v = new Vote($database);
						$votes = $v->deleteVotes(array('id'=>$wishid, 'category'=>'wish'));
					}

					// send message about transferred wish
					$jconfig =& JFactory::getConfig();
					$admin_email = $jconfig->getValue('config.mailfrom');

					$oldtitle = $objWishlist->getTitle($listid);
					$newtitle = $objWishlist->getTitle($newlist);

					$name = JText::_('COM_WISHLIST_UNKNOWN');
					$login = JText::_('COM_WISHLIST_UNKNOWN');
					$ruser =& Hubzero_User_Profile::getInstance($objWish->proposed_by);
					if (is_object($ruser)) 
					{
						$name  = $ruser->get('name');
						$login = $ruser->get('username');
					}

					if ($objWish->anonymous) 
					{
						$name = JText::_('COM_WISHLIST_ANONYMOUS');
					}

					$subject1 = JText::_(strtoupper($this->_name)).', '.JText::_('COM_WISHLIST_NEW_WISH').' '.JText::_('COM_WISHLIST_FOR').' '.$newtitle.' '.JText::_('COM_WISHLIST_FROM').' '.$name.' - '.JText::_('COM_WISHLIST_TRANSFERRED');
					$subject2 = JText::_(strtoupper($this->_name)).', '.JText::_('COM_WISHLIST_YOUR_WISH').' #'.$wishid.' '.JText::_('COM_WISHLIST_WISH_TRANSFERRED_TO_DIFFERENT_LIST');

					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
					$from['email'] = $jconfig->getValue('config.mailfrom');

					// get list owners
					$objOwner = new WishlistOwner($database);
					$owners   = $objOwner->get_owners($newlist, $this->admingroup);

					$message  = '----------------------------' . "\r\n";
					$message .= JText::_('COM_WISHLIST_WISH').' #'.$wishid.', '.$newtitle.' '.JText::_('COM_WISHLIST') . "\r\n";
					$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($objWish->subject) . "\r\n";
					$message .= JText::_('COM_WISHLIST_PROPOSED_ON').' '.JHTML::_('date',$objWish->proposed, '%d %b, %Y');
					$message .= ' '.JText::_('COM_WISHLIST_BY').' '.$name.' ';
					$message .= $objWish->anonymous ? '' : '('.$login.')' . "\r\n";
					$message .= JText::_('COM_WISHLIST_WISH_TRANSFERRED_FROM_WISHLIST').' "'.$oldtitle.'"';
					$message .= "\r\n\r\n";

					$message .= '----------------------------' . "\r\n";
					$url = $live_site.JRoute::_('index.php?option=' . $this->_option . '&task=wish&id='.$newlist.'&wishid=' . $wishid);
					$message .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_THIS_WISH').'.';

					JPluginHelper::importPlugin('xmessage');
					$dispatcher =& JDispatcher::getInstance();

					if (!$dispatcher->trigger('onSendMessage', array('wishlist_new_wish', $subject1, $message, $from, $owners['individuals'], $this->_option))) 
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MESSAGE_OWNERS'));
					}

					if (!$dispatcher->trigger('onSendMessage', array('support_item_transferred', $subject2, $message, $from, array($objWish->proposed_by), $this->_option))) 
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
					}
				}
			}

			if ($listid == $newlist) 
			{
				// nothing changed
				$this->_task = 'wishlist';
			}

		} // end if move within Wish List component 

		// go back to wishlist
		$this->listid = $listid;
		$this->wishlist();
	}

	/**
	 * Assign a point bonus to a wish
	 * 
	 * @return     void
	 */
	public function addbonus()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$listid = JRequest::getInt('wishlist', 0);
		$wishid = JRequest::getInt('wish', 0);
		$amount = JRequest::getInt('amount', 0);

		// missing wish id 
		if (!$wishid or !$listid) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		$objWishlist = new Wishlist($database);
		$objWish = new Wish($database);

		$wishlist = $objWishlist->get_wishlist($listid);

		if (!$wishlist) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Login required
		if ($juser->get('guest')) 
		{
			// Set page title
			$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7'  && isset($wishlist->resource->alias))
						? 'tool "' . $wishlist->resource->alias . '"'
						: $wishlist->title;
			if (!$wishlist->public && !$this->_admin) 
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->login();
			return;
		}

		// check available user funds
		$BTL = new Hubzero_Bank_Teller($database, $juser->get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$funds   = ($funds > 0) ? $funds : '0';

		// missing amount
		if ($amount == 0) 
		{
			JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_INVALID_AMOUNT'));
			return;
		}
		if ($amount < 0) 
		{
			JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_NEGATIVE_BONUS'));
			return;
		}
		else if ($amount > $funds) 
		{
			JError::raiseError(500, JText::_('COM_WISHLIST_ERROR_NO_FUNDS'));
			return;
		}

		// put the  amount on hold
		$BTL = new Hubzero_Bank_Teller($database, $juser->get('id'));
		$BTL->hold($amount, JText::_('COM_WISHLIST_BANKING_HOLD').' #'.$wishid.' '.JText::_('COM_WISHLIST_FOR').' '.$wishlist->title, 'wish', $wishid);

		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid);

	}

	/**
	 * Mark a wish as deleted
	 * 
	 * @return     void
	 */
	public function deletewish()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$wishid = JRequest::getInt('wishid', 0);
		$id     = JRequest::getInt('id', 0);
		$refid  = JRequest::getInt('rid', 0);
		$cat    = JRequest::getVar('category', '');

		$obj = new Wishlist($database);
		$objWish = new Wish($database);

		if (!$wishid) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));
			return;
		}

		// Check if wish exists on this list
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		if (!$wishlist) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'));
			return;
		}
		else 
		{
			// Login required
			if ($juser->get('guest')) 
			{
				// Set page title
				$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7' && isset($wishlist->resource->alias))
							? 'tool "' . $wishlist->resource->alias . '"'
							: $wishlist->title;
				if (!$wishlist->public && !$this->_admin) 
				{
					$this->_list_title = '';
				}
				$this->_buildTitle();

				// Set the pathway
				$this->_buildPathway($wishlist);
				$this->login();
				return;
			}

			// get admin priviliges
			$this->authorize_admin($wishlist->id);

			$objWish->load($wishid);
			if (!$this->_admin && $objWish->proposed_by != $juser->get('id')) 
			{
				JError::raiseError(403, JText::_('COM_WISHLIST_ALERTNOTAUTH'));
				return;
			}

			$withdraw = 0; //$this->_task=='withdraw' ? 1 : 0; /* [!] zooley - Mark as deleted instead of withdrawn? Seems to cause confusion if wish still appears in lists. */

			if ($objWish->delete_wish($wishid, $withdraw)) 
			{
				// also delete all votes for this wish
				$objR = new WishRank($database);

				if ($objR->remove_vote($wishid)) 
				{
					// re-calculate rankings of remaining wishes
					$this->listid = $wishlist->id;
					$this->rank($wishlist->id);
				}

				// return bonuses
				if ($this->banking) 
				{
					$WE = new WishlistEconomy($database);
					$WE->cleanupBonus($wishid);
				}
			}
			else 
			{
				$this->_error = JText::_('COM_WISHLIST_ERROR_WISH_DELETE_FAILED');
			}
		}

		// go back to the wishlist
		$this->category = $cat;
		$this->refid = $refid;
		$this->wishlist();
	}

	/**
	 * Save a vote for a wish
	 * 
	 * @return     void
	 */
	public function savevote()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$refid    = JRequest::getInt('rid', 0);
		$category = JRequest::getVar('category', '');
		$wishid   = JRequest::getInt('wishid', 0);

		// get vote
		$effort = JRequest::getVar('effort', '', 'post');
		$importance = JRequest::getVar('importance', '', 'post');

		$objWishlist = new Wishlist($database);
		$objWish = new Wish($database);
		$objR = new WishRank($database);

		// figure list id
		if ($category && $refid) 
		{
			$listid = $objWishlist->get_wishlistID($refid, $category);
		}

		// cannot rank a wish if list/wish is not found
		if (!$listid or !$wishid) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		$wishlist = $objWishlist->get_wishlist($listid);
		$item = $objWish->get_wish($wishid, $juser->get('id'));

		// cannot proceed if wish id is not found
		if (!$wishlist or !$item) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// is this wish on correct list?
		if ($listid != $wishlist->id)
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND_ON_LIST'));
			return;
		}

		// Login required
		if ($juser->get('guest')) 
		{
			// Set page title
			$this->_list_title =(isset($wishlist->resource) && $wishlist->resource->type=='7' && isset($wishlist->resource->alias))
						? 'tool "' . $wishlist->resource->alias . '"'
						: $wishlist->title;
			if (!$wishlist->public && !$this->_admin) 
			{
				$this->_list_title = '';
			}
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_TO_RANK') ;
			$this->login();
			return;
		}

		// get admin priviliges
		$this->authorize_admin($listid);

		// Need to be list admin
		if (!$this->_admin) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ALERTNOTAUTH_ACTION'));
			return;
		}

		// did user make selections?
		if (!$effort or !$importance) 
		{
			$this->_message     = 'Please be sure to provide both an importance and an effort';
			$this->_messageType = 'error';
			$this->_redirect    = JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid);
			return;
		}

		// is the wish ranked already?
		if (isset($item->ranked) && !$item->ranked) 
		{
			$objR->wishid = $wishid;
			$objR->userid = $juser->get('id');
		}
		else 
		{
			// edit rating
			$objR->load_vote($juser->get('id'), $wishid);
		}

		$objR->voted = date('Y-m-d H:i:s', time());
		$objR->importance = $importance;
		$objR->effort = $effort;

		// Check content
		if (!$objR->check()) 
		{
			JError::raiseError(500, $objR->getError());
			return;
		}
		// Store new content
		if (!$objR->store()) 
		{
			JError::raiseError(500, $objR->getError());
			return;
		}
		else 
		{
			// update priority on all wishes
			$this->listid = $wishlist->id;
			$this->rank($wishlist->id);
		}

		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid);

	}

	/**
	 * Short description for 'rank'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $listid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function rank($listid)
	{
		if (!$this->listid) 
		{
			$this->listid = $listid;
		}

		// get admin priviliges
		$this->authorize_admin($this->listid);

		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$filters = $this->getFilters();

		$objWishlist = new Wishlist($database);
		$objWish = new Wish($database);
		$objOwner = new WishlistOwner($database);
		$objR = new WishRank($database);

		$filters['limit'] = 0;

		$wishlist = $objWishlist->get_wishlist($this->listid);
		$wishlist->items = $objWish->get_wishes($this->listid, $filters, $this->_admin, $juser);

		$weight_e = 4;
		$weight_i = 5;
		$weight_f = 0.5;
		$f_threshold = 5;
		$co = 0.5;
		$co_adv = 0.8;
		$co_reg = 0.2;

		// do we give more weight to votes coming from advisory committee?
		$votesplit = $this->config->get('votesplit') ? $this->config->get('votesplit')  : 0;

		if ($wishlist->items) 
		{
			$owners = $objOwner->get_owners($this->listid, $this->admingroup, $wishlist);
			$managers = $owners['individuals'];
			$advisory = $owners['advisory'];

			$voters = array_merge($managers, $advisory);

			foreach ($wishlist->items as $item) 
			{
				$weight_e = 4;
				$weight_i = 5;
				$weight_f = 0.5;
				$f_threshold = 5;
				$co = 0.5;
				$co_adv = 0.8;
				$co_reg = 0.2;

				$votes = $objR->get_votes($item->id);
				$ranking = 0;

				// first consider votes by list owners
				if ($votes) 
				{
					$imp 	= 0;
					$eff 	= 0;
					$num 	= 0;
					$skipped = 0; // how many times effort selection was skipped
					$divisor = 0;

					foreach ($votes as $vote) 
					{
						if (in_array($vote->userid, $voters)) 
						{
							// vote must come from list owner!
							$num++;
							if ($votesplit && in_array($vote->userid, $advisory)) 
							{
								$imp += $vote->importance * $co_adv;
								$divisor +=$co_adv;
							}
							else if ($votesplit) 
							{
								$imp += $vote->importance * $co_reg;
								$divisor +=$co_reg;
							}
							else 
							{
								$imp += $vote->importance;
							}
							if ($vote->effort!= 6) 
							{ // ignore "don't know" selection
								$eff += $vote->effort;
							}
							else 
							{ 
								$skipped++;
							}
						}
						else 
						{
							// need to clean up this vote! looks like owners list changed since last voting
							$remove = $objR->remove_vote($item->id, $vote->userid);
						}
					}

					// average values
					$imp = ($votesplit && $divisor) ? $imp/$divisor: $imp/$num;
					$eff = ($num - $skipped) != 0 ? $eff/($num - $skipped) : 0;
					$weight_i = ($num - $skipped) != 0 ? $weight_i : 7;

					// we need to factor in how many people voted 
					$certainty = $co + $num/count($voters);

					$ranking += ($imp * $weight_i) * $certainty;
					$ranking += ($eff * $weight_e) * $certainty;
				}

				// determine weight of community feedback
				$f = $item->positive + $item->negative;
				$q = $f/$f_threshold;
				//$weight_f = ($weight_f >= 1) ? ($weight_f + $q * $weight_f) : $weight_f;
				$weight_f = ($q >= 1) ? ($weight_f + $q * $weight_f) : $weight_f;

				$ranking += ($item->positive * $weight_f);
				$ranking -= ($item->negative * $weight_f);

				// Do not allow negative ranking
				$ranking = ($ranking < 0) ? 0 : $ranking;

				// save calculated priority

				$row = new Wish($database);
				$row->load($item->id);
				$row->ranking = $ranking;

				// store new content
				if (!$row->store()) 
				{
					JError::raiseError(500, $row->getError());
					return;
				}
			}
		}
	}

	/**
	 * Save a wish comment
	 * 
	 * @return     void
	 */
	public function savereply()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$live_site = rtrim(JURI::base(), '/');

		// Incoming
		$id       = JRequest::getInt('referenceid', 0);
		$listid   = JRequest::getInt('listid', 0);
		$wishid   = JRequest::getInt('wishid', 0);
		$ajax     = JRequest::getInt('ajax', 0);
		$category = JRequest::getVar('cat', '');
		$when     = date('Y-m-d H:i:s');

		$obj = new Wishlist($database);

		// Get wishlist info
		$wishlist = $obj->get_wishlist($listid);

		// trim and addslashes all posted items
		$_POST = array_map('trim', $_POST);

		if (!$wishlist) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'));
			return;
		}

		// Set page title
		$this->_list_title = (isset($wishlist->resource) && $wishlist->resource->type=='7' && isset($wishlist->resource->alias))
					? 'tool "' . $wishlist->resource->alias . '"'
					: $wishlist->title;
		if (!$wishlist->public && !$this->_admin) 
		{
			$this->_list_title = '';
		}
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway($wishlist);

		if (!$id && !$ajax) 
		{
			// cannot proceed
			$this->setError(JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND'));

			// Output HTML
			$view = new JView(array('name'=>'error'));
			$view->title = JText::_(strtoupper($this->_name));
			if ($this->getError()) 
			{
				$view->setError($this->getError());
			}
			$view->display();
			return;
		}

		// is the user logged in?
		if ($juser->get('guest')) 
		{
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_TO_ADD_COMMENT');
			$this->login();
			return;
		}

		if ($id && $category) 
		{
			$row = new Hubzero_Comment($database);
			if (!$row->bind($_POST)) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			// Perform some text cleaning, etc.
			$row->comment   = $row->comment == JText::_('COM_WISHLIST_ENTER_COMMENTS') ? '' : $row->comment;
			//$row->comment   = Hubzero_View_Helper_Html::purifyText($row->comment);
			$attachment     = $this->upload($wishid);
			$row->comment  .= ($attachment) ? "\n" . $attachment : '';
			//$row->comment   = nl2br($row->comment);
			//$row->comment   = str_replace('<br>', '<br />', $row->comment);
			$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
			$row->added     = $when;
			$row->state     = 0;
			$row->category  = $category;
			$row->added_by  = $juser->get('id');

			// Check for missing (required) fields
			if (!$row->check()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			// Save the data
			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			else 
			{
				// Send notofications
				$objWish = new Wish($database);
				$objWish->load($wishid);

				// Build e-mail components
				$jconfig =& JFactory::getConfig();
				$admin_email = $jconfig->getValue('config.mailfrom');

				$name = JText::_('UNKNOWN');
				$login = JText::_('UNKNOWN');
				$ruser =& Hubzero_User_Profile::getInstance($row->added_by);
				if (is_object($ruser)) 
				{
					$name = $ruser->get('name');
					$login = $ruser->get('username');
				}
				if ($row->anonymous) 
				{
					$name = JText::_('ANONYMOUS');
				}

				// Parse comments for attachments
				$webpath = $this->getWebPath($wishid);
				$attach = new WishAttachment($database);
				$attach->webpath = $live_site . $webpath;
				$attach->uppath  = JPATH_ROOT . $webpath;
				$attach->output  = 'email';
				$subject = JText::_(strtoupper($this->_name)) . ', ' . JText::_('COM_WISHLIST_MSG_COMENT_POSTED_YOUR_WISH') . ' #' . $wishid . ' ' . JText::_('BY') . ' ' . $name;

				// email components	
				$from = array();
				$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
				$from['email'] = $jconfig->getValue('config.mailfrom');

				// for the wish owner
				$subject1 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_COMMENTED_YOUR_WISH').' #'.$wishid;

				// for the person to whom wish is assigned
				$subject2 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_COMMENTED_ON_WISH').' #'.$wishid.' '.JText::_('COM_WISHLIST_MSG_ASSIGNED_TO_YOU');

				// for original commentor 
				$subject3 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_REPLIED_YOUR_COMMENT').' #'.$wishid;

				// for others included in the conversation thread.
				$subject4 = JText::_(strtoupper($this->_name)).', '.$name.' '.JText::_('COM_WISHLIST_MSG_COMMENTED_AFTER_YOU').' #'.$wishid;

				$message  = JText::_('COM_WISHLIST_WISH').' #'.$wishid.', '.$wishlist->title.' '.JText::_('COM_WISHLIST') . "\r\n";
				$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY').': '.stripslashes($objWish->subject) . "\r\n";
				$message .= '----------------------------' . "\r\n";
				$message .= JText::_('COM_WISHLIST_MSG_COMMENT_BY').' '.$name.' ';
				$message .= $row->anonymous ? '' : '('.$login.')';
				$message .= ' '.JText::_('COM_WISHLIST_MSG_POSTED_ON').' '.JHTML::_('date',$row->added, '%d %b, %Y').':' . "\r\n";
				$message .= $attach->parse(Hubzero_View_Helper_Html::purifyText($row->comment)) . "\r\n\r\n";
				$message .= "\r\n";

				$message .= '----------------------------' . "\r\n";
				$url = $live_site.JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid);
				$message .= JText::_('COM_WISHLIST_GO_TO').' '.$url.' '.JText::_('COM_WISHLIST_TO_VIEW_THIS_WISH').'.';

				JPluginHelper::importPlugin('xmessage');
				$dispatcher =& JDispatcher::getInstance();

				// collect ids of people who were already emailed
				$contacted = array();

				if ($objWish->proposed_by != $row->added_by) 
				{
					$contacted[] = 	$objWish->proposed_by;

					// send message to wish owner
					if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_posted', $subject1, $message, $from, array($objWish->proposed_by), $this->_option))) 
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_AUTHOR'));
					}
				} // -- end send to wish author

				if ($objWish->assigned && $objWish->assigned != $row->added_by && !in_array($objWish->assigned, $contacted)) 
				{
					$contacted[] = $objWish->assigned;

					// send message to person to who wish is assigned
					if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_posted', $subject2, $message, $from, array($objWish->assigned), $this->_option))) 
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_ASSIGNEE'));
					}
				} // -- end send message to person to who wish is assigned

				// get comment author if reply is posted to a comment
				if ($category=='wishcomment') 
				{
					$parent = new Hubzero_Comment($database);
					$parent->load($id);
					$cuser =& JUser::getInstance($parent->added_by);

					// send message to comment author
					if (is_object($cuser) && $parent->added_by != $row->added_by && !in_array($parent->added_by, $contacted)) 
					{
						$contacted[] = 	$parent->added_by;
						if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_thread', $subject3, $message, $from, array($parent->added_by), $this->_option))) 
						{
							$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_COMMENTOR'));
						}
					}
				}

				// get all users who commented
				$commentors = self::getComments($wishid, $wishid, 'wish', 0, false, array(), 0, 1, 1);
				$comm = array_diff($commentors, $contacted);

				if (count($comm) > 0) 
				{
					if (!$dispatcher->trigger('onSendMessage', array('wishlist_comment_thread', $subject4, $message, $from, $comm, $this->_option))) 
					{
						$this->setError(JText::_('COM_WISHLIST_ERROR_FAILED_MSG_COMMENTOR'));
					}
				}
			} // -- end if success
		} // -- end if id & category

		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $wishid);
	}

	/**
	 * Short description for 'deletereply'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function deletereply()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Incoming
		$replyid = JRequest::getInt('replyid', 0);

		$row = new Hubzero_Comment($database);

		// Do we have a reply ID?
		if (!$replyid or !$row->load($replyid)) 
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_REPLY_NOT_FOUND'));
			return;
		}
		if ($row->added_by == $juser->get('id')) 
		{
			// Delete the comment
			$row->state = 4;

			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}
		else 
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_CANNOT_DELETE_REPLY'));
			return;
		}

		// Go back to the page
		$referer = JRequest::getVar('HTTP_REFERER', NULL, 'server'); // What page they came from
		$this->_redirect = $referer;
	}

	/**
	 * Short description for 'reply'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function reply()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Retrieve a review or comment ID and category
		$listid = JRequest::getInt('id', 0);
		$wishid = JRequest::getInt('wishid', 0);
		$rid    = JRequest::getInt('refid', 0);
		$cat    = JRequest::getVar('cat', '');
		//$page   = JRequest::getVar('page', 1);

		// is the user logged in?
		if ($juser->get('guest')) 
		{
			// Get wishlist info
			$obj = new Wishlist($database);
			$wishlist = $obj->get_wishlist($listid, $rid, $cat);

			// Set page title
			$this->_list_title = ($wishlist->public or (!$wishlist->public && $this->_admin==2)) ? $wishlist->title : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);
			$this->_msg = JText::_('COM_WISHLIST_WARNING_LOGIN_TO_ADD_COMMENT');
			$this->login();
			return;
		}

		$this->referenceid = $rid;
		$this->cat = $cat;
		$this->wishid = $wishid;
		$this->wish();
	}

	/**
	 * Short description for 'rateitem'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function rateitem()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Incoming
		$id   = JRequest::getInt('refid', 1);
		$ajax = JRequest::getInt('ajax', 0);
		$page = JRequest::getVar('page', 'wishlist');
		$cat  = 'wish';
		$vote = JRequest::getWord('vote', ''); // assuming text only vote. Fix for sql injection ticket 1182
		ximport('Hubzero_Environment');
		$ip   = Hubzero_Environment::ipAddress();

		if (!$id) 
		{
			// cannot proceed
			return;
		}

		// load wish
		$row = new Wish($database);
		$row->load($id);

		$objWishlist = new Wishlist($database);
		$listid = $row->wishlist;
		$wishlist = $objWishlist->get_wishlist($listid);

		// Login required
		if ($juser->get('guest')) 
		{
			// Set page title
			$this->_list_title = ($wishlist->public or (!$wishlist->public && $this->_admin==2)) ? $wishlist->title : '';
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway($wishlist);

			$this->_msg = JText::_('COM_WISHLIST_WARNING_WISHLIST_LOGIN_TO_RATE');
			$this->login();
			return;
		}

		$this->authorize_admin($listid);
		$filters = self::getFilters($this->_admin);

		$voted = $row->get_vote ($id, $cat, $juser->get('id'));

		if (!$voted && $row->proposed_by != $juser->get('id') && $row->status==0) 
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'vote.class.php');
			$v = new Vote($database);
			$v->referenceid = $id;
			$v->category = $cat;
			$v->voter = $juser->get('id');
			$v->ip = $ip;
			$v->voted = date('Y-m-d H:i:s', time());
			$v->helpful = $vote;
			if (!$v->check()) 
			{
				$this->setError($v->getError());
				return;
			}
			if (!$v->store()) 
			{
				$this->setError($v->getError());
				return;
			}
			else 
			{
				// update priority on all wishes
				$this->listid = $listid;
				$this->rank($listid);
			}
		}

		// update display
		if ($ajax) 
		{
			$wish = $row->get_wish ($id, $juser->get('id'));
			$view = new JView(array('name'=>'rateitem'));
			$view->option = $this->option;
			$view->item = $wish;
			$view->listid = $listid;
			$view->plugin = 0;
			$view->admin = $this->_admin;
			$view->page = 'wishlist';
			$view->filters = $filters;
			$view->display();
		}
		else 
		{
			if ($page == 'wishlist') 
			{
				$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wishlist&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&filterby='.$filters['filterby'].'&sortby='.$filters['sortby'].'&limitstart='.$filters['start'].'&limit='.$filters['limit'].'&tags='.$filters['tag']);
			}
			else 
			{
				$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=wish&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid . '&wishid=' . $id . '&filterby='.$filters['filterby'].'&sortby='.$filters['sortby'].'&limitstart='.$filters['start'].'&limit='.$filters['limit'].'&tags='.$filters['tag']);
			}
		}
	}

	/**
	 * Short description for 'getWebPath'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $wishid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getWebPath($wishid=0)
	{
		$webpath  = DS . trim($this->config->get('webpath', '/site/wishes'), DS);

		$webpath .= $wishid ? DS . $wishid : '';

		if (!is_dir(JPATH_ROOT . $webpath)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create(JPATH_ROOT . $webpath, 0777)) 
			{
				$out .= JText::_('COM_WISHLIST_ERR_UNABLE_TO_CREATE_PATH');
				return false;
			}
		}

		return $webpath;
	}

	/**
	 * Short description for 'getComments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parentid Parameter description (if any) ...
	 * @param      unknown $itemid Parameter description (if any) ...
	 * @param      unknown $category Parameter description (if any) ...
	 * @param      integer $level Parameter description (if any) ...
	 * @param      boolean $abuse Parameter description (if any) ...
	 * @param      unknown $owners Parameter description (if any) ...
	 * @param      unknown $admin Parameter description (if any) ...
	 * @param      integer $skipattachments Parameter description (if any) ...
	 * @param      integer $getauthors Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getComments($parentid, $itemid, $category, $level, $abuse=false, $owners, $admin, $skipattachments=0, $getauthors = 0)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$live_site = rtrim(JURI::base(),'/');

		$level++;
		$hc = new Hubzero_Comment($database);
		$authors = array();

		$comments = $hc->getResults(array('id' => $itemid, 'category' => $category), 1 , 1);

		if ($comments) 
		{
			// Parse comment text for attachment tags
			if (!$skipattachments) 
			{
				$webpath = $this->getWebPath($parentid);

				$attach = new WishAttachment($database);
				$attach->webpath = $live_site . $webpath;
				$attach->uppath  = JPATH_ROOT . $webpath;
				$attach->output  = 'web';
			}

			foreach ($comments as $comment)
			{

				$comment->comment = stripslashes($comment->comment);
				if (!$skipattachments) 
				{
					$attach->description = '';
					if (!strstr($comment->comment, '</p>') && !strstr($comment->comment, '<pre class="wiki">')) 
					{
						$comment->comment = preg_replace('/<br\\s*?\/??>/i', '', $comment->comment);
						//$comment->comment = htmlentities($comment->comment, ENT_COMPAT, 'UTF-8');
						//$comment->comment = nl2br($comment->comment);
						//$comment->comment = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $comment->comment);
					}
					$comment->comment    = $attach->parse($comment->comment);
					$comment->attachment = $attach->description;
				}

				// get authors excluding current commentator
				if ($comment->added_by != $juser->get('id')) 
				{
					$authors[] = $comment->added_by;
				}

				$comment->replies = self::getComments($parentid, $comment->id, 'wishcomment', $level, $abuse, $owners, $admin, $skipattachments, $getauthors);
				$comment->admin = 0;
				if (in_array($comment->added_by, $owners)) 
				{
					$comment->admin = 1;  // this is a comment by list owner
				}
			}
		}

		if ($getauthors) 
		{
			return array_unique($authors);
		}

		return $comments;
	}

	/**
	 * Short description for 'getFilters'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getFilters($admin=0)
	{
		// Query filters defaults
		$filters = array();
		$filters['sortby']   = trim(JRequest::getVar('sortby', ''));
		$filters['filterby'] = trim(JRequest::getVar('filterby', 'all'));
		$filters['search']   = trim(JRequest::getVar('search', ''));
		$filters['tag']      = trim(JRequest::getVar('tags', ''));

		if ($admin) 
		{
			$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : 'ranking';
		} 
		else 
		{
			$default = $this->banking ? 'bonus' : 'date';
			$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : $default;
		}

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Paging vars
		$filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$filters['start']    = JRequest::getInt('limitstart', 0);
		$filters['new']      = JRequest::getInt('newsearch', 0);
		$filters['start']    = $filters['new'] ? 0 : $filters['start'];
		$filters['comments'] = JRequest::getVar('comments', 1, 'get');

		// Return the array
		return $filters;
	}

	/**
	 * Short description for 'authorize_admin'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $listid Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     void
	 */
	public function authorize_admin($listid = 0, $admin = 0)
	{
		$juser =& JFactory::getUser();
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) 
		{
			$admin = 1;
		}

		if ($listid) 
		{
			$admingroup = $this->config->get('group', 'hubadmin');

			// Get list administrators
			$database =& JFactory::getDBO();
			$objOwner = new WishlistOwner($database);
			$owners = $objOwner->get_owners($listid,  $admingroup);
			$managers =  $owners['individuals'];
			$advisory =  $owners['advisory'];

			if (!$juser->get('guest')) 
			{
				if (in_array($juser->get('id'), $managers)) 
				{
					$admin = 2;  // individual group manager
				}
				if (in_array($juser->get('id'), $advisory)) 
				{
					$admin = 3;  // advisory committee member
				}
			}
		}

		$this->_admin = $admin;
	}

	/**
	 * Short description for 'userSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      array $ownerids Parameter description (if any) ...
	 * @param      unknown $active Parameter description (if any) ...
	 * @param      integer $nouser Parameter description (if any) ...
	 * @param      string $javascript Parameter description (if any) ...
	 * @param      string $order Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function userSelect($name, $ownerids, $active, $nouser=0, $javascript=NULL, $order='a.name')
	{
		$database =& JFactory::getDBO();

		$query = "SELECT a.id AS value, a.name AS text"
			  . "\n FROM #__users AS a"
			  . "\n WHERE a.block = '0' ";
		if (count($ownerids) > 0) 
		{
			$query .= "AND (a.id IN (";
			$tquery = '';
			foreach ($ownerids as $owner) 
			{
				$tquery .= "'" . $owner . "',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);

			$query .= $tquery . ")) ";
		}
		else 
		{
			$query .= " AND 2=1 ";
		}
		$query .= "\n ORDER BY " . $order;

		$database->setQuery($query);
		if ($nouser) 
		{
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
			$users = array_merge($users, $database->loadObjectList());
		} 
		else 
		{
			$users = $database->loadObjectList();
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

		return $users;
	}

	/**
	 * Short description for 'mkt'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $stime Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function mkt($stime)
	{
		if ($stime && preg_match("#([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})#", $stime, $regs)) 
		{
			$stime = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $stime;
	}

	/**
	 * Short description for 'timeAgoo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      number $timestamp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();

		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;

		// Set the periods of time
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0) $val = 0;

		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);

		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) $periods[$val] .= "s";

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0))
		{
			$text .= self::TimeAgoo($new_time);
		}

		return $text;
	}

	/**
	 * Short description for 'timeAgo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $timestamp Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function timeAgo($timestamp)
	{
		$text = $this->timeAgoo($timestamp);

		$parts = explode(' ', $text);

		$text  = $parts[0] . ' ' . $parts[1];
		//$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}

	/**
	 * Short description for 'makeArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function makeArray($string='')
	{
		$string = preg_replace('# #', ',', $string);
		$arr    = explode(',', $string);
		$arr    = $this->cleanArray($arr);
		$arr    = array_unique($arr);

		return $arr;
	}

	/**
	 * Short description for 'cleanArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $array Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function cleanArray($array) 
	{
		foreach ($array as $key => $value) 
		{
			$value = trim($value);
			if ($value == '') 
			{
				unset($array[$key]);
			}
		}

		return $array;
	}

	/**
	 * Short description for 'transform'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $array Parameter description (if any) ...
	 * @param      array $newarray Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function transform($array, $newarray=array()) 
	{
		if (count($array) > 0) 
		{
			foreach ($array as $a) 
			{
				if (is_object($a)) 
				{
					$newarray[$a->gidNumber] = $a->description;
				}
				else 
				{
					$newarray[] = $a;
				}
			}
		}

		return $newarray;
	}

	/**
	 * Short description for 'autocomplete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function autocomplete()
	{
		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = trim(JRequest::getString('value', ''));
		$which = JRequest::getVar('which', 'resource');

		// Fetch results
		$rows = $this->getAutocomplete($filters, $which);

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				if ($which == 'resource') 
				{
					$json[] = '["' . $row->id . ': ' . htmlspecialchars($row->title) . '","' . $row->id . '"]';
				}
				else 
				{
					$json[] = '["' . $row->description . '","' . $row->cn . '"]';
				}

			}
		}

		echo '[' . implode(',', $json) . ']';
		return;
	}

	/**
	 * Short description for 'getAutocomplete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      string $which Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	private function getAutocomplete($filters=array(), $which)
	{
		$database =& JFactory::getDBO();

		if ($which == 'resource') 
		{
			$query = "SELECT r.title, r.id
					FROM #__resources as r 
					WHERE r.standalone=1 AND (LOWER(r.title) LIKE '%" . $filters['search'] . "%' OR LOWER(r.alias) LIKE '%" . $filters['search'] . "%' OR r.id LIKE '" . $filters['search'] . "%')
					ORDER BY r.title ASC";
		}
		else 
		{
			$query = "SELECT t.gidNumber, t.cn, t.description 
					FROM #__xgroups AS t 
					WHERE t.type=1 AND (LOWER(t.cn) LIKE '%" . $filters['search'] . "%' OR LOWER(t.description) LIKE '%" . $filters['search'] . "%')
					ORDER BY t.description ASC";
		}

		//WHERE (t.type=1 OR t.type=2) AND (LOWER(t.cn) LIKE '%".$filters['search']."%' OR LOWER(t.description) LIKE '%".$filters['search']."%')

		$database->setQuery($query);
		return $database->loadObjectList();
	}

	/**
	 * Short description for 'upload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $listdir Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function upload($listdir)
	{
		if (!$listdir) 
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_NO_UPLOAD_DIRECTORY'));
			return '';
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_NO_FILE'));
			return '';
		}

		// Incoming
		$description = JRequest::getVar('description', '');

		$webpath = $this->getWebPath($listdir);
		$path = JPATH_ROOT . $webpath;

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(JFile::getExt($file['name']));

		//make sure that file is acceptable type
		if (!in_array($ext, explode(',', $this->config->get('file_ext', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,wav,mp3,eps,ppt,pps,swf,tar,tex,gz')))) 
		{
			$this->setError(JText::_('ATTACHMENT: Incorrect file type.'));
			return JText::_('ATTACHMENT: Incorrect file type.');
		}

		// Build the path if it doesn't exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('COM_WISHLIST_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return 'ATTACHMENT: ' . JText::_('COM_WISHLIST_UNABLE_TO_CREATE_UPLOAD_PATH');
			}
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setError(JText::_('COM_WISHLIST_ERROR_UPLOADING'));
			return 'ATTACHMENT: ' . JText::_('COM_WISHLIST_ERROR_UPLOADING');
		} 
		else 
		{
			// Scan for viruses
			$path = $path . DS . $file['name']; //JPATH_ROOT . DS . 'virustest';
			exec("clamscan -i --no-summary --block-encrypted $path", $output, $status);
			if ($status == 1)
			{
				if (JFile::delete($path)) 
				{
					$this->setError(JText::_('ATTACHMENT: File rejected because the anti-virus scan failed.'));
					return JText::_('ATTACHMENT: File rejected because the anti-virus scan failed.');
				}
			}

			// File was uploaded
			$description = htmlspecialchars($description);

			$database =& JFactory::getDBO();
			$row = new WishAttachment($database);
			$row->bind(array(
				'id' => 0,
				'wish' => $listdir,
				'filename' => $file['name'],
				'description' => $description
			));
			if (!$row->check()) 
			{
				$this->setError($row->getError());
			}
			if (!$row->store()) 
			{
				$this->setError($row->getError());
			}
			if (!$row->id) 
			{
				$row->getID();
			}

			return '{attachment#' . $row->id . '}';
		}
	}

	/**
	 * Download an attachment
	 * 
	 * @return     void
	 */
	public function download()
	{
		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		$database =& JFactory::getDBO();

		$file = JRequest::getVar('file', '');
		$wishid = JRequest::getInt('wishid', 0);

		$wish = new Wish($database);
		$wish->load($wishid);

		// Ensure we have a path
		if (!$wish->id || $wish->status == 2 || $wish->status == 4) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_FILE_NOT_FOUND'));
			return;
		}

		$wa = new WishAttachment($database);
		$attachment = $wa->loadAttachment($file, $wishid);

		// Ensure we have a path
		if (!$attachment->wish || empty($attachment->filename)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $attachment->filename)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $attachment->filename)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $attachment->filename)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $attachment->filename)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $attachment->filename)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_BAD_FILE_PATH'));
			return;
		}

		jimport('joomla.filesystem.file');
		$ext = strtolower(JFile::getExt($attachment->filename));

		//make sure that file is acceptable type
		if (!in_array($ext, explode(',', $this->config->get('file_ext', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,wav,mp3,eps,ppt,pps,swf,tar,tex,gz')))) 
		{
			JError::raiseError(404, JText::_('Unknown file type.'));
			return;
		}

		$attachment->filename = DS . ltrim($attachment->filename, DS);

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $this->getWebPath($attachment->wish) . $attachment->filename;

		// Ensure the file exist
		if (!file_exists($filename)) 
		{
			JError::raiseError(404, JText::_('COM_WISHLIST_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve()) 
		{
			// Should only get here on error
			JError::raiseError(500, JText::_('COM_WISHLIST_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}

	/**
	 * Short description for 'convertTime'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $rawnum Parameter description (if any) ...
	 * @param      array $due Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function convertTime($rawnum,  $due=array())
	{
		$rawnum = round($rawnum);
		$today = date('Y-m-d H:i:s');

		switch($rawnum)
		{
			case '0':
				$due['immediate'] = date('Y-m-d H:i:s', time() + (62 * 24 * 60 * 60));
				$due['warning'] = date('Y-m-d H:i:s', time() + (120 * 24 * 60 * 60));
			break; // 2 months	

			case '1':
				$due['immediate']= date('Y-m-d H:i:s', time() + (14 * 24 * 60 * 60));
				$due['warning'] = date('Y-m-d H:i:s', time() + (32 * 24 * 60 * 60));
			break; // 2 weeks

			case '2':
				$due['immediate'] = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60));
				$due['warning'] = date('Y-m-d H:i:s', time() + (14 * 24 * 60 * 60));
			break; // 1 week

			case '3':
				$due['immediate'] = date('Y-m-d H:i:s', time() + (2 * 24 * 60 * 60));
				$due['warning'] = date('Y-m-d H:i:s', time() + (6 * 24 * 60 * 60));
			break; // 2 days

			case '4':
				$due['immediate'] = date('Y-m-d H:i:s', time() + (24 * 60 * 60));
				$due['warning'] = date('Y-m-d H:i:s', time() + (2 * 24 * 60 * 60));
			 break; // 1 day

			case '5':
				$due['immediate'] = date('Y-m-d H:i:s', time() + (24 * 60 * 60));
				$due['warning'] = date('Y-m-d H:i:s', time() + (2 * 24 * 60 * 60));
			break; // 4 hours
		}

		return $due;
	}
}
