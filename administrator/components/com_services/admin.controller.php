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

class ServicesController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $error  = NULL;

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

	public function execute()
	{
		
		$database =& JFactory::getDBO();
		
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		
		switch ( $this->getTask() ) 
		{
			case 'subscriptions':   	$this->subscriptions();     break;
			case 'subscription':   		$this->subscription();     	break;
			case 'savesubscription':   	$this->savesubscription();  break;
			case 'services':    		$this->services();     		break;
			case 'service':    			$this->service();     		break;
			case 'newservice':    		$this->service();     		break;
			case 'saveservice':    		$this->saveservice();     	break;
			default: $this->subscriptions(); break;
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

	public function intro()
	{
		// Output HTML
		$html ='';
		$html .= '<p>There is currently no back-end functionality</p>';
		
		echo $html;
	}
	
	//---------------------
	// Subscriptions List
	//---------------------
	
	public function subscriptions()
	{
		// Push some styles to the template
		$this->getStyles();

		// Get filters
		$filters = $this->getFilters();

		$database =& JFactory::getDBO();

		$obj = new Subscription($database);
		
		// Record count
		$total = $obj->getSubscriptionsCount( $filters, true );
		
		// Fetch results
		$rows = $obj->getSubscriptions( $filters, true );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		ServicesHtml::subscriptions( $database, $rows, $total, $pageNav, $this->_option, $filters );
	}

	//---------------------
	// Subscription
	//---------------------
	
	public function subscription()
	{
		$database =& JFactory::getDBO();
		$id 	= JRequest::getInt( 'id', 0 );
		
		$row = new Subscription($database);
		$subscription = $row->getSubscription( $id );
					
		if(!$subscription) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('Subscription not found');
			return;
		}
		
		$juser =& JUser::getInstance($subscription->uid);
		
		// check available user funds		
		$BTL 		= new BankTeller( $database, $subscription->uid);
		$balance 	= $BTL->summary();			
		$credit  	= $BTL->credit_summary();
		$funds   	= $balance;			
		$funds   	= ($funds > 0) ? $funds : '0';
	
		// output HTML
		ServicesHtml::subscription( $database, $subscription, $this->_option, $funds, $juser );
	}
	
	//---------------------
	// Save Subscription
	//---------------------
	
	public function savesubscription()
	{
		$database =& JFactory::getDBO();
		$id 	= JRequest::getInt( 'id', 0 );
		
		$subscription = new Subscription($database);
		
		if(!$subscription->load($id)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('Subscription not found');
			return;
		}
		
		// get service
		$service = new Service($database);
		
		if(!$service->loadService ('', $subscription->serviceid)) {
			JError::raiseError( 404, JText::_('Service not found. ') );
			return;
		}
		
		$author 	=& JUser::getInstance($subscription->uid);
		$subscription->notes = rtrim(stripslashes(JRequest::getVar( 'notes', '' )));
		$action	 	= JRequest::getVar( 'action', '' );
		$message	= JRequest::getVar( 'message', '' );
		$statusmsg  = '';
		$email		= 0;
		
		
		switch($action)
		{
				case 'refund': 
					$received_refund 				= JRequest::getInt( 'received_refund', 0 );
					$newunits 		    			= JRequest::getInt( 'newunits', 0 );	
					$pending 						= $subscription->pendingpayment - $received_refund	;
					$pendingunits 					= $subscription->pendingunits - $newunits;
					$subscription->pendingpayment 	= $pending <= 0 ? 0 : $pending;
					$subscription->pendingunits  	= $pendingunits <= 0 ? 0 : $pendingunits;	
					$email = 0;
					$statusmsg .= JText::_('Refund has been processed.');					
				break;
				
				case 'activate': 
					$received_payment 				= JRequest::getInt( 'received_payment', 0 );
					$newunits 		    			= JRequest::getInt( 'newunits', 0 );
					$pending 						= $subscription->pendingpayment - $received_payment;	
					$pendingunits 					= $subscription->pendingunits - $newunits;
					$subscription->pendingpayment 	= $pending <= 0 ? 0 : $pending;
					$subscription->pendingunits  	= $pendingunits <= 0 ? 0 : $pendingunits;					
					$subscription->totalpaid 		= $subscription->totalpaid + $received_payment;	
					$oldunits						= $subscription->units;				
					
					$months 						= $newunits * $service->unitsize;
					$newexpire 						= ($oldunits > 0  && intval( $subscription->expires ) <> 0) ? date("Y-m-d",strtotime($subscription->expires. "+".$months."months")) : date("Y-m-d",strtotime("+".$months."months"));
					$subscription->expires 			= $newunits ? $newexpire : $subscription->expires;
					$subscription->status 			=  1;
					$subscription->units 			= $subscription->units + $newunits;
					
					$email 				= ($received_payment > 0 or $newunits > 0)  ? 1 : 0;				
					$statusmsg 		   .= JText::_('Subscription has been activated');
					if($newunits > 0) {
						$statusmsg 		   .=  ' '.JText::_('for').' '.$newunits.' ';
						$statusmsg		   .= $oldunits > 0 ? JText::_('additional').' ' : '';
						$statusmsg		   .= JText::_('month(s)');
					}	
				break;
				
				case 'message': 
					$statusmsg .= JText::_('Your message has been sent.');	
				break;
				
				case 'cancelsub':				
					$refund 	  = 0;
					$unitsleft 	  = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);
			
					// get cost per unit (to compute required refund)	
					$refund = ($subscription->totalpaid > 0 && $unitsleft > 0 && ($subscription->totalpaid - $unitsleft * $unitcost) > 0 ) ? $unitsleft * $prevunitcost : 0; 
					$subscription->status = 2;
					$subscription->pendingpayment = $refund;
					$subscription->pendingunits = $refund > 0  ? $unitsleft : 0;
					$email = 1;
					$statusmsg .= JText::_('Subscription has been cancelled by site administrator.');	
				
				break;
		}
		
		if(($action && $action != 'message') or $message) {
			$subscription->notes .= '------------------------------'.r.n;
			$subscription->notes .= JText::_('Subscription status update').', '.date( 'Y-m-d H:i:s', time() ).r.n;	
			$subscription->notes .= $statusmsg ? $statusmsg.r.n : '';
			$subscription->notes .= $message ? $message.r.n : '';	
			$subscription->notes .= '------------------------------'.r.n;
		}
		
		
		if (!$subscription->check()) {
			echo ServicesHtml::alert( $subscription->getError() );
			exit();
		}
		if (!$subscription->store()) {
			echo ServicesHtml::alert( $subscription->getError() );
			exit();
		}
		
		
		if($email or $message) {
			
			$xhub =& XFactory::getHub();

			// E-mail "from" info
			$from = array();
			$from['email'] = $xhub->getCfg('hubSupportEmail');
			$from['name']  = $xhub->getCfg('hubShortURL').' '.JText::_('Subscriptions');
			
			// start email message
			$subject = JText::_('Status update on your subscription #').$subscription->code;
			$emailbody  = $subject.':'.r.n;
			$emailbody .= JText::_('Subscription Service').' - '.$service->title.r.n;
			$emailbody .= '----------------------------------------------------------'.r.n;	
	
			$emailbody .= $action != 'message' && $statusmsg ? $statusmsg : '';
			if($message) {
				$emailbody .= "\r\n";
				$emailbody .= $message;
			}				
			
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'subscriptions_message', $subject, $emailbody, $from, array($subscription->uid), $this->_option ))) {
				$this->setError( JText::_('Failed to message users.') );
			}
		}
		
		$this->_redirect = 'index.php?option='.$this->_option;
		$msg  = JText::_('Subscription successfully saved.');
		$msg .= $statusmsg ? ' '.$statusmsg : ''; 
		$this->_message = JText::_($msg);
		
	}
	
	
	
	//---------------------
	// Services List
	//---------------------
	
	public function services()
	{
		$database =& JFactory::getDBO();
		ximport( 'subscriptions' );
		
		$app =& JFactory::getApplication();
		
		// Get configuration
		$config = JFactory::getConfig();
		
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.serv.limit', 'limit', 25, 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.serv.limitstart', 'limitstart', 0, 'int');
		$filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.serv.sort', 'filter_order', 'category'));
		$filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.serv.sortdir', 'filter_order_Dir', 'ASC'));	
		
		// get all available services
		$objS = new Service($database);		
		$services = $objS->getServices('', 1, '', $filters['sort'], $filters['sort_Dir'], '', 1);
		
		$total = $services ? count($services) : 0;		
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total , $filters['start'], $filters['limit'] );
		
		// output HTML
		ServicesHtml::services( $services, $this->_option, $pageNav, $filters);
	}
	
	//----------------------------------------------------------
	// Initial setup of default jobs services
	//----------------------------------------------------------
	
	protected function setupServices() 
	{
		ximport( 'subscriptions' );
		$database =& JFactory::getDBO();
		
		$objS = new Service($database);
		$now = date( 'Y-m-d H:i:s', time() );
		
		$default1 = array (
					'id' => 0, 
					'title' => JText::_('Employer Service, Basic'), 
					'category' => strtolower(JText::_('jobs')), 
					'alias' => JText::_('employer_basic'), 
					'status' => 1, 
					'description' => JText::_('Allows to search member resumes and post one job ad'),
					'unitprice' => '0.00', 
					'pointprice' => 0, 
					'currency' => '$',
					'maxunits' => 6,
					'minunits' => 1,   
					'unitsize' => 1, 
					'unitmeasure' => strtolower(JText::_('month')),  
					'changed' => $now,     
					'params' => 'promo=First 3 months FREE
promomaxunits=3
maxads=1'
					);
		$default2 = array (
					'id' => 0, 
					'title' => JText::_('Employer Service, Premium'), 
					'category' => strtolower(JText::_('jobs')), 
					'alias' => JText::_('employer_premium'), 
					'status' => 0, 
					'description' => JText::_('Allows to search member resumes and post up to 3 job ads'),
					'unitprice' => '500.00', 
					'pointprice' => 0, 
					'currency' => '$',
					'maxunits' => 6,
					'minunits' => 1,   
					'unitsize' => 1, 
					'unitmeasure' => strtolower(JText::_('month')),  
					'changed' => $now,     
					'params' => 'promo=
promomaxunits=
maxads=3'
					);
		
		if (!$objS->bind($default1)) {
			$this->_error = $objS->getError();
			return false;
		}
		if (!$objS->store()) {
			$this->_error = $objS->getError();
			return false;
		}
		if (!$objS->bind($default2)) {
			$this->_error = $objS->getError();
			return false;
		}
		if (!$objS->store()) {
			$this->_error = $objS->getError();
			return false;
		}
		
	}
	
	
	//---------------------
	// Service
	//---------------------
	
	public function service()
	{
		// Output HTML
		$html ='';		
		echo $html;
	}
	
	//---------------------
	// Save service
	//---------------------
	
	public function saveservice()
	{
		// Output HTML
		$html ='';		
		echo $html;
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
	
	public function getFilters()
	{
		
		// Query filters defaults
		$filters = array();
		$filters['sortby'] = trim(JRequest::getVar( 'sortby', 'pending' ));
		$filters['filterby'] = trim(JRequest::getVar( 'filterby', 'all' ));	
		
		// Paging vars
		$filters['limit'] = JRequest::getInt( 'limit', 25 );
		$filters['start'] = JRequest::getInt( 'limitstart', 0, 'get' );
		
		// Return the array
		return $filters;
	}
}
?>