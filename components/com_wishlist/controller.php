<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class WishlistController extends JObject
{
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

	//-----------

	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';

		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}

		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}
	
	//-----------

	public function setVar ($property, $value)
	{
		$this->$property = $value;
	}
	
	//-----------

	public function getVar ($property)
	{
		return $this->$property;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	//-----------

	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	//-----------

	public function getStyles($option='', $css='')
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet($option, $css);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}

	}
	//-----------
	
	public function getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.$option.DS.$name.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//-----------

	public function getTask()
	{
		$task = JRequest::getVar( 'task', '', 'post' );
		if (!$task) {
			$task = JRequest::getVar( 'task', '', 'get' );
		}
		$this->_task = $task;

		return $task;
	}
	
	//-----------
	
	public function execute()
	{
			
		// Get the component parameters
		$wconfig = new WishlistConfig( $this->_option );
		$this->config = $wconfig;
		
		$database =& JFactory::getDBO();
		$objWishlist = new Wishlist ( $database );
		
		// Check if main wishlist exists, create one if missing
		$this->mainlist = $objWishlist->get_wishlistID(1, 'general');
		if(!$this->mainlist) {
			$this->mainlist = $objWishlist->createlist('general', 1);	
		}
		
		$this->admingroup = isset($this->config->parameters['group']) ? trim($this->config->parameters['group']) : 'hubadmin';
		
		// are we using banking functions?
		$xhub =& XFactory::getHub();
		$banking = $xhub->getCfg('hubBankAccounts');
		$this->banking = ($banking && isset($this->config->parameters['banking']) && $this->config->parameters['banking']==1 ) ? 1: 0 ;
		
		if ($banking) {
			ximport( 'bankaccount' );
		}
		
			
		switch( $this->getTask() ) 
		{
			case 'wishlist':    $this->wishlist();      break;
			case 'settings':    $this->settings();  	break;
			case 'savesettings':$this->savesettings(); 	break;
			case 'newlist':     $this->createlist();    break;
			
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
			
			default: $this->wishlist(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}

	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login($msg='') 
	{
		// Set the page title
		$title = JText::_(strtoupper($this->_name));
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
			
		echo WishlistHtml::div( WishlistHtml::hed( 2, $title ), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		if ($msg) {
			echo WishlistHtml::warning( $msg );
		}
		ximport('xmodule');
		XModuleHelper::displayModules('force_mod');
		echo '</div><!-- / .main section -->'.n;
	
	}
	
	//-----------

	public function all()
	{

		////////////// TBD ///////////////		
		
	}
	
	//-----------

	public function wishlist()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
	
		// get admin priviliges
		WishlistController::authorize_admin();
		$abuse = WishlistController::useAbuse();
		
		$id = JRequest::getInt( 'id', 0 );
		$refid  = JRequest::getInt( 'rid', 0 );
		$cat   	= JRequest::getVar( 'category', '' );
		$filters = WishlistController::getFilters($this->_admin);
		$saved  = JRequest::getInt( 'saved', 0 );
		
		// are we viewing this from within a plugin?
		$plugin = (isset($this->plugin) && $this->plugin!='') ? $this->plugin : '';
		$id = $plugin ? 0 : $id; 
			
		
		$obj = new Wishlist( $database );
		
		if(!$plugin) {	
			if ($this->listid && !$id) {
				$id = $this->listid;
			}
		}
		
		if ($this->category && $this->refid && !$id) {
				$cat = $this->category;
				$refid = $this->refid;
		}
		
		// what wishlist categories are we allowed to have?
		$cats   = $this->config->parameters['categories'];
		$cats   = $cats ? $cats : 'general, resource';
		if($cat && !preg_replace("/".$cat."/", "", $cats) && !$plugin) {
			// oups, this looks like a wrong URL
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		

		if(!$id && $refid) {
			
			$id = $obj->get_wishlistID($refid, $cat);
			
			// Is this a list for an existing resource?
			if(!$id && $cat == 'resource') {
					// get resource title
					$resource = new ResourcesResource( $database );
					$resource->load ($refid);					
					
					if($resource->title && $resource->standalone == 1  && $resource->published == 1) {
						//$type = $resource->getTypeTitle($resource->type);
						//$type = $type ? substr($type,0,strlen($type) - 1) : JText::_('resource') ;
						$rtitle = ($resource->type=='7'  && isset($resource->alias)) ? JText::_('Tool').' '.$resource->alias : JText::_('Resource ID').' '.$resource->id;
						$id = $obj->createlist($cat, $refid, 1, $rtitle, $resource->title);
					}
			}
			
			else if(!$id && $cat == 'user') {
				// create private list for user
				$id = $obj->createlist($cat, $refid, 0, JText::_('My Wish List'));
			}	
			else if(!$id && $cat == 'group') {
				
				// create private list for group
				if(XGroupHelper::groups_exists($refid)) {
					$group = new XGroup();
					$group->select($refid);	
					$id = $obj->createlist($cat, $refid, 0, $group->cn.' '.JText::_('Group'));
				}
			}			
			
		}
		
		// cannot find this list
		if(!$id && !$plugin) {
			//JError::raiseError( 404, JText::_('List doesn\'t exist') );
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
						
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		$total = 0;
		
		
		if(!$wishlist) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
	
		else {
			// remember list id for plugin use
			$this->listid = isset($this->listid) ? $this->listid : $id;
			
			// who are list owners?
			$objOwner = new WishlistOwner( $database );
			$objG 	  = new WishlistOwnerGroup( $database );
			if($plugin) {
				$this->admingroup = isset($this->config->parameters['group']) ? trim($this->config->parameters['group']) : 'hubadmin';
			}
			$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);
			$wishlist->owners = $owners['individuals'];
			$wishlist->groups = $owners['groups'];
			
			// do we have a list owner?
			if(!$juser->get('guest')) {
				if(in_array($juser->get('id'), $wishlist->owners)) {
					$this->_admin = 2;  // individual group owner
				}
			
			}
			
			// need to log in to private list
			if(!$wishlist->public && $juser->get('guest')) {			
				$msg = 'This private wish list requires a login.';
				WishlistController::$this->login($msg);
				return;
			}
						
			// get individual wishes
			$objWish = new Wish( $database );
			$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $this->_admin, $juser);
			$filters['limit'] = (isset($this->limit)) ? $this->limit : $filters['limit'];	
			
			// Get info on individual wishes			
			if($wishlist->items) {
			
				foreach ($wishlist->items as $item) {
					
					// Get comments and abuse reports
					$item->replies = WishlistController::getComments($item->id, 'wish', 0, $abuse=false, $wishlist->owners, $this->_admin);
					$item->reports = WishlistController::getAbuseReports($item->id, 'wish');	
					
					// Do some text cleanup
					$item->subject = stripslashes($item->subject);
					$item->subject = str_replace('&quote;','&quot;',$item->subject);
					$item->subject = htmlspecialchars($item->subject);
					
					// Turn off bonuses is banking is off			
					if(!$this->banking) {
						$item->bonus = 0;
					}
					
					
					$item->urgent = 0;	
					// Do we have a due date?
					if($item->due != '0000-00-00 00:00:00') {
						
						$delivery = WishlistHtml::convertTime ($item->average_effort);
						if($item->due < $delivery['warning']) {
							$item->urgent = 1;
						}
						if($item->due < $delivery['immediate']) {
							$item->urgent = 2;
						}
												
						
					}
					
				}
				
			}
			
			$wishlist->saved = $saved;
			$wishlist->plugin = $plugin;
			$wishlist->banking = $this->banking ? $this->banking : 0;
			$wishlist->banking = $wishlist->category=='user' ? 0 : $this->banking; // do not allow points for individual wish lists
			
			$refid = $wishlist->referenceid;
			$cat   = $wishlist->category;
			$total = ($wishlist->items && count($wishlist->items) > 0) ? count($wishlist->items) : 0 ;
			// record number of items for plugin display
			if($plugin) {
			$this->wishcount = $total;
			}

		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
		
		
		// Add the CSS to the template
		WishlistController::getStyles();
		if(!$plugin) {
		WishlistController::getScripts();
		}
		
		// Thumbs voting CSS & JS
		WishlistController::getStyles('com_answers', 'vote.css');
		//if(!$plugin) {
		//WishlistController::getScripts('com_answers', 'vote');
		//}
		
				
		// Set the pathway
		if(!$plugin) {
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		if($wishlist->public or (!$wishlist->public && $this->_admin==2)) {	
				$title .= ': '.$wishlist->title;
		}	
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title);
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$this->startPath ($wishlist, $title, $pathway);	
		}
		
		if(!$plugin) {		
		echo WishlistHtml::wishlist( $wishlist, $title, $this->_option, $this->_task, $this->_admin, $this->_error, $filters, $juser, $pageNav, $abuse);
		}
		else {
		return WishlistHtml::wishlist( $wishlist, JText::_('Wishlist'), $this->_option, $this->_task, $this->_admin, $this->_error, $filters, $juser, $pageNav, $abuse);
		}
		
	}
	
	//-----------

	public function wish()
	{
		$database =& JFactory::getDBO();
		$juser 		=& JFactory::getUser();
	
		
		$abuse 		= $this->useAbuse();
		$wishid  	= JRequest::getInt( 'wishid', 0 );
		$id  		= JRequest::getInt( 'id', 0 );
		$refid  	= JRequest::getInt( 'rid', 0 );
		$cat   		= JRequest::getVar( 'category', '' );
		$action     = JRequest::getVar( 'action', '');
		$com   		= JRequest::getInt( 'com', 0, 'get' );
		$canedit 	= false;
			
		if ($this->wishid && !$wishid) {
			$wishid = $this->wishid;
		}
		
		if(!$wishid) {
			JError::raiseError( 404, JText::_('Wish not found.') );
			return;
		}
		
		if($juser->get('guest') && $action) {
				$msg = ($action=="addbonus") ? JText::_('Please login to add a bonus for fulfilling this wish.') : '';
				$this->login($msg);
				return;
		}
						
		$obj = new Wishlist( $database );
		$objWish = new Wish( $database );
		
		// Get wish info
		$wish = $objWish->get_wish ($wishid, $juser);
		
		if(!$wish) {
			// wish not found
			JError::raiseError( 404, JText::_('Wish not found.') );
			return;
		}	
		
		// Check if wish is on the right list
		$listid = $wish->wishlist;	
		$id = ($id or ($refid && $cat)) ? $id : $listid; 
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		if(!$wishlist or !$objWish->check_wish ($wishid, $wishlist->id) ) {
			JError::raiseError( 404, JText::_('Wish not found on the requested wish list.') );
			return;
		}
		else {	
		
			// get admin priviliges
			$this->authorize_admin();
			$canedit = $this->_admin ? true : false;		
			
			// who are list owners?
			$objOwner = new WishlistOwner( $database );
			$objG 	  = new WishlistOwnerGroup( $database );
			$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);
			$wishlist->owners = $owners['individuals'];
			$wishlist->groups = $owners['groups'];
			
			// do we have a list owner?
			if(!$juser->get('guest')) {
				if(in_array($juser->get('id'), $wishlist->owners)) {
					$this->_admin = 2;  // individual group owner
				}
			
			}
						
			if(!$wishlist->public && $juser->get('guest')) {
				// need to log in to private list
				$msg = 'This private wish list requires a login.';
				$this->login($msg);
				return;
			}
			
			if($wish->private && $juser->get('guest')) {
				// need to log in to view private wish
				$msg = 'Please login to view this private wish.';
				$this->login($msg);
				return;
			}
			
			// Get the next and previous wishes
			$wish->prev = $objWish->getWishId('prev', $wishid, $listid, $this->_admin);
			$wish->next = $objWish->getWishId('next', $wishid, $listid, $this->_admin);
			
			// Get comments, abuse reports
			$wish->replies = $this->getComments($wishid, 'wish', 0, $abuse, $wishlist->owners, $this->_admin);
			$wish->reports = $this->getAbuseReports($wishid, 'wish');
			
			// Do some text cleanup
			$wish->subject = stripslashes($wish->subject);
			$wish->subject = str_replace('&quote;','&quot;',$wish->subject);
			$wish->subject = htmlspecialchars($wish->subject);
			
			$wish->about = stripslashes($wish->about);
			$wish->about = str_replace('&quote;','&quot;',$wish->about);
			if (!strstr( $wish->about, '</p>' ) && !strstr( $wish->about, '<pre class="wiki">' )) {
				$wish->about = str_replace("<br />","",$wish->about);
				//$wish->about = htmlentities($wish->about);
				$wish->about = nl2br($wish->about);
				//$wish->about = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$wish->about);
				//$wish->about = str_replace("    ",'&nbsp;&nbsp;&nbsp;&nbsp;',$wish->about);
				//$wish->about = utf8_decode($wish->about);
			}
			
			// Build owners drop-down for assigning wishes
			$wish->assignlist = $this->userSelect('assigned', $wishlist->owners, $wish->assigned, 1);	
									
			// Do we have a due date?
			$wish->urgent = 0;
			if($wish->due != '0000-00-00 00:00:00') {
						
				$delivery = WishlistHtml::convertTime ($wish->average_effort);
				if($wish->due < $delivery['warning']) {
					$wish->urgent = 1;
				}
				if($wish->due < $delivery['immediate']) {
					$wish->urgent = 2;
				}						
			}
			
			if($action == 'addbonus' && $this->banking) {
				// check available user funds		
				$BTL 		= new BankTeller( $database, $juser->get('id') );
				$balance 	= $BTL->summary();
				$credit 	= $BTL->credit_summary();
				$funds 		= $balance - $credit;			
				$funds 		= ($funds > 0) ? $funds : '0';
				$wish->funds = $funds;
			}

			
			// Get implementation plan
			$objPlan = new WishlistPlan( $database );
			$plan = $objPlan->getPlan($wishid);
			$plan = $plan ? $plan[0] : '';
			
			$wish->action = $action;
			
			$wish->com = $com;		
			$refid = $wishlist->referenceid;
			$cat   = $wishlist->category;
			
			
		}
		
		if (isset($this->comment)) {
			$addcomment =& $this->comment;
			
		} else {
			$addcomment = NULL;
		}
			
		
		// Add the CSS to the template
		$this->getStyles();
		$this->getScripts();
		
		// Thumbs voting CSS & JS
		$this->getStyles('com_answers', 'vote.css');
		//$this->getScripts('com_answers', 'vote');
		
		// calendar styling
		$this->getStyles('com_events', 'calendar.css');
		$document =& JFactory::getDocument();
		$document->addScript('components'.DS.'com_events'.DS.'js'.DS.'calendar.rc4.js');
		$document->addScript('components'.DS.'com_events'.DS.'js'.DS.'events.js');
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		if(isset($wishlist->resource) && $wishlist->resource->type=='7'  && isset($wishlist->resource->alias)) {
			$subtitle = 'tool "'. $wishlist->resource->alias.'"';	
		}
		else {
			$subtitle = $wishlist->title;
		}
		if($wishlist->public or (!$wishlist->public && $this->_admin==2)) {	
			$title .= ': '.$subtitle;
		}
		
		$extratitle	= WishlistHtml::shortenText($wish->subject, 80, 0);
				
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title);
		
		if ( $this->_task=='reply') {
			$addcomment = & new XComment( $database );
			$addcomment->referenceid = $this->referenceid;
			$addcomment->category = $this->cat;
				
		} else {
			$addcomment = NULL;
		}
		
		// Turn off bonuses is banking is off			
		$wishlist->banking = $this->banking ? $this->banking : 0;
		$wishlist->banking = $wishlist->category=='user' ? 0 : $this->banking; // do not allow points for individual wish lists
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$this->startPath ($wishlist, $title, $pathway);	
		$pathway->addItem( $extratitle, 'index.php?option='.$this->_option.a.'task=wish'.a.'category='.$cat.a.'rid='.$refid.a.'wishid='.$wishid );
		
		$title  = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
						
		echo WishlistHtml::wish( $wishlist, $wish,  $title, $this->_option, $this->_task,  $this->_error, $this->_admin, $juser, $addcomment, $plan, $abuse, $canedit);
	
	}
	
	
	//----------------------------------------------------------
	// Manage Plan
	//----------------------------------------------------------
	
	public function saveplan() {
	
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$wishid  = JRequest::getInt( 'wishid', 0 );
		
		// Make sure we have wish id
		if(!$wishid) {
			JError::raiseError( 404, JText::_('Wish not found.') );
			return;
		}
						
		$obj = new Wishlist( $database );
		$objWish = new Wish( $database );
		$objWish->load($wishid);
		
		if(!$objWish->load($wishid)) {
			JError::raiseError( 404, JText::_('Wish not found.') );
			return;
		}	
		
		$wishlist = $obj->get_wishlist($objWish->wishlist);	
		
		$pageid = JRequest::getInt( 'pageid', 0, 'post' );
		$create_revision = JRequest::getInt( 'create_revision', 0, 'post' );
				
		// Initiate extended database class
		$page = new WishlistPlan( $database );
		if (!$pageid) {
			// New page - save it to the database			
			$old = new WishlistPlan( $database );
			
		} else {
			// Existing page - load it up
			$page->load( $pageid );

			// Get the revision before changes
			$old = $page;
		}
		
		$page->version = JRequest::getInt( 'version', 1, 'post' );
		
		
		if($create_revision) {
			$page = new WishlistPlan( $database );
			$page->version = $old->version + 1;
		}

		$page->wishid = $wishid;
		$page->created_by = JRequest::getInt( 'created_by', $juser->get('id'), 'post' );
		$page->created = date( 'Y-m-d H:i:s', time());
		$page->approved = 1;
		$page->pagetext   = rtrim($_POST['pagetext']);
		
		// Stripslashes just to make sure
		$old->pagetext = rtrim(stripslashes($old->pagetext));
		$page->pagetext = rtrim(stripslashes($page->pagetext));
		
		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if ($old->pagetext != $page->pagetext or (!$create_revision && $pageid)) {
				
			// Transform the wikitext to HTML
			ximport('wiki.parser');
			$p = new WikiParser( $objWish->id, $this->_option, 'wishlist'.DS.$wishlist->id, $objWish->id );
			$page->pagehtml = $p->parse( $page->pagetext );
				
			// Store content
			if (!$page->store()) {
				echo WishlistHtml::alert( $page->getError() );
				exit();
			}
		}		
		
		// do we have a due date?
		$isdue  = JRequest::getInt( 'isdue', 0 );
		$due    = JRequest::getVar( 'publish_up', '' );
	
		if($due) {
			$publishtime = $due.' 00:00:00';
			$due = strftime("%Y-%m-%d %H:%M:%S",strtotime($publishtime)); 
		}
		
		//is this wish assigned to anyone?
		$assignedto = JRequest::getInt( 'assigned', 0 );
		
		$objWish->due = ($due ) ? $due : '0000-00-00 00:00:00';
	    $objWish->assigned = ($assignedto ) ? $assignedto : 0;

		// store our due date
		if (!$objWish->store()) {
			echo WishlistHtml::alert( $objWish->getError() );
			exit();
		}
		
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$wishid).'#plan';	
	}
	
	//----------------------------------------------------------
	// Manage List
	//----------------------------------------------------------
			
	
	public function savesettings() {
	
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$listid  = JRequest::getInt( 'listid', 0);
		$action  = JRequest::getVar( 'action', '');
		
		
		// Make sure we have list id
		if(!$listid) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
		}
		
		$obj = new Wishlist( $database );
		$wishlist = $obj->get_wishlist($listid);
		
		$objOwner = new WishlistOwner( $database );
		$objG 	  = new WishlistOwnerGroup( $database );		
				
		// get admin priviliges
		$this->authorize_admin($listid);
			
		if(!$this->_admin) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		
		// Deeleting a user/group
		if($action == 'delete') {
			$user   = JRequest::getInt( 'user', 0);
			$group  = JRequest::getInt( 'group', 0);
			
			if($user) {
				$objOwner->delete_owner($listid, $user, $this->admingroup);
			}
			else if($group) {
				$objG->delete_owner_group($listid, $group, $this->admingroup);
			}
			
			// update priority on all wishes
			$this->listid = $listid;
			$this->rank();
			
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'?saved=1';
			return;
		}
			
		
		$_POST = array_map('trim',$_POST);
		
		$obj->load($listid);
		
		if (!$obj->bind( $_POST )) {
			echo WishlistHtml::alert( $obj->getError() );
			exit();
		}
		$obj->description  = rtrim(stripslashes($obj->description));
		$obj->description  = TextFilter::cleanXss($obj->description);
		$obj->description  = nl2br($obj->description);
	
		// check content
		if (!$obj->check()) {
			echo WishlistHtml::alert( $obj->getError() );
			exit();
		}

		// store new content
		if (!$obj->store()) {
			echo WishlistHtml::alert( $obj->getError() );
			exit();
		}
		
		// Save new owners
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.helper.php' );
		$newowners = ContribtoolHelper::makeArray(rtrim($_POST['newowners']));
		$newgroups = ContribtoolHelper::makeArray(rtrim($_POST['newgroups']));
		
		$objOwner->save_owners($listid, $this->config, $newowners );
		$objG->save_owner_groups($listid, $this->config, $newgroups);
		
		// update priority on all wishes
		$this->listid = $listid;
		$this->rank();
		
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'?saved=1';
	}
	
	//-----------
	
	public function settings()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// get list id
		$id  	= JRequest::getInt( 'id', 0 );
		
		// Login required
		if ($juser->get('guest')) {
			$msg = 'Please login to view manage list settings.';
			$this->login($msg);
			return;
		}
				
		$obj = new Wishlist( $database );
		$wishlist = $obj->get_wishlist($id);
		
		if(!$wishlist) {
			// list not found
			JError::raiseError( 404, JText::_('Wish list not found.') );
			return;
		}
		
		// get admin priviliges
		$this->authorize_admin($id);
		
		// only admins are allowed to change list settings
		if(!$this->_admin) {	
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}	
		
		// who are list owners?
		$objOwner = new WishlistOwner( $database );
		$objG 	  = new WishlistOwnerGroup( $database );
		$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);
		$wishlist->owners = $owners['individuals'];
		$wishlist->groups = $owners['groups'];
		
		$nativeowners = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist, 1);
		$wishlist->nativeowners = $nativeowners['individuals'];
		$wishlist->nativegroups = $nativeowners['groups'];
		
		
		// Add the CSS to the template
		$this->getStyles();
		$this->getScripts();
		
		// Thumbs voting CSS & JS
		$this->getStyles('com_answers', 'vote.css');
		$this->getScripts('com_answers', 'vote');
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		if($wishlist->public or (!$wishlist->public && $this->_admin==2)) {	
				$title .= ': '.$wishlist->title;
		}	
				
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title.' - '.JText::_(strtoupper($this->_task)));
		
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$this->startPath ($wishlist, $title, $pathway);	
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task=settings'.a.'id='.$id );
			
		
		echo WishlistHtml::settings( $wishlist,JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)), $this->_option, $this->_error, $this->_admin, $juser);
		
	}

	//----------------------------------------------------------
	// Manage Wishes
	//----------------------------------------------------------

	public function addwish($wishid=0)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Incoming
		$listid 	= JRequest::getInt( 'id', 0 );
		$refid		= JRequest::getInt( 'rid', 0 );
		$category 	= JRequest::getVar( 'category', '' );
		
		
		// Login required
		if ($juser->get('guest')) {
			$msg = 'Please login to add a wish.';
			$this->login($msg);
			return;
		}
		
		$objWishlist = new Wishlist ( $database );
		$wish = new Wish ( $database );
		
		if(!$listid && $refid) {
			if(!$category) {
				JError::raiseError( 404, JText::_('Cannot locate a wishlist') );
				return;
			}
			else {
				$listid = $objWishlist->get_wishlistID($refid, $category);
			}
			
			// Create wishlist for resource 
			if ($category == 'resource' && !$listid) {
					// check if resources exists and get  title
					$resource = new ResourcesResource( $database );
					$resource->load ($refid);
					
					if($resource->title && $resource->standalone == 1) {
						$listid = $objWishlist->createlist($cat, $refid, 1, $resource->title);
					}
				
			}
			
		}
		if($wishid) {
			// we are editing
			$wish->load($wishid);
			$listid = $wish->wishlist;
		}
		
		// cannot add a wish to a non-found list
		if(!$listid) {
			JError::raiseError( 404, JText::_('Cannot locate a wishlist') );
			return;
		}
		else {		
			$wishlist = $objWishlist->get_wishlist($listid);
		} 
		
		// list not found - seems to be an incorrect id
		if(!$wishlist) {
			JError::raiseError( 404, JText::_('Cannot locate a wishlist') );
			return;
		}
		
		// get admin priviliges
		$this->authorize_admin($listid);
		
		// this is a private list, can't add to it
		if(!$wishlist->public && $this->_admin!=2) {	
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}		

			if(!$wishid) {
			$wish->proposed_by 	= $juser->get('id');
			$wish->status = 0;
			$wish->anonymous  = 0;
			$wish->private = 0;
			}
		
		 // do not allow points for individual wish lists
		$this->banking = $wishlist->category=='user' ? 0 : $this->banking;
		
		// Is banking turned on?
		$funds = 0;
		if ($this->banking) {
			$database =& JFactory::getDBO();
			
			$BTL = new BankTeller( $database, $juser->get('id') );
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$funds   = $balance - $credit;			
			$funds   = ($funds > 0) ? $funds : '0';				
		}
		
		$aconfig =& JComponentHelper::getParams( 'com_answers' );
		$infolink = $aconfig->get('infolink') ? $aconfig->get('infolink') : '/kb/points/'; 
		
		// Add the CSS to the template
		$this->getStyles();
		$this->getScripts();
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		if($wishlist->public or (!$wishlist->public && $this->_admin==2)) {	
				$title .= ': '.$wishlist->title;
		}	
				
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title.' - '.JText::_(strtoupper($this->_task)));
		
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$this->startPath ($wishlist, $title, $pathway);	
		if(!$wishid) {
		$pathway->addItem( JText::_('TASK_ADD'), 'index.php?option='.$this->_option.a.'task=add'.a.'category='.$category.a.'rid='.$refid  );
		}
		else {
		$pathway->addItem( JText::_('Edit Wish'), 'index.php?option='.$this->_option.a.'task=editwish'.a.'category='.$category.a.'rid='.$refid.a.'wishid='.$wishid );
		}
		
		$task = $this->_task == 'editwish' ? $this->_task : 'TASK_ADD';
				
		echo WishlistHtml::wish_form( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($task)), $wishlist, $wish, $this->_error, $this->_option, $this->_task, $this->_admin, $funds, $this->banking, $infolink);
		
	}
	
	//--------------
	
	public function savewish()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$listid = JRequest::getInt( 'wishlist', 0 );
		$wishid = JRequest::getInt( 'id', 0 );
		$reward = JRequest::getVar( 'reward', '');
		$funds  = JRequest::getVar( 'funds', '0' );
		
		// Login required
		if ($juser->get('guest')) {
			$msg = 'Please login to add a wish.';
			$this->login($msg);
			return;
		}
		
		// trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
		
		// initiate class and bind posted items to database fields
		$row = new Wish ( $database );
		if (!$row->bind( $_POST )) {
			echo WishlistHtml::alert( $row->getError() );
			exit();
		}
		
		// If we are editing
		$by = JRequest::getVar( 'by', '', 'post' );
		if($by) {
			$ruser =& XUser::getInstance($by);
			if (is_object($ruser)) {
				$row->proposed_by = $ruser->get('uid');
			}
			else {
				echo WishlistHtml::alert( JText::_('The username of the wish author appears to be invalid.') );
				exit();
			}
			
		}
		
		// If offering a reward, do some checks
		if ($reward) {
			// Is it an actual number?
			if (!is_numeric($reward)) {
				echo WishlistHtml::alert( JText::_('ERROR_INVALID_AMOUNT') );
				exit();
			}
			// Are they offering more than they can afford?
			if ($reward > $funds) {
				echo WishlistHtml::alert( JText::_('ERROR_NO_FUNDS') );
				exit();
			}
		}
				
		$row->anonymous 	= JRequest::getInt( 'anonymous', 0 );
		$row->private	    = JRequest::getInt( 'private', 0 );
		$row->about     	= TextFilter::cleanXss($row->about);
		//$row->about     	= nl2br($row->about);
		$row->proposed    	= ($wishid) ? $row->proposed : date( 'Y-m-d H:i:s', time() );

		// check content
		if (!$row->check()) {
			echo WishlistHtml::alert( $row->getError() );
			exit();
		}

		// store new content
		if (!$row->store()) {
			echo WishlistHtml::alert( $row->getError() );
			exit();
		}
		
		$objWishlist = new Wishlist ( $database );			
		$wishlist = $objWishlist->get_wishlist($listid);
	
		
		// send message about a new wish
		if(!$wishid) {
					// Build e-mail components
					$xhub =& XFactory::getHub();
					$jconfig =& JFactory::getConfig();
					$admin_email = $jconfig->getValue('config.mailfrom');
					
					$name = JText::_('UNKNOWN');
					$login = JText::_('UNKNOWN');
					$ruser =& XUser::getInstance($row->proposed_by);
					if (is_object($ruser)) {
						$name = $ruser->get('name');
						$login = $ruser->get('login');
					}
					
					$subject = JText::_(strtoupper($this->_name)).', '.JText::_('NEW_WISH').' '.JText::_('FOR').' '.$wishlist->title.' '.JText::_('from').' '.$name;
					
					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
					$from['email'] = $jconfig->getValue('config.mailfrom');
					
					
					
					// get list owners
					$objOwner = new WishlistOwner( $database );
					$owners   = $objOwner->get_owners($wishlist->id, $this->admingroup , $wishlist);					
		
					$message  = '----------------------------'.r.n;
					$message .= JText::_('WISH').' #'.$row->id.', '.$wishlist->title.' '.JText::_('WISHLIST').r.n;
					$message .= JText::_('WISH_DETAILS_SUMMARY').': '.stripslashes($row->subject).r.n;
					$message .= JText::_('PROPOSED_ON').' '.JHTML::_('date',$row->proposed, '%d %b, %Y');
					$message .= ' '.JText::_('BY').' '.$name.' ('.$login.')'.r.n.r.n;
					
					$message .= '----------------------------'.r.n;
					$url = $xhub->getCfg('hubLongURL').JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$row->id);
					$message .= JText::_('GO_TO').' '.$url.' '.JText::_('TO_VIEW_THIS_WISH').'.';
					
					JPluginHelper::importPlugin( 'xmessage' );
					$dispatcher =& JDispatcher::getInstance();
					
					if (!$dispatcher->trigger( 'onSendMessage', array( 'wishlist_new_wish', $subject, $message, $from, $owners['individuals'], $this->_option ))) {
								$this->setError( JText::_('Failed to message wish list owners.') );
								echo WishlistHtml::alert( $this->_error );
					}
					
		}
		
	
		if($reward && $this->banking) {
			
			// put the  amount on hold
			$BTL = new BankTeller( $database, $juser->get('id') );
			$BTL->hold($reward, JText::_('BANKING_HOLD').' #'.$row->id.' '.JText::_('for').' '.$wishlist->title, 'wish', $row->id);
		}
		
				
		$saved = $wishid ? 2 : 3;
		
		// go back to wishlist
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'?saved='.$saved;		
		//$this->listid = $listid;
		//$this->wishlist();
	}
	
	//-----------
	
	public function editwish()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$wishid  = JRequest::getInt( 'wishid', 0 );
		$id  	= JRequest::getInt( 'id', 0 );
		$refid  = JRequest::getInt( 'rid', 0 );
		$cat   	= JRequest::getVar( 'category', '' );
		$status = JRequest::getVar( 'status', '' );
		$vid 	= JRequest::getInt( 'vid', 0 );
		
		// Login required
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
				
		$obj = new Wishlist( $database );
		$objWish = new Wish( $database );
		
		if(!$wishid) {
			JError::raiseError( 404, JText::_('Could not find a wish to take action.') );
			return;
		}
		// Check if wish exists on this list
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		if(!$wishlist or !$objWish->check_wish ($wishid, $wishlist->id) ) {
			JError::raiseError( 404, JText::_('Wish not found on the requested wish list.') );
			return;
		}
		else {	
		
			// get admin priviliges
			$this->authorize_admin($wishlist->id);
			
			if(!$this->_admin) {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			
			// load wish
			$objWish->load($wishid);
			$changed = 0;		
		
			if($this->_task == 'editprivacy') {
				
				$private 	= JRequest::getInt( 'private', 0, 'get' );
				if($objWish->private != $private) {
					$objWish->private = $private;
					$changed = 1;
				}
				
			}
			if($this->_task == 'editwish' && $status) {
				$former_status = $objWish->status;
				$former_accepted = $objWish->accepted;
				switch( $status) 
				{
					case 'pending':
					$objWish->status = 0; 
					$objWish->accepted = 0;   	
					break;
					
					case 'accepted':
					$objWish->status = 0;
					$objWish->accepted = 1;    	
					break;
					
					case 'rejected':
					$objWish->accepted = 0;
					$objWish->status = 3;
					
					// return bonuses
					if($this->banking) {
						$WE = new WishlistEconomy( $database );			
						$WE->cleanupBonus($wishid);
					}	    	
					break;
					
					case 'granted':
					$objWish->status = 1;
					$objWish->granted = date( 'Y-m-d H:i:s', time() );
					$objWish->granted_by = $juser->get('id'); 
					$objWish->granted_vid= $vid ? $vid : 0;
					
					$wish = $objWish->get_wish ($wishid);
					$objWish->points = $wish->bonus;
					
					if($this->banking) {
						// Distribute bonus and earned points
						$WE = new WishlistEconomy( $database );			
						$WE->distribute_points($wishid);
					}					   	
					break;
				}
				
				$changed = ($former_status!=$objWish->status or $former_accepted!=$objWish->accepted) ? 1 : 0;
				
				if($changed) {
					// Build e-mail components
					$xhub =& XFactory::getHub();
					$jconfig =& JFactory::getConfig();
					$admin_email = $jconfig->getValue('config.mailfrom');
					
					$subject = JText::_(strtoupper($this->_name)).', '.JText::_('YOUR_WISH').' #'.$wishid.' is '.$status;
					
					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
					$from['email'] = $jconfig->getValue('config.mailfrom');
					
					$name = JText::_('UNKNOWN');
					$login = JText::_('UNKNOWN');
					$ruser =& XUser::getInstance($objWish->proposed_by);
					if (is_object($ruser)) {
						$name = $ruser->get('name');
						$login = $ruser->get('login');
					}
		
					$message  = '----------------------------'.r.n;
					$message .= JText::_('WISH').' #'.$objWish->id.', '.$wishlist->title.' '.JText::_('WISHLIST').r.n;
					$message .= JText::_('WISH_DETAILS_SUMMARY').': '.stripslashes($objWish->subject).r.n;
					$message .= JText::_('PROPOSED_ON').' '.JHTML::_('date',$objWish->proposed, '%d %b, %Y');
					$message .= ' '.JText::_('BY').' '.$name.' ('.$login.')'.r.n.r.n;
					
					$message .= '----------------------------'.r.n;
					if($status!='pending') {
					$message .= JText::_('YOUR_WISH').' '.JText::_('HAS_BEEN').' '.$status.' '.JText::_('BY_LIST_ADMINS').'.'.r.n;
					}
					else {
					$message .= JText::_('The status of your wish changed to').' '.$status.' '.JText::_('BY_LIST_ADMINS').'.'.r.n;
					}
					$url = $xhub->getCfg('hubLongURL').JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$cat.a.'rid='.$refid.a.'wishid='.$wishid);
					$message .= JText::_('GO_TO').' '.$url.' '.JText::_('TO_VIEW_YOUR_WISH').'.';
					
					JPluginHelper::importPlugin( 'xmessage' );
					$dispatcher =& JDispatcher::getInstance();
					
					if (!$dispatcher->trigger( 'onSendMessage', array( 'wishlist_status_changed', $subject, $message, $from, array($objWish->proposed_by), $this->_option ))) {
								$this->setError( JText::_('Failed to message wish author.') );
								echo WishlistHtml::alert( $this->_error );
					}
					
				}
				
			}
			
			// no status change, only information
			else if($this->_task == 'editwish') {			
				
				$this->addwish($wishid);
				return;
			}
			
			/*
			if($this->_task == 'grantwish') {
				
				$objWish->status = 1;
				$objWish->granted = date( 'Y-m-d H:i:s', time() );
				$objWish->granted_by = $juser->get('id');
				$changed = 1;
				
				if($this->banking) {
					// Distribute bonus and earned points
					$WE = new WishlistEconomy( $database );			
					$WE->distribute_points($wishid);
				}			
			
			}*/
			
			if($changed) {
				// save changes
				if (!$objWish->store()) {
					echo WishlistHtml::alert( $objWish->getError() );
					exit();
				}
			}	
			
		
		}
	
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$cat.a.'rid='.$refid.a.'wishid='.$wishid);	
		
		
	}
	//-----------
	
	public function movewish()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$listid = JRequest::getInt( 'wishlist', 0 );
		$wishid = JRequest::getInt( 'wish', 0 );
		$category = JRequest::getVar( 'type', '' );
		$refid = JRequest::getInt( 'resource', 0);
		
		
		// missing wish id 
		if(!$wishid) {
			echo WishlistHtml::alert( JText::_('Missing wish id'));
			exit();
		}
		// missing or invalid resource ID
		if($category == 'resource' && (!$refid or !intval($refid))) {
			echo WishlistHtml::alert( JText::_('Please specify a valid resource ID'));
			exit();
		}
		else if($category == 'general' ) {			
			$refid = 1; // default to main wish list
		}
		
		if($category=='question' or $category=='ticket') {
			// move to a question or a ticket
			
			JPluginHelper::importPlugin( 'support' , 'transfer');
			$dispatcher =& JDispatcher::getInstance();
			
			$dispatcher->trigger( 'transferItem', array(
					'wish',
					$wishid,
					$category)
			);
				
		}
		else {
		
			$objWishlist = new Wishlist ( $database );
			$objWish = new Wish( $database );
			
			// Where do we put this wish?
			$newlist = $objWishlist->get_wishlistID($refid, $category);
			
			// Create wishlist for resource if doesn't exist 
			if ($category == 'resource' && !$newlist) {
						// check if resources exists and get  title
						$resource = new ResourcesResource( $database );
						$resource->load ($refid);
						
						if($resource->title && $resource->standalone == 1) {
							$newlist = $objWishlist->createlist($category, $refid, 1, $resource->title);
						}
						else {
							echo WishlistHtml::alert( JText::_('Resource with specified ID was not found or not accessible.'));
							exit();
						}
					
			}
		
			
			// cannot add a wish to a non-found list
			if(!$newlist) {
				JError::raiseError( 404, JText::_('Cannot locate a wishlist') );
				return;
			}
			else if($listid != $newlist) {		
				// Transfer wish
				$objWish->load($wishid);
				$objWish->wishlist = $newlist;
				$objWish->ranking = 0; // zero ranking
				
				if (!$objWish->store()) {
					$this->_error = JText::_('Failed to move the wish.');
				}
				else {
				
					// also delete all previous votes for this wish
					$objR = new WishRank( $database );
					$objR->remove_vote($wishid);
				
				}
				
			}
			
			if($listid == $newlist) {
			// nothing changed
			$this->_task = 'wishlist';
			}
		
		} // end if move within Wish List component 
		
		
		
		// go back to wishlist		
		$this->listid = $listid;
		$this->wishlist();
		
	}
	//-----------
	
	public function addbonus()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$listid = JRequest::getInt( 'wishlist', 0 );
		$wishid = JRequest::getInt( 'wish', 0 );
		$amount = JRequest::getInt( 'amount', 0 );
		
		// Login required
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// missing wish id 
		if(!$wishid) {
			echo WishlistHtml::alert( JText::_('Missing wish id'));
			exit();
		}
		
		// check available user funds		
		$BTL 		= new BankTeller( $database, $juser->get('id') );
		$balance 	= $BTL->summary();
		$credit 	= $BTL->credit_summary();
		$funds 		= $balance - $credit;			
		$funds 		= ($funds > 0) ? $funds : '0';
		
		// missing amount
		if($amount == 0) {
			echo WishlistHtml::alert( JText::_('ERROR_INVALID_AMOUNT'));
			exit();
		}
		else if($amount > $funds ) {			
			echo WishlistHtml::alert( JText::_('ERROR_NO_FUNDS'));
			exit();
		}
		
		$objWishlist = new Wishlist ( $database );
		$objWish = new Wish( $database );
		
		$wishlist = $objWishlist->get_wishlist($listid);
		
		// put the  amount on hold
		$BTL = new BankTeller( $database, $juser->get('id') );
		$BTL->hold($amount, JText::_('BANKING_HOLD').' #'.$wishid.' '.JText::_('for').' '.$wishlist->title, 'wish', $wishid);
		
	
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$wishid);
		
	}
	
	//-----------
	
	public function deletewish()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$wishid  = JRequest::getInt( 'wishid', 0 );
		$id  	= JRequest::getInt( 'id', 0 );
		$refid  = JRequest::getInt( 'rid', 0 );
		$cat   	= JRequest::getVar( 'category', '' );
				
		$obj = new Wishlist( $database );
		$objWish = new Wish( $database );
		
		if(!$wishid) {
			JError::raiseError( 404, JText::_('Could not find a wish to delete.') );
			return;
		}
				
		// Check if wish exists on this list
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		if(!$wishlist or !$objWish->check_wish ($wishid, $wishlist->id) ) {
			JError::raiseError( 404, JText::_('Wish not found on the requested wish list.') );
			return;
		}
		else {	
		
			// get admin priviliges
			$this->authorize_admin($wishlist->id);
			
			if(!$this->_admin) {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			
			$withdraw = $this->_task=='withdraw' ? 1 : 0;
		
			if($objWish->delete_wish ($wishid, $withdraw)) {
				
				// also delete all votes for this wish
				$objR = new WishRank( $database );
				
				if($objR->remove_vote($wishid)) {
				
					// re-calculate rankings of remaining wishes
					$this->listid = $wishlist->id;
					$this->rank();
				}
				
				// return bonuses
				if($this->banking) {
					$WE = new WishlistEconomy( $database );			
					$WE->cleanupBonus($wishid);
				}	
				
			}
			else {
				$this->_error = JText::_('Failed to delete the wish.');
			}
		
		}
		
		// go back to the wishlist
		$this->category = $cat;
		$this->refid = $refid;
		$this->wishlist();	
	}
	
	//----------------------------------------------------------
	// Admin votes
	//----------------------------------------------------------

	public function savevote()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		//$listid 	= JRequest::getInt( 'id', 0 );
		$refid		= JRequest::getInt( 'rid', 0 );
		$category 	= JRequest::getVar( 'category', '' );
		$wishid 	= JRequest::getInt( 'wishid', 0 );
		
		// get vote
		$effort 	= JRequest::getVar( 'effort', '', 'post' );
		$importance = JRequest::getVar( 'importance', '', 'post' );
			
		$objWishlist = new Wishlist ( $database );
		$objWish = new Wish ( $database );
		$objR = new WishRank ( $database );
		
		// figure list id
		if($category && $refid) {
			$listid = $objWishlist->get_wishlistID($refid, $category);
		}
		
		// cannot rank a wish if list/wish is not found
		if(!$listid or !$wishid) {
			JError::raiseError( 404, JText::_('Cannot locate a wish or a wish list') );
			return;
		}	
		
		$wishlist = $objWishlist->get_wishlist($listid);
		$item = $objWish->get_wish ($wishid, $juser);
		
		// cannot proceed if wish id is not found
		if(!$wishlist or !$item) {
			JError::raiseError( 404, JText::_('Cannot locate a wish or a wish list') );
			return;
		}	
		
		// is this wish on correct list?
		if($listid != $wishlist->id){
			JError::raiseError( 404, JText::_('Wish not found on requested wish list') );
			return;
		}
		
		// Login required
		if ($juser->get('guest')) {
			$msg = 'Please login to add a wish.';
			$this->login($msg);
			return;
		}
		
		// get admin priviliges
		$this->authorize_admin($listid);
	
		
		// Need to be list admin
		if (!$this->_admin) {
			JError::raiseError( 404, JText::_('Action not authorized.') );
			return;
		}
		
		// did user make selections?
		if (!$effort or !$importance) {
			echo WishlistHtml::alert( 'Please make selections first' );
			exit();
		}
		
		
		// is the wish ranked already?
		if(isset($item->ranked) && !$item->ranked) {
			$objR->wishid = $wishid;
			$objR->userid = $juser->get('id');
			
		}
		else {
			// edit rating
			$objR->load_vote($juser->get('id'), $wishid);
			
		}
		
		$objR->voted = date( 'Y-m-d H:i:s', time() );
		$objR->importance = $importance;
		$objR->effort = $effort;
		
		// Check content
		if (!$objR->check()) {
			echo WishlistHtml::alert( $objR->getError() );
			exit();
		}

		// Store new content
		if (!$objR->store()) {
			echo WishlistHtml::alert( $objR->getError() );
			exit();
		}
		else {
			// update priority on all wishes
			$this->listid = $wishlist->id;
			$this->rank();
		}
		
		
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$wishid);
		
	}
	
	//-----------

	public function rank()
	{
		
		if(!$this->listid) {
		 return false;
		}
		
		// get admin priviliges
		$this->authorize_admin($this->listid);
		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$filters = $this->getFilters();
		
		$objWishlist = new Wishlist ( $database );
		$objWish = new Wish ( $database );
		$objOwner = new WishlistOwner( $database );
		$objR = new WishRank ( $database );
		
		$wishlist = $objWishlist->get_wishlist($this->listid);
		$wishlist->items = $objWish->get_wishes($this->listid, $filters, $this->_admin, $juser);
	
		$weight_e = 4;
		$weight_i = 5;
		$weight_f = 0.5;
		$f_threshold = 5;
		$co = 0.5;
		
		
		if($wishlist->items) {
			
			$owners = $objOwner->get_owners($this->listid, $this->admingroup , $wishlist);
			$owners   =  $owners['individuals'];
			
			foreach($wishlist->items as $item) {
				
				$votes = $objR->get_votes($item->id);
				$ranking = 0;
				
				// first consider votes by list owners
				if($votes) {
					$imp 	= 0;
					$eff 	= 0;
					$num 	= 0;
					
					foreach($votes as $vote) {					
						if(in_array($vote->userid, $owners)) {
							// vote must come from list owner!							
							$num++;
							$imp += $vote->importance;
							$eff += $vote->effort;
						}
						else {
							// need to clean up this vote! looks like owners list changed since last voting
							$remove = $objR->remove_vote( $item->id, $vote->userid );
						}					
					}
					
					// average values
					$imp = $imp/$num;
					$eff = $eff/$num;
										
					// we need to factor in how many people voted 
					$certainty = $co + $num/count($owners);
					
					$ranking += ($imp * $weight_i) * $certainty;
					$ranking += ($eff * $weight_e) * $certainty;
					
				}
				
				// determine weight of community feedback
					$f = $item->positive + $item->negative;
					$q = $f/$f_threshold;
					$weight_f = ($weight_f >= 1) ? ($weight_f + $q * $weight_f) : $weight_f;
									
					$ranking += ($item->positive * $weight_f);
					$ranking -= ($item->negative * $weight_f);
				
				
				// Do we have a due date?
				if($item->due) {
					
					$today = date( 'Y-m-d H:i:s');
					// TBD
				}
								
				// Do not allow negative ranking
				$ranking = ($ranking < 0) ? 0 : $ranking;
				
				// save calculated priority
				$row = new Wish ( $database );
				$row->load($item->id);
				$row->ranking = $ranking;
		
				// store new content
				if (!$row->store()) {
					echo WishlistHtml::alert( $row->getError() );
					exit();
				}
				
			}
			
		}
	
	}
	
	

	//----------------------------------------------------------
	// Comments and Ratings
	//----------------------------------------------------------
	
	public function savereply()
	{
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		// Incoming
		$id      	= JRequest::getInt( 'referenceid', 0 );
		$listid 	= JRequest::getInt( 'listid', 0 );
		$wishid 	= JRequest::getInt( 'wishid', 0 );
		$ajax    	= JRequest::getInt( 'ajax', 0 );
		$category	= JRequest::getVar( 'cat', '' );
		$when 		= date( 'Y-m-d H:i:s');
		
		
		$obj = new Wishlist( $database );
		
		// Get wishlist info
		$wishlist = $obj->get_wishlist($listid);
		
		// trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
		
		if (!$id && !$ajax) {
			// cannot proceed
			$document =& JFactory::getDocument();
			$document->setTitle( $title );
			
			echo WishlistHtml::hed(2, $title);
			echo WishlistHtml::error( JText::_('No Wish ID found.') );
			return;
		}
		
		// is the user logged in?
		if ($juser->get('guest')) {
			$msg = 'Please login to post a comment';
			$this->login($msg);
			return;
		}
		
		if ($id && $category) {
			$row = new XComment( $database );
			if (!$row->bind( $_POST )) {
				echo WishlistHtml::alert( $row->getError() );
				exit();
			}
			
			// Perform some text cleaning, etc.
			$row->comment   = $this->purifyText($row->comment);
			$row->comment   = nl2br($row->comment);
			$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
			$row->added   	= $when;
			$row->state     = 0;
			$row->category  = $category;
			$row->added_by 	= $juser->get('id');
			
			// Check for missing (required) fields
			if (!$row->check()) {
				echo WishlistHtml::alert( $row->getError() );
				exit();
			}
			// Save the data
			if (!$row->store()) {
				echo WishlistHtml::alert( $row->getError() );
				exit();
			}
		}
	
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$wishid);
	}
	
	//-----------
	
	public function reply()
	{	
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Retrieve a review or comment ID and category
		$listid 	 = JRequest::getInt( 'id', 0 );
		$wishid  = JRequest::getInt( 'wishid', 0 );
		$rid 	 = JRequest::getInt( 'refid', 0 );
		$cat 	 = JRequest::getVar( 'cat', '' );
		$page 	 = JRequest::getVar( 'page', 1 );
	
		
		// is the user logged in?
		if ($juser->get('guest')) {
			$msg = 'Please login to post a comment';
			$this->login($msg);
			return;
		}
		
		// Do we have an ID?
		if (!$wishid) {
			// cannot proceed
			return;
		}
		// Do we have a category?
		if (!$cat) {
			// cannot proceed
			return;
		}
		

		$this->referenceid = $rid;
		$this->cat = $cat;
		$this->wishid = $wishid;
		//$this->listid = $id;
			
		$this->wish();	
	}
	
	//----------------
	
	public function rateitem()
	{		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		
		$id 	 = JRequest::getInt( 'refid', 0 );
		$ajax 	 = JRequest::getInt( 'ajax', 0 );
		$page 	 = JRequest::getVar( 'page', 'wishlist' );
		$cat 	 = 'wish';
		$vote 	 = JRequest::getVar( 'vote', '' );
		$ip 	 = $this->ip_address();
		
		
		if(!$id) {
			// cannot proceed		
			return;
		}
		
		// is the user logged in?
		if ($juser->get('guest')) {
			$this->login( JText::_('Please login to vote') );
			return;
		}
		else {
			// load wish
			$row = new Wish( $database );
			$row->load( $id );
			
			$objWishlist = new Wishlist( $database );
			$listid = $row->wishlist;
			$wishlist = $objWishlist->get_wishlist($listid);
					
			$voted = $row->get_vote ($id, $cat, $juser->get('id'));
					
			if(!$voted && $row->proposed_by != $juser->get('id')) {
							
				require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'vote.class.php' );
				$v = new Vote( $database );
				$v->referenceid = $id;
				$v->category = $cat;
				$v->voter = $juser->get('id');
				$v->ip = $ip;
				$v->voted = date( 'Y-m-d H:i:s', time() );
				$v->helpful = $vote;
				if (!$v->check()) {
					$this->setError( $v->getError() );
					return;
				}
				if (!$v->store()) {
					$this->setError( $v->getError() );
					return;
				}
				else {
					// update priority on all wishes
					$this->listid = $listid;
					$this->rank();
				}
			}
						
			// update display
			if($ajax) {
				$wish = $row->get_wish ($id, $juser);
				echo WishlistHtml::rateitem($wish, $juser, $this->_option, $listid);
			}
			else {
				if($page == 'wishlist') {
					$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid);
				}
				else {
					$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$id);
				}
			}
		}
			
	}
	
	//----------------------------------------------------------
	// Misc retrievers
	//----------------------------------------------------------
	
	public function getComments($itemid, $category, $level, $abuse=false, $owners, $admin)
	{
			$database =& JFactory::getDBO();
			
			$level++;
			$hc = new XComment( $database );
			
			$comments = $hc->getResults( array('id'=>$itemid, 'category'=>$category) );
			
			if ($comments) {
				foreach ($comments as $comment) 
				{
					$comment->replies = WishlistController::getComments($comment->id, 'wishcomment', $level, $abuse, $owners, $admin);
					if ($abuse) {
						$comment->reports = WishlistController::getAbuseReports($comment->id, 'wishcomment');
					}
					
					$comment->admin = 0;
					if(in_array($comment->added_by, $owners)) {
						$comment->admin = 1;  // this is a comment by list owner
					}
					
				}
			}
		
		return $comments;
	}
		
	//-----------
		
	public function getAbuseReports($item, $category)
	{
		$database =& JFactory::getDBO();
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php' );
		$ra = new ReportAbuse( $database );
		return $ra->getCount( array('id'=>$item, 'category'=>$category) );
	}
	//-----------
		
	public function getBonus($id) {
		$database =& JFactory::getDBO();
		$BT = new BankTransaction( $database);
		$bonus = $BT->getTransactions( 'wish', 'hold', $id );
		$bonus = $bonus ? array('sum'=>$bonus[0]->sum, 'num'=>$bonus[0]->total) : array('sum'=>0, 'num'=>0);
		
		return $bonus;
	}
		
	//-----------
	
	public function getFilters($admin=0)
	{
		// Query filters defaults
		$filters = array();
		$filters['sortby'] = trim(JRequest::getVar( 'sortby', '' ));
		$filters['filterby'] = trim(JRequest::getVar( 'filterby', 'all' ));	
		$filters['search'] = trim(JRequest::getVar( 'search', '' ));

		if($admin) {	$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : 'ranking'; }
		else { 
			$default = $this->banking ? 'bonus' : 'date';
			$filters['sortby'] = ($filters['sortby']) ? $filters['sortby'] : $default; 
		}

		// Paging vars
		$filters['limit'] = JRequest::getInt( 'limit', 50 );
		$filters['start'] = JRequest::getInt( 'limitstart', 0, 'get' );
		
		$filters['comments'] = JRequest::getVar( 'comments', 1, 'get');


		// Return the array
		return $filters;
	}
	//------------
	
	public function authorize_admin($listid = 0, $admin = 0)
	{

		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				$admin = 1;
			}
		}

		$juser =& JFactory::getUser();
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			$admin = 1;
		}
		
		if($listid) {
		// Get list administrators
		$database =& JFactory::getDBO();
		$objOwner = new WishlistOwner( $database );
		$owners = $objOwner->get_owners($listid,  $this->admingroup );
		$owners =  $owners['individuals'];
				
			if(!$juser->get('guest')) {
				if(in_array($juser->get('id'), $owners)) {
					$admin = 2;  // individual group owner
				}
			
			}
		}
		

		$this->_admin = $admin;
	}
	
	//---------------

	public function userSelect( $name, $ownerids, $active, $nouser=0, $javascript=NULL, $order='a.name' ) 
	{
		$database =& JFactory::getDBO();

		$query = "SELECT a.id AS value, a.name AS text"
			  . "\n FROM #__users AS a"
			  . "\n WHERE a.block = '0' ";
		if(count($ownerids) > 0) {	  
		$query .= "AND (a.id IN (";
		$tquery = '';
			foreach ($ownerids as $owner) {
				$tquery .= "'".$owner."',";
			}
		$tquery = substr($tquery,0,strlen($tquery) - 1);
		
		$query .= $tquery.")) ";
		}
		else {
		$query .= " AND 2=1 ";
		}
		$query .= "\n ORDER BY ". $order;

		$database->setQuery( $query );
		if ( $nouser ) {
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
			$users = array_merge( $users, $database->loadObjectList() );
		} else {
			$users = $database->loadObjectList();
		}
		
		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false );

		return $users;
	}
	
	
	//----------------------------------------------------------
	// Misc
	//----------------------------------------------------------

	public function startPath ($wishlist, $title, $pathway) {
		
		
		// build return path to resource
		if(isset($wishlist->resource) && isset($wishlist->resource->typetitle)) {
				$normalized_valid_chars = 'a-zA-Z0-9';
				$typenorm = preg_replace("/[^$normalized_valid_chars]/", "", $wishlist->resource->typetitle);
				$typenorm = strtolower($typenorm);
				
				$pathway->addItem( JText::_('Resources'), 'index.php?option=com_resources' );
				$pathway->addItem( ucfirst(JText::_($wishlist->resource->typetitle)), JRoute::_('index.php?option=com_resources'.a.'type='.$typenorm));
				$pathway->addItem(stripslashes($wishlist->resource->title),JRoute::_('index.php?option=com_resources'.a.'id='.$wishlist->referenceid));
				$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid );
				
		}
		else {
			$pathway->addItem( $title, 'index.php?option='.$this->_option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid );
		}
		
	}
	
	//-------------

	public function send_email($hub, $email, $subject, $message) 
	{
		if ($hub) {
			$contact_email = $hub['email'];
			$contact_name  = $hub['name'];

			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
			$headers .= 'From: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= 'Reply-To: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '. $hub['name'] .n;
			if (mail($email, $subject, $message, $headers, $args)) {
				return(1);
			}
		}
		return(0);
	}

	//-----------

	public function check_validEmail($email) 
	{
		if(eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}

	//-----------

	public function server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}
	

	//-----------

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	function useAbuse ($abuse = 0) 
	{
		/*if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php')) {
			$abuse = 1;
		}
				
		return $abuse;
		*/
		return 1;
	}
	
	//-----------
	
	function useComments ($reply = 0) 
	{
		/*
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php')) {
			$reply = 1;
		}
		
		return $reply;
		*/
		return 1;
	}
	
	//-----------
	
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
		if($number != 1) $periods[$val].= "s";
		
		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= WishlistController::TimeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		$text = $this->timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		//$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}

	//-----------
	
	public function valid_ip($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}
	
	//-----------

	public function ip_address()
	{
		if ($this->server('REMOTE_ADDR') AND $this->server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->server('REMOTE_ADDR')) {
			 $ip_address = $_SERVER['REMOTE_ADDR'];
		} elseif ($this->server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->server('HTTP_X_FORWARDED_FOR')) {
			 $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		if ($ip_address === FALSE) {
			$ip_address = '0.0.0.0';
			return $ip_address;
		}
		
		if (strstr($ip_address, ',')) {
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}
		
		if (!$this->valid_ip($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}
	
	//------------
	
	public function purifyText( &$text ) 
	{
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		return $text;
	}

	
}
?>
