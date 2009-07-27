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

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

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
	
	private function getTask()
	{
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}

	
	//-----------
	
	public function execute( )
	{
		// Get the component parameters
		$sconfig = new StoreConfig( $this->_option );
		$this->config = $sconfig;
		
		$xhub =& XFactory::getHub();
		$banking = $xhub->getCfg('hubBankAccounts');
		$this->banking = $banking;
		
		if ($banking) {
			ximport( 'bankaccount' );
		}
	
		switch( $this->getTask() ) 
		{
			case 'orders': 		$this->orders();   		break;
			case 'storeitems': 	$this->storeitems();   	break;
			case 'publish': 	$this->state();   		break;
			case 'unpublish': 	$this->state();   		break;
			case 'avail': 		$this->state();   		break;
			case 'unavail': 	$this->state();   		break;
			case 'order': 		$this->order();   		break;
			case 'newitem': 	$this->storeitem();   	break;
			case 'storeitem': 	$this->storeitem();   	break;
			case 'cancel_i': 	$this->cancel();   		break;
			case 'saveorder': 	$this->saveorder();   	break; // save changes to order
			case 'saveitem': 	$this->saveitem();   	break; // save new store item/ changes
			case 'receipt': 	$this->receipt();   	break; // produce PDF receipt for purchase

			default: $this->orders(); break;
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
	private function getStyles() 
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'admin.'.$this->_name.'.css');
	}
	
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function orders() 
	{
		$database =& JFactory::getDBO();// paging limits
		// Get configuration
		$config = JFactory::getConfig();
		$store_enabled = (isset($this->config->parameters['store_enabled'])) ? $this->config->parameters['store_enabled'] : 0;
		
		// Get cart object
		$objOrder = new Order( $database );
		
		// Get paging variables
		$filter = array();
		$filters['limit'] 	= JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$filters['start'] 	= JRequest::getInt('limitstart', 0);
		$filters['filterby']= JRequest::getVar( 'filterby', 'all');
		$filters['sortby']  = JRequest::getVar( 'sortby', 'm.id DESC');
	
		
		// get record count
		$total = $objOrder->getOrders ('count', $filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$orders=$objOrder->getOrders('',$filters);
		
		if($orders) {
			foreach ($orders as $o) {
				$items = '';
				$sql = "SELECT r.itemid, s.title"
				. "\n FROM #__order_items AS r"
				. "\n LEFT JOIN #__store AS s ON s.id=r.itemid "
				. "\n WHERE r.oid=".$o->id;
				$database->setQuery( $sql );
				$results = $database->loadObjectList();
				
				foreach($results as $r) {
				$items .= $r->title;
				$items .= ($r != end($results)) ? '; ' : '';
				}
				$o->itemtitles = $items;
				
				$xuser =& XUser::getInstance($o->uid);
				$o->author = $xuser->get('login');
			}
		
		}
		
		
		// output HTML
		$this->getStyles();
		StoreHTML::orders( $orders, $pageNav, $this->_option, $filters);
		
	}

	//-----------

	protected function storeitems( )
	{
		$database =& JFactory::getDBO();// paging limits
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filter = array();
		$filters['limit'] = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['filterby']= JRequest::getVar( 'filterby', 'all');
		$filters['sortby']  = JRequest::getVar( 'sortby', 'date');

		$obj = new Store($database);
		$total = $obj->getItems( 'count', $filters, $this->config);

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );		
		$storeitems = $obj->getItems( 'retrieve', $filters, $this->config);
	
		
		// how many times ordered?
		if($storeitems) {
			foreach ($storeitems as $o) {
				$sql = "SELECT count(*)"
				. "\n FROM #__order_items AS r, #__store AS s, #__orders AS o "
				. "\n WHERE o.status=0"
				. "\n AND s.id=r.itemid"
				. "\n AND o.id=r.oid"
				. "\n AND r.itemid=".$o->id;
				$database->setQuery( $sql );
				$result = $database->loadResult();
				
				// active orders
				$o->activeorders = $result;
				
				// all orders
				$sql2 = "SELECT count(*)"
				. "\n FROM #__order_items AS r, #__store AS s "
				. "\n WHERE s.id=r.itemid"
				. "\n AND r.itemid=".$o->id;
				$database->setQuery( $sql2 );
				$result2 = $database->loadResult();
				$o->allorders = $result2;
				
			}
		
		}
		
		// output HTML
		$this->getStyles();
		StoreHTML::storeitems( $storeitems, $pageNav, $this->_option, $filters );
		
	}
	
	//-----------

	protected function receipt( ) 
	{
		$database =& JFactory::getDBO();
		$id 	= JRequest::getInt( 'id', 0 );
		
		$row = new Order( $database );
		$row->load( $id );
		
		$oi = new OrderItem( $database );
		
		if ($id) {		
		// get order items
				$orderitems = $oi->getOrderItems($id);
				if($orderitems) {
					foreach ($orderitems as $r) {
						$params 	 		=& new JParameter( $r->params );
						$selections  		=& new JParameter( $r->selections );
						
						// get size selection
						$r->sizes    		= $params->get( 'size', '' );
						$r->sizes 			= str_replace(" ","",$r->sizes);				
						$r->selectedsize    = trim($selections->get( 'size', '' ));
						$r->sizes    		= split(',',$r->sizes);
						$r->sizeavail		= in_array($r->selectedsize, $r->sizes) ? 1 : 0;
						
						// get color selection
						$r->colors    		= $params->get( 'color', '' );
						$r->colors 			= str_replace(" ","",$r->colors);				
						$r->selectedcolor   = trim($selections->get( 'color', '' ));
						$r->colors    		= split(',',$r->colors);
						
					}
				}
		
		$customer =& XUser::getInstance($row->uid);
		
		}
		
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'receipt.pdf.php' );
		$xhub =& XFactory::getHub();
		$hubname = $xhub->getCfg('hubShortName');
		
		$pdf=new PDF();
		$hubaddress = array();
		$hubaddress[] = isset($this->config->parameters['hubaddress_ln1']) ? $this->config->parameters['hubaddress_ln1'] : '' ;
		$hubaddress[] = isset($this->config->parameters['hubaddress_ln2']) ? $this->config->parameters['hubaddress_ln2'] : '' ;
		$hubaddress[] = isset($this->config->parameters['hubaddress_ln3']) ? $this->config->parameters['hubaddress_ln3'] : '' ;
		$hubaddress[] = isset($this->config->parameters['hubaddress_ln4']) ? $this->config->parameters['hubaddress_ln4'] : '' ;
		$hubaddress[] = isset($this->config->parameters['hubaddress_ln5']) ? $this->config->parameters['hubaddress_ln5'] : '' ;
		$hubaddress[] = isset($this->config->parameters['hubemail']) ? $this->config->parameters['hubemail'] : '' ;
		$hubaddress[] = isset($this->config->parameters['hubphone']) ? $this->config->parameters['hubphone'] : '' ;
		$pdf->hubaddress = $hubaddress;
		$pdf->url = $xhub->getCfg('hubLongURL').DS.'store';
		$pdf->headertext_ln1 = isset($this->config->parameters['headertext_ln1']) ? $this->config->parameters['headertext_ln1'] : '' ;
		$pdf->headertext_ln2 = isset($this->config->parameters['headertext_ln2']) ? $this->config->parameters['headertext_ln2'] : $hubname ;
		$pdf->footertext = isset($this->config->parameters['footertext']) ? $this->config->parameters['footertext'] : 'Thank you for contributions to our HUB!' ;
		$pdf->receipt_title = isset($this->config->parameters['receipt_title']) ? $this->config->parameters['receipt_title'] : 'Your Order' ;
		$pdf->receipt_note = isset($this->config->parameters['receipt_note']) ? $this->config->parameters['receipt_note'] : '' ;
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		// title
		$pdf->mainTitle();
		
		// order details
		if($id) {
		$pdf->orderDetails($customer, $row, $orderitems);
		
		}
		else {
		$pdf->Warning('No information available. Please supply order ID');
		}
	
				
		// thank-you line
		
		//for($i=1;$i<=40;$i++)
			//$pdf->Cell(0,10,'Printing line number '.$i,0,1);
		$pdf->Output();
		exit();
		
		
		
	
	}
	//-----------

	protected function order( ) 
	{
		$database =& JFactory::getDBO();
		$id 	= JRequest::getInt( 'id', 0 );
		
	
		$row = new Order( $database );
		$row->load( $id );
		
		$oi = new OrderItem( $database );
	
		if ($id) {		
		// get order items
				$orderitems = $oi->getOrderItems($id);
				if($orderitems) {
					foreach ($orderitems as $r) {
						$params 	 		=& new JParameter( $r->params );
						$selections  		=& new JParameter( $r->selections );
						
						// get size selection
						$r->sizes    		= $params->get( 'size', '' );
						$r->sizes 			= str_replace(" ","",$r->sizes);				
						$r->selectedsize    = trim($selections->get( 'size', '' ));
						$r->sizes    		= split(',',$r->sizes);
						$r->sizeavail		= in_array($r->selectedsize, $r->sizes) ? 1 : 0;
						
						// get color selection
						$r->colors    		= $params->get( 'color', '' );
						$r->colors 			= str_replace(" ","",$r->colors);				
						$r->selectedcolor   = trim($selections->get( 'color', '' ));
						$r->colors    		= split(',',$r->colors);
						
					}
				}
			$customer =& XUser::getInstance($row->uid);
			
			// check available user funds		
			$BTL 		= new BankTeller( $database, $row->uid);
			$balance 	= $BTL->summary();			
			$credit  	= $BTL->credit_summary();
			$funds   	= $balance;			
			$funds   	= ($funds > 0) ? $funds : '0';
		
		}
		else {
		$row = array();
		$orderitems = array();
		$customer = array();
		}	
	
		// output HTML
		StoreHTML::viewOrder( $row, $orderitems, $customer, $funds, $this->_option);
	}
	
	//-----------

	protected function storeitem( ) 
	{
		$database =& JFactory::getDBO();
		$id 	= JRequest::getInt( 'id', 0 );
	
		// load info from database
		$row = new Store( $database );
		$row->load( $id );
	
		if ($id) {		
		// Get parameters
				$params 		=& new JParameter( $row->params );
				$row->size    	= $params->get( 'size', '' );							
				$row->color  	= $params->get( 'color', '' );
		
		}
		else { // new item			
			$row->available     = 0;
			$row->created    	= date( 'Y-m-d H:i:s', time() );
			$row->published 	= 0;
			$row->featured 		= 0;
			$row->special		= 0;
			$row->type			= 1;
			$row->category		= 'wear';
		}
		
		// output HTML
		StoreHTML::viewItem( $row, $this->_option);
	}


	//----------------------------------------------------------
	//  Processers
	//----------------------------------------------------------
	
	protected function saveorder( ) 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$statusmsg = '';
		$email = 1; // turn emailing on/off
		$emailbody ='';
		ximport( 'bankaccount' );

		$data = array_map('trim',$_POST);
		$action = (isset($data['action'])) ? $data['action'] : '';
		$id = ($data['id']) ? $data['id'] : 0 ;
		$cost = intval($data['total']);
		$xhub 			=& XFactory::getHub();
		$hubShortName 	= $xhub->getCfg('hubShortName');
				
		if($id) {
			// initiate extended database class
			$row = new Order( $database );
			$row->load( $id );
			$row->notes = TextFilter::cleanXss($data['notes']);
			$hold = $row->total;
			$row->total = $cost;
			
			// get user bank account
			$xuser =& XUser::getInstance( $row->uid );
			$BTL_Q = new BankTeller( $database, $xuser->get('uid') );
			
			// start email message
			$emailbody .= JText::_('THANKYOU').' '.JText::_('IN_THE').' '.$hubShortName.' '.JText::_('STORE').'!'.r.n.r.n;
			$emailbody .= JText::_('EMAIL_UPDATE').':'.r.n;
			$emailbody .= '----------------------------------------------------------'.r.n;
			$emailbody .= JText::_('ORDER').' '.JText::_('NUM').': '. $id .r.n;
			$emailbody .= t.JText::_('ORDER').' '.JText::_('TOTAL').': '. $cost .r.n;
			$emailbody .= t.t.JText::_('PLACED').': '. JHTML::_('date', $row->ordered, '%d %b, %Y').r.n;
			$emailbody .= t.t.JText::_('STATUS').': ';
				
			switch($action)
			{
				case 'complete_order': 
		
					// adjust credit
					$credit = $BTL_Q->credit_summary();
					$adjusted = $credit - $hold;
					$BTL_Q->credit_adjustment($adjusted);	
					
					// remove hold 
					$sql = "DELETE FROM #__users_transactions WHERE category='store' AND type='hold' AND referenceid='".$id."' AND uid=".$row->uid;
					$database->setQuery( $sql);
					if (!$database->query()) {
							echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
							exit;
					}
					// debit account
					if ($cost > 0) {
						$BTL_Q->withdraw($cost, JText::_('BANKING_PURCHASE').' #'.$id, 'store', $id);
					}	
					
					// update order information
					$row->status_changed = date( "Y-m-d H:i:s" );
					$row->status = 1;
					$statusmsg = JText::_('ORDER').' #'.$id.' '.JText::_('HAS_BEEN').' '.strtolower(JText::_('COMPLETED')).'.';
										
					break;
					
				case 'cancel_order': 
				
					// adjust credit
					$credit = $BTL_Q->credit_summary();
					$adjusted = $credit - $hold;
					$BTL_Q->credit_adjustment($adjusted);	
					
					// remove hold
					$sql = "DELETE FROM #__users_transactions WHERE category='store' AND type='hold' AND referenceid='".$id."' AND uid=".$row->uid;
					$database->setQuery( $sql);
					if (!$database->query()) {
							echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
							exit;
					}	
					// update order information
					$row->status_changed = date( "Y-m-d H:i:s" );
					$row->status = 2;
					$statusmsg = JText::_('ORDER').' #'.$id.' '.JText::_('HAS_BEEN').' '.strtolower(JText::_('CANCELLED')).'.';
					break;
					
				case 'message': 
					$statusmsg = JText::_('MSG_SENT').'.';
					break;	
				default: 
					$statusmsg = JText::_('ORDER_DETAILS_UPDATED').'.';
					break;
			}
		
	
			// check content
			if (!$row->check()) {
				echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
			
			// store new content
			if (!$row->store()) {
				echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
			
			switch($row->status)
			{
				case 0: ;
					$emailbody .= ' '.JText::_('IN_PROCESS').r.n;
					break;
				case 1: 
					$emailbody .= ' '.strtolower(JText::_('COMPLETED')).' '.JText::_('ON').' '.JHTML::_('date', $row->status_changed, '%d %b, %Y').r.n.r.n;
					$emailbody .= JText::_('EMAIL_PROCESSED').'.'.r.n;
					break;
				case 2:
				default: 
					$emailbody .= ' '.strtolower(JText::_('CANCELLED')).' '.JText::_('ON').' '.JHTML::_('date', $row->status_changed, '%d %b, %Y').r.n.r.n;
					$emailbody .= JText::_('EMAIL_CANCELLED').'.'.r.n;
					break;
			}
			
			if($data['message']) { // add custom message			
				$emailbody .= $data['message'].r.n;				
			}
			//$emailbody .= '----------------------------------------------------------'.r.n;
			//$emailbody .= r.n.r.n.r.n;
			//$emailbody .= 'To view your current balance and transaction history, log on to:'.r.n;
			//$emailbody .= $this->_config['live_site'].'/my_account'.r.n;
				
			// send email
			if($action || $data['message']) { 
				if($email) {
					$admin_email = $xhub->getCfg('hubSupportEmail');
					$subject     = $hubShortName.' '.JText::_('STORE').': '.JText::_('EMAIL_UPDATE_SHORT').' #'.$id;
					$from        = $hubShortName.' '.JText::_('STORE');
					$hub         = array('email' => $admin_email, 'name' => $from);
														
					$this->send_email($hub, $row->email, $subject, $emailbody);
				}
			}
			
		}
	
		$this->_redirect ='index2.php?option='.$this->_option;
		$this->_message = $statusmsg;	
		$this->redirect();	
	
	}

	//-----------

	protected function saveitem( ) 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$id 	= JRequest::getInt( 'id', 0 );
		
		$_POST = array_map('trim',$_POST);

		// initiate extended database class
		$row = new Store( $database );
		if (!$row->bind( $_POST )) {
			echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		// code cleaner
		$row->description 	= TextFilter::cleanXss($row->description);
		//$row->description 	= nl2br($row->description);
		if(!$id) { $row->created 		= $row->created ? $row->created : date( "Y-m-d H:i:s" );}
		$sizes 				= ($_POST['sizes']) ? $_POST['sizes'] : '';
		$sizes 				= str_replace(" ","",$sizes);				
		$sizes    			= split(',',$sizes);
		$sizes_cl			= '';
		foreach($sizes as $s) {
			if(trim($s)!='') {
			$sizes_cl .= $s;
			$sizes_cl .= ($s==end($sizes)) ? '' : ', ';
			}
		}
		$row->title			= htmlspecialchars(stripslashes($row->title));
		$row->params		= $sizes_cl ? 'size='.$sizes_cl : '';
		$row->published		= isset($_POST['published']) ? 1 : 0;
		$row->available		= isset($_POST['available']) ? 1 : 0;
		$row->featured		= isset($_POST['featured']) ? 1 : 0;
		$row->type			= $_POST['category']== 'service' ? 2 : 1;
		
		
		// check content
		if (!$row->check()) {
			echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		// store new content
		if (!$row->store()) {
			echo "<script type=\"text/javascript\"> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		$this->_redirect ='index2.php?option='.$this->_option.'&task=storeitems';
		$this->_message =  JText::_('MSG_SAVED');	
		$this->redirect();
	}

	//-----------

	protected function state( ) 
	{
		$database =& JFactory::getDBO();
		$id 	= JRequest::getInt( 'id', 0, 'get' );
	
		$task = $this->_task;

		if($task == 'publish' || $task == 'unpublish') {
			$publish = ($task == 'publish') ? 1 : 0;
	
			// check for a resource
			if (!$id) {
				$action = ($publish == 1) ? 'published' : 'unpublished';
				echo "<script type=\"text/javascript\"> alert('".JText::_('ALERT_SELECT_ITEM')." ".$action."'); window.history.go(-1);</script>\n";
				exit;
			}
	
		
			// update record(s)
			$database->setQuery( "UPDATE #__store SET published='".$publish."' WHERE id='".$id."'");
			if (!$database->query()) {
				echo "<script type=\"text/javascript\"> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
	
			// set message
			if ( $publish == '1' ) {
				$msg = JText::_('MSG_ITEM_ADDED');
			} else if ( $publish == '0' ) {
				$msg = JText::_('MSG_ITEM_DELETED');
			}
		}
		else if($task == 'avail' || $task == 'unavail') {
			$avail = ($task == 'avail') ? 1 : 0;
	
			// check for a resource
			if (!$id) {
				$action = ($avail == 1) ? 'available' : 'unavailable';
				echo "<script type=\"text/javascript\"> alert('".JText::_('ALERT_SELECT_ITEM')." ".$action."'); window.history.go(-1);</script>\n";
				exit;
			}
	
			// update record(s)
			$database->setQuery( "UPDATE #__store SET available='".$avail."' WHERE id='".$id."'");
			if (!$database->query()) {
				echo "<script type=\"text/javascript\"> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
	
			// set message
			if ( $avail == '1' ) {
				$msg = JText::_('MSG_ITEM_AVAIL');
			} else if ( $avail == '0' ) {
				$msg = JText::_('MSG_ITEM_UNAVAIL');
			}
		}

		$this->_redirect ='index2.php?option='.$this->_option.'&task=storeitems';
		$this->_message = $msg;	
		$this->redirect();
	
	}
	
	//-----------
	
	protected function cancel( )
	{
		$database =& JFactory::getDBO();
		$id 	= JRequest::getInt( 'id', array(0) );
	
		$url  = 'index2.php?option='.$this->_option;
		if($this->_task=='cancel_i') {
		$url .= '&task=storeitems';
		}
		$this->_redirect = $url;
		$this->redirect();	
	}

	//-----------

	function send_email($hub, $email, $subject, $message) 
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
	
}
?>
