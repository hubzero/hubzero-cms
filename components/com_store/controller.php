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

class StoreController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

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
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	public function execute()
	{
		// Check if the store is enabled or not
		//$this->config = new StoreConfig($this->_option);
		$component =& JComponentHelper::getComponent( $this->_option );
		if (!trim($component->params)) {
			return $this->abort();
		} else {
			$config =& JComponentHelper::getParams( $this->_option );
		}
		$this->config = $config;
		
		if (!$this->config->get('store_enabled')) {
			// Redirect to home page
			$this->_redirect = '/';
			return;
		}
		
		$this->_task = JRequest::getVar( 'task', '', 'post' );
		if (!$this->_task) {
			$this->_task = JRequest::getVar( 'task', '', 'get' );
		}
		
		switch ($this->_task) 
		{
			case 'cart':     $this->cart();           break;
			case 'checkout': $this->checkout();       break;
			case 'process':  $this->process();  break;
			case 'finalize': $this->finalize(); break;
			case 'accept':   $this->accept();         break;
			
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

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function _getStyles($option='') 
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet('com_'.$option);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}
	}
	
	//-----------
	
	private function _getScripts($option='',$name='')
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
	
	private function _buildPathway() 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			switch ($this->_task) 
			{
				case 'finalize':
					$pathway->addItem( 
						JText::_(strtoupper($this->_option).'_CART'), 
						'index.php?option='.$this->_option.'&task=cart' 
					);
					$pathway->addItem( 
						JText::_(strtoupper($this->_option).'_CHECKOUT'), 
						'index.php?option='.$this->_option.'&task=checkout' 
					);
					$pathway->addItem(
						JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						'index.php?option='.$this->_option.'&task='.$this->_task
					);
				break;
				case 'process':
					$pathway->addItem( 
						JText::_(strtoupper($this->_option).'_CART'), 
						'index.php?option='.$this->_option.'&task=cart' 
					);
				break;
				default:
					$pathway->addItem(
						JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						'index.php?option='.$this->_option.'&task='.$this->_task
					);
				break;
			}
		}
	}
	
	//-----------
	
	private function _buildTitle() 
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task) {
			$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}
	
	//-----------
	
	private function _getPurchaseType($num) 
	{
		switch ($num) 
		{
			case '1': $out = JText::_('COM_STORE_MERCHANDISE'); break;
			case '2': $out = JText::_('COM_STORE_SERVICE');     break;
			default:  $out = JText::_('COM_STORE_MERCHANDISE'); break;
			
		}
		return $out;
	}

	//-----------

	private function _checkValidEmail($email) 
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}

	//-----------
	
	private function _authorize() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}

		return false;
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login() 
	{
		$view = new JView( array('name'=>'login') );
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function storefront() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'storefront') );
		$view->option = $this->_option;
		
		// Incoming
		$view->filters = array();
		$view->filters['limit']  = JRequest::getInt( 'limit', 25 );
		$view->filters['start']  = JRequest::getInt( 'limitstart', 0 );
		$view->filters['sortby'] = JRequest::getVar( 'sortby', '' );
		
		// Get the most recent store items
		$database =& JFactory::getDBO();
		$obj = new Store($database);
		$view->rows = $obj->getItems('retrieve', $view->filters, $this->config);
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();
		//$this->_getScripts("resources");
		
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		$view->infolink = $this->infolink;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------
	
	protected function cart() 
	{
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();
		
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		// Need to login to view cart
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}

		// Instantiate a new view
		$view = new JView( array('name'=>'cart') );
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->infolink = $this->infolink;
		$view->msg = '';

		// Check if economy functions are unavailable
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		if (!$upconfig->get('bankAccounts')) {
			$view->setError( JText::_('COM_STORE_MSG_STORE_CLOSED') );
			$view->display();
			return;
		}
		
		$database =& JFactory::getDBO();
			
		// Incoming
		$view->action = JRequest::getVar( 'action', '');
		$view->id = JRequest::getInt( 'item', 0 );

		// Check if item exists
		$purchasetype = '';
		if ($view->id) {
			$objStore = new Store( $database );
			$iteminfo = $objStore->getInfo($view->id);
			if (!$iteminfo) {
				$view->id = 0;
			} else {
				$purchasetype = $this->_getPurchaseType($iteminfo[0]->type);
			}
		}

		// Get cart object
		$item = new Cart( $database );
				
		switch ($view->action)
		{
			case 'add': 
				// Check if item is already there, then update quantity or save new
				$found = $item->checkCartItem($view->id, $juser->get('id'));		
		
				if (!$found && $view->id) {					
					$item->itemid = $view->id;
					$item->uid = $juser->get('id');
					$item->type = $purchasetype;
					$item->added = date( 'Y-m-d H:i:s', time() );
					$item->quantity = 1;
					$item->selections = '';

					// store new content
					if (!$item->store()) {
						JError::raiseError( 500, $item->getError() );
						return;
					}
					
					$view->msg = JText::_('MSG_ADDED_TO_CART');	
				}
			break;
			
			case 'update': 
				// Update quantaties and selections					
				$item->saveCart(array_map('trim',$_POST), $juser->get('id'));
			break;
			
			case 'remove': 
				// Update quantaties and selections
				if ($view->id) {
					$item->deleteCartItem($view->id, $juser->get('id'));	
				}			
			break;
			
			case 'empty': 
				// Empty all
				$item->deleteCartItem('', $juser->get('id'), 'all');
			break;
			
			default:  
				// Do nothing      		
			break;
		}
		
		// Check available user funds		
		$BTL = new BankTeller( $database, $juser->get('id') );
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;			
		$view->funds = ($funds > 0) ? $funds : 0;
	
		// Calculate total
		$view->cost = $item->getCartItems($juser->get('id'), 'cost');
		
		// Get cart items		
		$view->rows = $item->getCartItems($juser->get('id'));

		// Output HTML
		$view->juser = $juser;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function checkout() 
	{
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();
		
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Check authorization
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'checkout') );
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->infolink = $this->infolink;
		$view->final = false;
		
		$database =& JFactory::getDBO();
		
		// Get cart object
		$item = new Cart( $database );
			
		// Update quantaties and selections					
		$item->saveCart(array_map('trim',$_POST), $juser->get('id'));
		
		// Calculate total
		$view->cost = $item->getCartItems($juser->get('id'), 'cost');
		
		// Check available user funds		
		$BTL = new BankTeller($database, $juser->get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;			
		$view->funds = ($funds > 0) ? $funds : '0';
		
		if ($view->cost > $view->funds) {
			$this->cart(); 
			return;
		}
		
		// Get cart items		
		$view->items = $item->getCartItems($juser->get('id'));
		
		// Clean-up unavailable items
		$item->deleteUnavail($juser->get('id'), $view->items);
				
		// Updated item list		
		$view->items = $item->getCartItems($juser->get('id'));
		
		// Output HTML
		$view->juser = $juser;
		$view->xprofile = new XProfile;
		$view->xprofile->load( $juser->get('id') );
		$view->posted = array();
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	protected function finalize() 
	{
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();
		
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Check authorization
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		$database =& JFactory::getDBO();

		$now = date( 'Y-m-d H:i:s', time() );
		
		// Get cart object
		$item = new Cart( $database );
		
		// Calculate total
		$cost = $item->getCartItems($juser->get('id'),'cost');
		
		// Check available user funds		
		$BTL = new BankTeller( $database, $juser->get('id') );
		$balance = $BTL->summary();
		$credit = $BTL->credit_summary();
		$funds = $balance - $credit;			
		$funds = ($funds > 0) ? $funds : '0';
		
		// Get cart items		
		$items = $item->getCartItems($juser->get('id'));
		if (!$items) {
			$this->cart();
			return;
		}
				
		// Get shipping info 
		$shipping = array_map('trim',$_POST);
	
		// make sure email address is valid
		$validemail = $this->_checkValidEmail($shipping['email']);
		$email = ($validemail) ? $shipping['email'] : $juser->get('email');
			
		// Format posted info
		$details  = JText::_('COM_STORE_SHIP_TO').':'."\r\n";
		$details .= $shipping['name'] ."\r\n";
		$details .= Hubzero_View_Helper_Html::purifyText($shipping['address'])."\r\n";
		$details .= JText::_('COM_STORE_COUNTRY').': '. $shipping['country']."\r\n";			
		$details .= '----------------------------------------------------------'."\r\n";
		$details .= JText::_('COM_STORE_CONTACT').': '."\r\n";
		if ($shipping['phone']) {
			$details .= $shipping['phone'] ."\r\n";
		}
		$details .= $email ."\r\n";
		$details .= '----------------------------------------------------------'."\r\n";
		$details .= JText::_('COM_STORE_DETAILS').': ';
		$details .= ($shipping['comments']) ? "\r\n".(Hubzero_View_Helper_Html::purifyText($shipping['comments'])) : 'N/A';
			
		// Register a new order
		$order = new Order($database);
		$order->uid     = $juser->get('id');
		$order->total   = $cost;
		$order->status  = '0'; // order placed
		$order->ordered = $now;
		$order->email   = $email;
		$order->details = $details;
		
		// Store new content
		if (!$order->store()) {
			JError::raiseError( 500, $order->getError() );
			return;
		}
		
		// Get order ID
		$objO = new Order($database);
		$orderid = $objO->getOrderID($juser->get('id'), $now);
			
		if ($orderid) {
			// Transfer cart items to order
			foreach ($items as $itm) 
			{
				$orderitem = new OrderItem( $database );
				$orderitem->uid        = $juser->get('id');
				$orderitem->oid        = $orderid;
				$orderitem->itemid     = $itm->itemid;
				$orderitem->price      = $itm->price;
				$orderitem->quantity   = $itm->quantity;
				$orderitem->selections = $itm->selections;
									
				// Save order item
				if (!$orderitem->store()) {
					JError::raiseError( 500, $orderitem->getError() );
					return;
				}
				
				// Delete item from cart
				if (!$item->deleteItem($itm->itemid, $juser->get('id'))) {
					JError::raiseError( 500, $item->getError() );
					return;
				}
			}
			
			// Put the purchase amount on hold
			$BTL = new BankTeller( $database, $juser->get('id') );
			$BTL->hold($order->total, JText::_('COM_STORE_BANKING_HOLD'), 'store', $orderid);
			
			$jconfig =& JFactory::getConfig();
			
			// Compose confirmation "from"
			$hub = array(
				'email' => $jconfig->getValue('config.mailfrom'), 
				'name' => $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_option))
			);
			
			// Compose confirmation subject
			$subject = JText::_(strtoupper($this->_name)).': '.JText::_('COM_STORE_ORDER').' #'.$orderid;
				
			// Compose confirmation message
			$eview = new JView( array('name'=>'emails','layout'=>'confirmation') );
			$eview->option = $this->_option;
			$eview->hubShortName = $jconfig->getValue('config.sitename');
			$eview->orderid = $orderid;
			$eview->cost = $cost;
			$eview->now = $now;
			$eview->details = $details;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);
			
			// Send confirmation
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'store_notifications', $subject, $message, $hub, array($juser->get('id')), $this->_option ))) {
				$this->setError( JText::_('COM_STORE_ERROR_MESSAGE_FAILED') );
			}
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'completed') );
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->infolink = $this->infolink;
		
		// Output HTML
		$view->juser = $juser;
		$view->orderid = $orderid;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//------------
	
	protected function process() 
	{
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();
		
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Check authorization
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Get cart object
		$item = new Cart( $database );
		
		// Calculate total
		$cost = $item->getCartItems($juser->get('id'),'cost');
		
		if (!$cost) {
			$this->setError( JText::_('COM_STORE_ERR_EMPTY_ORDER') );
		}
		
		// Check available user funds		
		$BTL = new BankTeller( $database, $juser->get('id') );
		$balance = $BTL->summary();
		$credit = $BTL->credit_summary();
		$funds = $balance - $credit;			
		$funds = ($funds > 0) ? $funds : '0';
		
		if ($cost > $funds) { 
			$this->setError( JText::_('COM_STORE_MSG_NO_FUNDS') );
		}
		
		// Get cart items		
		$items = $item->getCartItems($juser->get('id'));
				
		// Get shipping info 
		$posted = array_map('trim',$_POST);

		if (!$posted['name'] || !$posted['address'] || !$posted['country']) {
			$this->setError( JText::_('COM_STORE_ERR_BLANK_FIELDS') );
		}
		
		// Incoming
		$action = JRequest::getVar( 'action', '');
		
		// Output HTML
		if (!$this->getError() && $action != 'change') {
			// Instantiate a new view
			$view = new JView( array('name'=>'finalize') );
			$view->final = true;
		} else {
			// Instantiate a new view
			$view = new JView( array('name'=>'checkout') );
			$view->final = false;
		}
		
		// Output HTML
		$view->cost = $cost;
		$view->funds = $funds;
		$view->items = $items;
		$view->posted = $posted;
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->infolink = $this->infolink;
		$view->juser = $juser;
		$view->xprofile = new XProfile;
		$view->xprofile->load( $juser->get('id') );
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
}
?>