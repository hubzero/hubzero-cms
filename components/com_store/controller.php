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

class StoreController
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
		// Get the component parameters
		$aconfig =& JComponentHelper::getParams( 'com_answers' );
		$this->infolink =  $aconfig->get('infolink') ? $aconfig->get('infolink') : '/kb/points/';
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if(isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
		
	//-----------
	
	private function getTask()
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
	
		$config = new StoreConfig($this->_option);
		$this->config = $config;
		$store_enabled = (isset($this->config->parameters['store_enabled'])) ? $this->config->parameters['store_enabled'] : 0;
		
		
		if(!$store_enabled) {
			// redirect to home page
			$this->_redirect = '/';
			$this->redirect();		
		}
		
		switch( $this->getTask() ) 
		{
			case 'cart':        $this->cart();        		break;
			case 'checkout':    $this->checkout();    		break;
			case 'process':     $this->process_order();     break;
			case 'finalize':    $this->finalize_order();    break;
			case 'accept':      $this->accept();      		break;
			
			default: $this->storefront(); break;
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
	
	//-----------

	protected function login($msg) 
	{
		// Set the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$japp =& JFactory::getApplication();
		$pathway =& $japp->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
		
		echo StoreHtml::div( StoreHtml::hed( 2, $title ), 'full', 'content-header' );
		echo '<div class="main section">'.n;
		if ($msg) {
			echo StoreHtml::warning( $msg );
		}
		ximport('xmodule');
		XModuleHelper::displayModules('force_mod');
		echo '</div><!-- / .main section -->'.n;
	}

	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	private function getStyles($option='') 
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet('com_'.$option);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}
	}
	
	//-----------
	private function getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.'com_'.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.'com_'.$option.DS.$name.'.js');
			}
		} else {
			
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//-----------

	private function storefront() 
	{
		$database 		=& JFactory::getDBO();
		$xhub 			=& XFactory::getHub();
		$hubShortName 	= $xhub->getCfg('hubShortName');
			
		
		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 25 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$filters['sortby']   = JRequest::getVar( 'sortby', '' );
			
		// Set the page title
		$title = $hubShortName.' '.JText::_(strtoupper($this->_name)).': '.JText::_('STOREFRONT');
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// add the CSS to the template and set the page title
		$this->getStyles();
		$this->getScripts();
		$this->getScripts("resources");
		
		// get the most recent store items
		$obj = new Store($database);
		$items = $obj->getItems( 'retrieve', $filters, $this->config);
		$itemlist = StoreHtml::htmlItemList( $items, $this->_option, $this->infolink);
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}

		// output HTML
		echo StoreHtml::introduction($title, $itemlist, $featured ='', $this->_option, $filters, $this->infolink);
	}

	//-----------
	private function cart() 
	{
		$database 		=& JFactory::getDBO();
		$xhub 			=& XFactory::getHub();
		$juser 			=& JFactory::getUser();
		$hubShortName 	= $xhub->getCfg('hubShortName');
		$now    		= date( 'Y-m-d H:i:s', time() );
		$cartitems 		= array();
		$cost			= 0;
		$msg			= '';

		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking =  $upconfig->get('bankAccounts');
			
		// incoming
		$action = JRequest::getVar( 'action', '');
		$id 	= JRequest::getInt( 'item', 0 );
		//$purchasetype = $this->getPurchaseType(JRequest::getInt( 'type', 1));
		
		// Set the page title
		$title = $hubShortName.' '.JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
			
		// check if item exists
		if($id) {
			$objStore = new Store( $database );
			$iteminfo = $objStore->getInfo($id);
			if(!$iteminfo) {
				$id = 0;
			}
			else {
				$purchasetype = $this->getPurchaseType($iteminfo[0]->type);
			}
		}
			
		if(!$banking) { // if economy functions are unavailable
			echo StoreHtml::div( StoreHtml::hed(2, $title), 'full', 'content-header' );
			echo StoreHtml::div( StoreHtml::warning('MSG_STORE_CLOSED'), 'main section' );
			return;
		}
			
		// Need to login to view cart
		if ($juser->get('guest')) {
			return $this->login(JText::_('MSG_LOGIN_TO_CART'));
		}
		
		// Get cart object
		$item = new Cart( $database );
				
		if($action) {
			switch ($action)
			{
				case 'add': 
					// check if item is already there, then update quantity or save new
					$found = $item->checkCartItem( $id, $juser->get('id'));		
			
					if(!$found && $id) {					
						$item->itemid = $id;
						$item->uid 	= $juser->get('id');
						$item->type = $purchasetype;
						$item->added = $now;
						$item->quantity = 1;
						$item->selections = '';
	
						// store new content
						if (!$item->store()) {
							echo StoreHtml::alert( $item->getError() );
							exit();
						}
						else {
							$msg = JText::_('MSG_ADDED_TO_CART');
						}	
					}
					  
					    
				break;
				
				case 'update': 
					// update quantaties and selections					
					$item->saveCart(array_map('trim',$_POST), $juser->get('id'));
					      
				break;
				
				case 'remove': 
					// update quantaties and selections
					if($id) {
					$item->deleteCartItem($id, $juser->get('id'));	
					}			
					      
				break;
				
				case 'empty': 
					// empty all
					$item->deleteCartItem('', $juser->get('id'), 'all');
					 		
				break;
				
				default:  
				// do nothing      		
				break;
			}
			
		}
		
		// check available user funds		
		$BTL 		= new BankTeller( $database, $juser->get('id') );
		$balance 	= $BTL->summary();
		$credit 	= $BTL->credit_summary();
		$funds 		= $balance - $credit;			
		$funds 		= ($funds > 0) ? $funds : '0';
	
		// calculate total
		$cost = $item->getCartItems($juser->get('id'), 'cost');
		
		// get cart items		
		$items = $item->getCartItems($juser->get('id'));
		$cartitems = StoreHtml::htmlCartList( $items, $this->_option, $funds, $cost, $this->infolink);
		
		// add the CSS to the template and set the page title
		$this->getStyles();
		$this->getScripts();
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
		
		// output HTML
		echo StoreHtml::cart($title, $cartitems, $this->_option, $funds, $cost, $msg, $this->infolink, $juser);
		
	}
	//-----------
	
	private function checkout() 
	{
		$database =& JFactory::getDBO();
		$juser 			=& JFactory::getUser();
		$xuser 			=& XFactory::getUser();
		$xhub 			=& XFactory::getHub();
		$hubShortName 	= $xhub->getCfg('hubShortName');
		
	
		// Check authorization
		if ($juser->get('guest')) {
			return $this->login(JText::_('MSG_LOGIN_CHECKOUT'));
		}
		
		// Get cart object
		$item = new Cart( $database );
			
		// update quantaties and selections					
		$item->saveCart(array_map('trim',$_POST), $juser->get('id'));
		
		// calculate total
		$cost = $item->getCartItems($juser->get('id'), 'cost');
		
		// check available user funds		
		$BTL 		= new BankTeller( $database, $juser->get('id'));
		$balance 	= $BTL->summary();
		$credit 	= $BTL->credit_summary();
		$funds 		= $balance - $credit;			
		($funds > 0) ? $funds : '0';
		
		// output HTML
		if($cost > $funds) {
			$this->cart(); 
			return;
		}
		
		// get cart items		
		$items = $item->getCartItems($juser->get('id'));
		
		// clean-up unavailable items
		$item->deleteUnavail($juser->get('id'), $items);
				
		// updated item list		
		$items = $item->getCartItems($juser->get('id'));
		
		// Set the page title
		$title = $hubShortName.' '.JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// add the CSS to the template and set the page title
		$this->getStyles();
		$this->getScripts();
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_('CART'), 'index.php?option='.$this->_option.a.'task=cart' );
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
		
		echo StoreHtml::checkout($title, $items, $this->_option, $funds, $cost,  $xuser, array() ,'', $this->infolink);
		
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	private function finalize_order() 
	{
		$database =& JFactory::getDBO();
		$xuser 			=& XFactory::getUser();
		$juser 			=& JFactory::getUser();
		$xhub 			=& XFactory::getHub();
		$hubShortName 	= $xhub->getCfg('hubShortName');
		$success		= 1;
		$error			= 0;
		$msg			= '';
		$now    		= date( 'Y-m-d H:i:s', time() );
		$email 			= 1;  // turn on/off
		
		// Check authorization
		if ($juser->get('guest')) {
			return $this->login();
		}
		
		// Get cart object
		$item = new Cart( $database );
		
		// calculate total
		$cost = $item->getCartItems($juser->get('id'),'cost');
		
				
		// check available user funds		
		$BTL 		= new BankTeller( $database, $xuser->get('uid') );
		$balance 	= $BTL->summary();
		$credit 	= $BTL->credit_summary();
		$funds 		= $balance - $credit;			
		($funds > 0) ? $funds : '0';
		
		// get cart items		
		$items = $item->getCartItems($juser->get('id'));
		
		if(!$items) { // if user clicked on back button in browser
			$this->cart();
			return;
		}
				
		// get shipping info 
		$shipping = array_map('trim',$_POST);
	
		// make sure email address is valid
		$validemail = $this->check_validEmail($shipping['email']);
		$email = ($validemail) ? $shipping['email'] : $xuser->get('email');
		//$country = htmlentities(getcountry($shipping['country']));
		$country = $shipping['country'];
			
			// format posted info
			$details  = JText::_('SHIP_TO').':'.r.n;
			$details .= $shipping['name'] .r.n;
			$details .= $this->purifyText($shipping['address']).r.n;
			$details .= JText::_('COUNTRY').': '. $country.r.n;			
			$details .= '----------------------------------------------------------'.r.n;
			$details .= JText::_('CONTACT').': '.r.n;
			if($shipping['phone']) {
			$details .= $shipping['phone'] .r.n;
			}
			$details .= $email .r.n;
			$details .= '----------------------------------------------------------'.r.n;
			$details .= JText::_('DETAILS').': ';
			$details .= ($shipping['comments']) ? r.n.($this->purifyText($shipping['comments'])) : 'N/A';
			
			
			// register a new order
			$order 				= new Order( $database );
			$order->uid 		= $xuser->get('uid');
			//$order->type 		= 'merchandise';
			$order->total		= $cost;
			$order->status 		= '0'; // order placed
			$order->ordered 	= $now;
			$order->email 		= $email;
			$order->details 	= $details;
			
			// store new content
			if (!$order->store()) {
				echo StoreHtml::alert( $order->getError() );
				exit();
			}
		
			// get order id
			$objO = new Order($database);
			$orderid = $objO->getOrderID($juser->get('id'), $now);
			
			if($orderid) {
			// transfer cart items to order
				foreach($items as $item) {
					$orderitem 				= new OrderItem( $database );
					$orderitem->uid 		= $juser->get('id');
					$orderitem->oid 		= $orderid;
					$orderitem->itemid 		= $item->itemid;
					$orderitem->price 		= $item->price;
					$orderitem->quantity	= $item->quantity;
					$orderitem->selections 	= $item->selections;
										
					// save order item
					if (!$orderitem->store()) {
						echo StoreHtml::alert( $orderitem->getError() );
						exit();
					}
					// delete item from cart
				
					$sql = "DELETE FROM #__cart WHERE itemid='".$item->itemid."' AND type='merchandise' AND uid=".$xuser->get('uid');
					$database->setQuery( $sql);
					if (!$database->query()) {
						echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
						exit;
					}
				}
				
				// put the purchase amount on hold
				$BTL = new BankTeller( $database, $xuser->get('uid') );
				$BTL->hold($order->total, JText::_('BANKING_HOLD'), 'store', $orderid);
				
				// send confirmation email
				
				//if($email) {
					$admin_email = $xhub->getCfg('hubSupportEmail');
					$subject     = JText::_(strtoupper($this->_name)).': '.JText::_('ORDER').' #'.$orderid;
					$from        = $hubShortName.' '.JText::_(strtoupper($this->_name));
					$hub         = array('email' => $admin_email, 'name' => $from);
					
					// compose email message
					$emailbody  = JText::_('THANKYOU').' '.JText::_('IN_THE').' '.$hubShortName.' '.JText::_(strtolower($this->_name)).'!'.r.n.r.n;
					$emailbody .= JText::_('EMAIL_KEEP').r.n;
					$emailbody .= '----------------------------------------------------------'.r.n;
					$emailbody .= '	'.JText::_('ORDER').' '. JText::_('NUM').': '. $orderid .r.n;
					$emailbody .= ' '.JText::_('ORDER').' '.JText::_('TOTAL').': '. $cost.' '.JText::_('POINTS').r.n;
					$emailbody .= ' '.JText::_('PLACED').' '. JHTML::_('date', $now, '%d %b, %Y').r.n;
					$emailbody .= ' '.JText::_('STATUS').': '.JText::_('RECEIVED').r.n;
					$emailbody .= '----------------------------------------------------------'.r.n;
					$emailbody .= $details.r.n;
					$emailbody .= '----------------------------------------------------------'.r.n.r.n;
					$emailbody .= JText::_('EMAIL_ORDER_PROCESSED').'. ';
					$emailbody .= JText::_('EMAIL_QUESTIONS').'.'.r.n.r.n;
					$emailbody .= JText::_('EMAIL_THANKYOU').r.n;
														
					//$this->send_email($hub, $email, $subject, $emailbody);
					//ximport('xhubhelper');
					//XHubHelper::send_email($email, $subject, $emailbody);
				//}
				JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher =& JDispatcher::getInstance();
				if (!$dispatcher->trigger( 'onSendMessage', array( 'store_notifications', $subject, $emailbody, $hub, array($juser->get('id')), $this->_option ))) {
					$this->setError( JText::_('Failed to message users.') );
				}
			}
			
		
		// Set the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('THANKYOU');
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// add the CSS to the template
		$this->getStyles();
		$this->getScripts();
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_('CART'), 'index.php?option='.$this->_option.a.'task=cart' );
		$pathway->addItem( JText::_('CHECKOUT'), 'index.php?option='.$this->_option.a.'task=checkout' );
		$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
			
		// output HTML			
		echo StoreHtml::order_completed($this->_option, $orderid, $this->infolink, $title);
		
		
	}
	//------------
	private function process_order() 
	{
		// process order, user yet has to finalize it
		$database 		=& JFactory::getDBO();
		$juser 			=& JFactory::getUser();
		$xuser 			=& XFactory::getUser();
		$xhub 			=& XFactory::getHub();
		$hubShortName 	= $xhub->getCfg('hubShortName');
		$success		= 1;
		$error			= 0;
		$now    		= date( 'Y-m-d H:i:s', time() );
		$action 		= JRequest::getVar( 'action', '');
		
		// Check authorization
		if ($juser->get('guest')) {
			return $this->login();
		}
		
		// Get cart object
		$item = new Cart( $database );
		
		// calculate total
		$cost = $item->getCartItems($juser->get('id'),'cost');
		
		if(!$cost) {
			$error = JText::_('ERR_EMPTY_ORDER');
			$success = 0;
		}
		
		// check available user funds		
		$BTL 		= new BankTeller( $database, $juser->get('id') );
		$balance 	= $BTL->summary();
		$credit 	= $BTL->credit_summary();
		$funds 		= $balance - $credit;			
		($funds > 0) ? $funds : '0';
		
		if($cost > $funds) { 
			$error = JText::_('MSG_NO_FUNDS');
			$success = 0;
		}
		
		// get cart items		
		$items = $item->getCartItems($juser->get('id'));
				
		// get shipping info 
		$shipping = array_map('trim',$_POST);
			//if($shipping['name'] && $shipping['address1'] && $shipping['city'] && $shipping['country']  && $shipping['postal']) {
			if($shipping['name'] && $shipping['address'] && $shipping['country']) {
				$success = 1;
			}
			else {
				$error	 = JText::_('ERR_BLANK_FIELDS');
				$success = 0;
			}
			
		// Set the page title
		$title = $hubShortName.' '.JText::_(strtoupper($this->_name)).': '.JText::_('VERIFY_ORDER');
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// add the CSS to the template
		$this->getStyles();
		$this->getScripts();
	
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_('CART'), 'index.php?option='.$this->_option.a.'task=cart' );
		//$pathway->addItem( JText::_(strtoupper($this->_task)), 'index.php?option='.$this->_option.a.'task='.$this->_task );
	
		if($success && $action!='change') {
			// output HTML			
			echo StoreHtml::finalize($title, $items, $this->_option, $funds, $cost, $xuser, $shipping, $this->infolink);
		
		} 
		else {
			echo StoreHtml::checkout($hubShortName.' '.JText::_(strtoupper($this->_name)).': '.JText::_('CHECKOUT'), $items, $this->_option, $funds, $cost, $xuser, $shipping, $error, $this->infolink);
		}
		
	}
	//-----------
	
	public function getPurchaseType($num) {
		switch ( $num ) 
		{
			case '1':         $out=JText::_('MERCHANDISE');     break;
			case '2':         $out=JText::_('SERVICE');      	break;
			
			default: 		  $out=JText::_('MERCHANDISE');		break;
			
		}
		return $out;
	}
	
	//-----------

	private function send_email($hub, $email, $subject, $message) 
	{
		if ($hub) {
			$contact_email = $hub['email'];
			$contact_name  = $hub['name'];

			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
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

	private function check_validEmail($email) 
	{
		if(eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}


	//----------------------------------------------------------
	// Misc Functions
	//----------------------------------------------------------
	
	//-----------

	private function purifyText( &$text ) 
	{
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = strip_tags( $text );

		return $text;
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
			$text .= StoreController::TimeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		$text =  StoreController::timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		//$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}
	//-----------
	private function authorize() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
	
		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				return true;
			}
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}
		

		return false;
	}
}
?>
