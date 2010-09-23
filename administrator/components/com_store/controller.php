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

ximport('Hubzero_Controller');

class StoreController extends Hubzero_Controller
{
	public function execute()
	{
		// Get the component parameters
		$sconfig =& JComponentHelper::getParams( $this->_option );
		$this->config = $sconfig;
		
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking = $upconfig->get('bankAccounts');
		$this->banking = $banking;
		
		if ($banking) {
			ximport('Hubzero_Bank');
		}
		
		$this->_task = JRequest::getVar( 'task', '' );
		
		switch ($this->_task) 
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
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function orders() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'orders') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->store_enabled = $this->config->get('store_enabled');
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.orders.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.orders.limitstart', 'limitstart', 0, 'int');
		$view->filters['filterby'] = trim($app->getUserStateFromRequest($this->_option.'.orders.filterby', 'filterby', 'all'));
		$view->filters['sortby']   = trim($app->getUserStateFromRequest($this->_option.'.orders.sortby', 'sortby', 'm.id DESC'));	
		
		// Get cart object
		$objOrder = new Order( $this->database );
		
		// Get record count
		$view->total = $objOrder->getOrders('count', $view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		$view->rows = $objOrder->getOrders('',$view->filters);
		
		if ($view->rows) {
			$oi = new OrderItem( $this->database );
			foreach ($view->rows as $o) 
			{
				$items = '';

				$results = $oi->getOrderItems($o->id);
				
				foreach ($results as $r) 
				{
					$items .= $r->title;
					$items .= ($r != end($results)) ? '; ' : '';
				}
				$o->itemtitles = $items;
				
				$targetuser =& JUser::getInstance($o->uid);
				$o->author = $targetuser->get('username');
			}
		}

		// Push some styles to the view
		$this->_getStyles();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function storeitems()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'items') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->store_enabled = $this->config->get('store_enabled');
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.items.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.items.limitstart', 'limitstart', 0, 'int');
		$view->filters['filterby'] = trim($app->getUserStateFromRequest($this->_option.'.items.filterby', 'filterby', 'all'));
		$view->filters['sortby']   = trim($app->getUserStateFromRequest($this->_option.'.items.sortby', 'sortby', 'date'));

		$obj = new Store($this->database);
		
		$view->total = $obj->getItems('count', $view->filters, $this->config);

		$view->rows = $obj->getItems('retrieve', $view->filters, $this->config);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );		
	
		// how many times ordered?
		if ($view->rows) {
			$oi = new OrderItem( $this->database );
			foreach ($view->rows as $o) 
			{
				// Active orders
				$o->activeorders = $oi->countActiveItemOrders($o->id);

				// All orders
				$o->allorders = $oi->countAllItemOrders($o->id);
			}
		}
		
		// Push some styles to the view
		$this->_getStyles();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------

	protected function receipt() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Load the order
		$row = new Order( $this->database );
		$row->load( $id );
		
		// Instantiate an OrderItem object
		$oi = new OrderItem( $this->database );
		
		if ($id) {
			// Get order items
			$orderitems = $oi->getOrderItems($id);
			if ($orderitems) {
				foreach ($orderitems as $r) 
				{
					$params =& new JParameter( $r->params );
					$selections =& new JParameter( $r->selections );
					
					// Get size selection
					$r->sizes    		= $params->get( 'size', '' );
					$r->sizes 			= str_replace(" ","",$r->sizes);				
					$r->selectedsize    = trim($selections->get( 'size', '' ));
					$r->sizes    		= split(',',$r->sizes);
					$r->sizeavail		= in_array($r->selectedsize, $r->sizes) ? 1 : 0;
					
					// Get color selection
					$r->colors    		= $params->get( 'color', '' );
					$r->colors 			= str_replace(" ","",$r->colors);				
					$r->selectedcolor   = trim($selections->get( 'color', '' ));
					$r->colors    		= split(',',$r->colors);
				}
			}

			$customer =& JUser::getInstance($row->uid);
		}
		
		// Include needed libraries
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'helpers'.DS.'receipt.pdf.php' );

		// Get the Joomla config
		$jconfig =& JFactory::getConfig();
		
		// Build the link displayed
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$webpath = str_replace('/administrator/','/',$juri->base().$sef);
		$webpath = str_replace('//','/',$webpath);
		if (isset( $_SERVER['HTTPS'] )) {
			$webpath = str_replace('http:','https:',$webpath);
		}
		if (!strstr( $webpath, '://' )) {
			$webpath = str_replace(':/','://',$webpath);
		}
		
		// Start building the PDF
		$pdf = new PDF();
		$hubaddress = array();
		$hubaddress[] = $this->config->get('hubaddress_ln1') ? $this->config->get('hubaddress_ln1') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln2') ? $this->config->get('hubaddress_ln2') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln3') ? $this->config->get('hubaddress_ln3') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln4') ? $this->config->get('hubaddress_ln4') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln5') ? $this->config->get('hubaddress_ln5') : '' ;
		$hubaddress[] = $this->config->get('hubemail') ? $this->config->get('hubemail') : '' ;
		$hubaddress[] = $this->config->get('hubphone') ? $this->config->get('hubphone') : '' ;
		$pdf->hubaddress = $hubaddress;
		$pdf->url = $webpath;
		$pdf->headertext_ln1 = $this->config->get('headertext_ln1') ? $this->config->get('headertext_ln1') : '' ;
		$pdf->headertext_ln2 = $this->config->get('headertext_ln2') ? $this->config->get('headertext_ln2') : $jconfig->getValue('config.sitename') ;
		$pdf->footertext = $this->config->get('footertext') ? $this->config->get('footertext') : 'Thank you for contributions to our HUB!' ;
		$pdf->receipt_title = $this->config->get('receipt_title') ? $this->config->get('receipt_title') : 'Your Order' ;
		$pdf->receipt_note = $this->config->get('receipt_note') ? $this->config->get('receipt_note') : '' ;
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		// Title
		$pdf->mainTitle();
		
		// Order details
		if ($id) {
			$pdf->orderDetails($customer, $row, $orderitems);
		} else {
			$pdf->Warning('No information available. Please supply order ID');
		}
		
		// Thank-you line
		/*for($i=1;$i<=40;$i++) 
		{
			$pdf->Cell(0,10,'Printing line number '.$i,0,1);
		}*/
		$pdf->Output();
		exit();
	}
	
	//-----------

	protected function order() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'order') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->store_enabled = $this->config->get('store_enabled');
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Load data
		$view->row = new Order( $this->database );
		$view->row->load( $id );
		
		$oi = new OrderItem( $this->database );
	
		$view->orderitems = array();
		$view->customer = null;
		$view->funds = 0;
		if ($id) {
			// Get order items
			$view->orderitems = $oi->getOrderItems($id);
			if (count($view->orderitems) > 0) {
				foreach ($view->orderitems as $r) 
				{
					$params =& new JParameter( $r->params );
					$selections =& new JParameter( $r->selections );
					
					// Get size selection
					$r->sizes = $params->get( 'size', '' );
					$r->sizes = str_replace(" ","",$r->sizes);				
					$r->sizes = split(',',$r->sizes);
					$r->selectedsize = trim($selections->get( 'size', '' ));
					$r->sizeavail = in_array($r->selectedsize, $r->sizes) ? 1 : 0;
					
					// Get color selection
					$r->colors = $params->get( 'color', '' );
					$r->colors = str_replace(" ","",$r->colors);
					$r->colors = split(',',$r->colors);
					$r->selectedcolor = trim($selections->get( 'color', '' ));
				}
			}

			$view->customer =& JUser::getInstance($view->row->uid);
			
			// Check available user funds		
			$BTL = new Hubzero_Bank_Teller($this->database, $view->row->uid);
			$balance = $BTL->summary();			
			$credit  = $BTL->credit_summary();
			$view->funds = $balance;
		}	
	
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------

	protected function storeitem() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'item') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->store_enabled = $this->config->get('store_enabled');
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
	
		// Load info from database
		$view->row = new Store( $this->database );
		$view->row->load( $id );
	
		if ($id) {		
			// Get parameters
			$params =& new JParameter( $view->row->params );
			$view->row->size = $params->get( 'size', '' );							
			$view->row->color = $params->get( 'color', '' );
		} else {
			// New item			
			$view->row->available = 0;
			$view->row->created   = date( 'Y-m-d H:i:s', time() );
			$view->row->published = 0;
			$view->row->featured  = 0;
			$view->row->special   = 0;
			$view->row->type      = 1;
			$view->row->category  = 'wear';
		}
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	//  Processers
	//----------------------------------------------------------
	
	protected function saveorder() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$jconfig =& JFactory::getConfig();
		
		$statusmsg = '';
		$email = 1; // turn emailing on/off
		$emailbody ='';
		ximport('Hubzero_Bank');

		$data = array_map('trim',$_POST);
		$action = (isset($data['action'])) ? $data['action'] : '';
		$id = ($data['id']) ? $data['id'] : 0 ;
		$cost = intval($data['total']);

		if ($id) {
			// initiate extended database class
			$row = new Order( $this->database );
			$row->load( $id );
			$row->notes = Hubzero_Filter::cleanXss($data['notes']);
			$hold = $row->total;
			$row->total = $cost;
			
			// get user bank account
			//$xprofile =& Hubzero_User_Profile::getInstance( $row->uid );
			$xprofile =& JUser::getInstance($row->uid);
			$BTL_Q = new Hubzero_Bank_Teller( $this->database, $xprofile->get('id') );
			
			// start email message
			$emailbody .= JText::_('THANKYOU').' '.JText::_('IN_THE').' '.$jconfig->getValue('config.sitename').' '.JText::_('STORE').'!'."\r\n\r\n";
			$emailbody .= JText::_('EMAIL_UPDATE').':'."\r\n";
			$emailbody .= '----------------------------------------------------------'."\r\n";
			$emailbody .= JText::_('ORDER').' '.JText::_('NUM').': '. $id ."\r\n";
			$emailbody .= "\t".JText::_('ORDER').' '.JText::_('TOTAL').': '. $cost ."\r\n";
			$emailbody .= "\t\t".JText::_('PLACED').': '. JHTML::_('date', $row->ordered, '%d %b, %Y')."\r\n";
			$emailbody .= "\t\t".JText::_('STATUS').': ';
				
			switch ($action)
			{
				case 'complete_order':
					// adjust credit
					$credit = $BTL_Q->credit_summary();
					$adjusted = $credit - $hold;
					$BTL_Q->credit_adjustment($adjusted);	
					
					// remove hold 
					$sql = "DELETE FROM #__users_transactions WHERE category='store' AND type='hold' AND referenceid='".$id."' AND uid=".$row->uid;
					$this->database->setQuery($sql);
					if (!$this->database->query()) {
						JError::raiseError( 500, $this->database->getErrorMsg() );
						return;
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
					$this->database->setQuery($sql);
					if (!$this->database->query()) {
						JError::raiseError( 500, $this->database->getErrorMsg() );
						return;
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
				JError::raiseError( 500, $row->getError() );
				return;
			}
			
			// store new content
			if (!$row->store()) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
			
			switch ($row->status)
			{
				case 0: ;
					$emailbody .= ' '.JText::_('IN_PROCESS')."\r\n";
					break;
				case 1: 
					$emailbody .= ' '.strtolower(JText::_('COMPLETED')).' '.JText::_('ON').' '.JHTML::_('date', $row->status_changed, '%d %b, %Y')."\r\n\r\n";
					$emailbody .= JText::_('EMAIL_PROCESSED').'.'."\r\n";
					break;
				case 2:
				default: 
					$emailbody .= ' '.strtolower(JText::_('CANCELLED')).' '.JText::_('ON').' '.JHTML::_('date', $row->status_changed, '%d %b, %Y')."\r\n\r\n";
					$emailbody .= JText::_('EMAIL_CANCELLED').'.'."\r\n";
					break;
			}
			
			if ($data['message']) { // add custom message			
				$emailbody .= $data['message']."\r\n";				
			}

			// send email
			if ($action || $data['message']) { 
				if ($email) {
					ximport('Hubzero_Toolbox');
					$admin_email = $jconfig->getValue('config.mailfrom');
					$subject     = $jconfig->getValue('config.sitename').' '.JText::_('STORE').': '.JText::_('EMAIL_UPDATE_SHORT').' #'.$id;
					$from        = $jconfig->getValue('config.sitename').' '.JText::_('STORE');
					$hub         = array('email' => $admin_email, 'name' => $from);
														
					Hubzero_Toolbox::send_email($hub, $row->email, $subject, $emailbody);
				}
			}
		}
	
		$this->_redirect ='index.php?option='.$this->_option;
		$this->_message = $statusmsg;
	}

	//-----------

	protected function saveitem() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		$_POST = array_map('trim',$_POST);

		// initiate extended database class
		$row = new Store( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500,$row->getError() );
			return;
		}

		// code cleaner
		$row->description = Hubzero_Filter::cleanXss($row->description);
		if (!$id) { 
			$row->created = $row->created ? $row->created : date( "Y-m-d H:i:s" );
		}
		$sizes = ($_POST['sizes']) ? $_POST['sizes'] : '';
		$sizes = str_replace(" ","",$sizes);				
		$sizes = split(',',$sizes);
		$sizes_cl= '';
		foreach ($sizes as $s) 
		{
			if (trim($s) != '') {
				$sizes_cl .= $s;
				$sizes_cl .= ($s==end($sizes)) ? '' : ', ';
			}
		}
		$row->title = htmlspecialchars(stripslashes($row->title));
		$row->params = $sizes_cl ? 'size='.$sizes_cl : '';
		$row->published	= isset($_POST['published']) ? 1 : 0;
		$row->available	= isset($_POST['available']) ? 1 : 0;
		$row->featured = isset($_POST['featured']) ? 1 : 0;
		$row->type = $_POST['category'] == 'service' ? 2 : 1;
		
		// check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		$this->_redirect ='index.php?option='.$this->_option.'&task=storeitems';
		$this->_message = JText::_('MSG_SAVED');
	}

	//-----------

	protected function state() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		$id = JRequest::getInt( 'id', 0, 'get' );

		switch ($this->_task)
		{
			case 'publish':
			case 'unpublish':
				$publish = ($this->_task == 'publish') ? 1 : 0;

				// Check for an ID
				if (!$id) {
					$action = ($publish == 1) ? 'published' : 'unpublished';
					echo StoreHtml::alert( JText::_('ALERT_SELECT_ITEM')." ".$action );
					exit;
				}

				// Update record(s)
				$obj = new Store($this->database);
				$obj->load($id);
				$obj->published = $publish;

				if (!$obj->store()) {
					JError::raiseError( 500, $obj->getError() );
					return;
				}

				// Set message
				if ($publish == '1') {
					$this->_message = JText::_('MSG_ITEM_ADDED');
				} else if ( $publish == '0' ) {
					$this->_message = JText::_('MSG_ITEM_DELETED');
				}
			break;
		
			case 'avail':
			case 'unavail':
				$avail = ($this->_task == 'avail') ? 1 : 0;

				// Check for an ID
				if (!$id) {
					$action = ($avail == 1) ? 'available' : 'unavailable';
					echo StoreHtml::alert( JText::_('ALERT_SELECT_ITEM')." ".$action );
					exit;
				}

				// Update record(s)
				$obj = new Store($this->database);
				$obj->load($id);
				$obj->available = $avail;
				
				if (!$obj->store()) {
					JError::raiseError( 500, $obj->getError() );
					return;
				}

				// Set message
				if ($avail == '1') {
					$this->_message = JText::_('MSG_ITEM_AVAIL');
				} else if ($avail == '0') {
					$this->_message = JText::_('MSG_ITEM_UNAVAIL');
				}
			break;
		}

		$this->_redirect ='index.php?option='.$this->_option.'&task=storeitems';
	}
	
	//-----------
	
	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
		if ($this->_task == 'cancel_i') {
			$this->_redirect .= '&task=storeitems';
		}	
	}
}
