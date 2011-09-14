<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class ServicesController extends Hubzero_Controller
{
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '' );

		switch ($this->_task)
		{
			case 'subscriptions':    $this->subscriptions();    break;
			case 'subscription':     $this->subscription();     break;
			case 'savesubscription': $this->savesubscription(); break;

			case 'services':         $this->services();         break;
			case 'service':          $this->service();          break;
			case 'newservice':       $this->service();          break;
			case 'saveservice':      $this->saveservice();      break;

			default: $this->subscriptions(); break;
		}
	}

	//---------------------
	// Subscriptions List
	//---------------------

	public function subscriptions()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'subscriptions') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'admin.'.$this->_name.'.css');

		// Get filters
		$view->filters = $this->getFilters();

		$obj = new Subscription($this->database);

		// Record count
		$view->total = $obj->getSubscriptionsCount( $view->filters, true );

		// Fetch results
		$view->rows = $obj->getSubscriptions( $view->filters, true );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//---------------------
	// Subscription
	//---------------------

	public function subscription()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'subscription') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$id = JRequest::getInt( 'id', 0 );

		$row = new Subscription($this->database);
		$view->subscription = $row->getSubscription( $id );

		if (!$view->subscription) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('Subscription not found');
			return;
		}

		$view->customer =& JUser::getInstance($view->subscription->uid);

		// check available user funds		
		$BTL = new Hubzero_Bank_Teller($this->database, $view->subscription->uid);
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance;
		$view->funds = ($funds > 0) ? $funds : '0';

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//---------------------
	// Save Subscription
	//---------------------

	public function savesubscription()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$id = JRequest::getInt( 'id', 0 );

		$subscription = new Subscription($this->database);

		if (!$subscription->load($id)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('Subscription not found');
			return;
		}

		// get service
		$service = new Service($this->database);

		if (!$service->loadService ('', $subscription->serviceid)) {
			JError::raiseError( 404, JText::_('Service not found. ') );
			return;
		}

		$author 	=& JUser::getInstance($subscription->uid);
		$subscription->notes = rtrim(stripslashes(JRequest::getVar( 'notes', '' )));
		$action	 	= JRequest::getVar( 'action', '' );
		$message	= JRequest::getVar( 'message', '' );
		$statusmsg  = '';
		$email		= 0;

		switch ($action)
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

				$email = ($received_payment > 0 or $newunits > 0)  ? 1 : 0;
				$statusmsg .= JText::_('Subscription has been activated');
				if ($newunits > 0) {
					$statusmsg .=  ' '.JText::_('for').' '.$newunits.' ';
					$statusmsg .= $oldunits > 0 ? JText::_('additional').' ' : '';
					$statusmsg .= JText::_('month(s)');
				}
			break;

			case 'message':
				$statusmsg .= JText::_('Your message has been sent.');
			break;

			case 'cancelsub':
				$refund    = 0;
				$unitsleft = $subscription->getRemaining('unit', $subscription, $service->maxunits, $service->unitsize);

				// get cost per unit (to compute required refund)	
				$refund = ($subscription->totalpaid > 0 && $unitsleft > 0 && ($subscription->totalpaid - $unitsleft * $unitcost) > 0 ) ? $unitsleft * $prevunitcost : 0;
				$subscription->status = 2;
				$subscription->pendingpayment = $refund;
				$subscription->pendingunits = $refund > 0  ? $unitsleft : 0;
				$email = 1;
				$statusmsg .= JText::_('Subscription has been cancelled by site administrator.');
			break;
		}

		if (($action && $action != 'message') or $message) {
			$subscription->notes .= '------------------------------'."\r\n";
			$subscription->notes .= JText::_('Subscription status update').', '.date( 'Y-m-d H:i:s', time() )."\r\n";
			$subscription->notes .= $statusmsg ? $statusmsg."\r\n" : '';
			$subscription->notes .= $message ? $message."\r\n" : '';
			$subscription->notes .= '------------------------------'."\r\n";
		}

		if (!$subscription->check()) {
			JError::raiseError( 500, $subscription->getError() );
			return;
		}
		if (!$subscription->store()) {
			JError::raiseError( 500, $subscription->getError() );
			return;
		}

		if ($email or $message) {
			$jconfig =& JFactory::getConfig();

			// E-mail "from" info
			$from = array();
			$from['email'] = $jconfig->getValue('config.mailfrom');
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('Subscriptions');

			// start email message
			$subject = JText::_('Status update on your subscription #').$subscription->code;
			$emailbody  = $subject.':'."\r\n";
			$emailbody .= JText::_('Subscription Service').' - '.$service->title."\r\n";
			$emailbody .= '----------------------------------------------------------'."\r\n";

			$emailbody .= $action != 'message' && $statusmsg ? $statusmsg : '';
			if ($message) {
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
		$this->_message  = JText::_('Subscription successfully saved.');
		$this->_message .= $statusmsg ? ' '.$statusmsg : '';
	}

	//---------------------
	// Services List
	//---------------------

	public function services()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'services') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.serv.limit', 'limit', 25, 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.serv.limitstart', 'limitstart', 0, 'int');
		$view->filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.serv.sort', 'filter_order', 'category'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.serv.sortdir', 'filter_order_Dir', 'ASC'));

		// get all available services
		$objS = new Service($this->database);
		$view->rows = $objS->getServices('', 1, '', $view->filters['sort'], $view->filters['sort_Dir'], '', 1);

		$view->total = ($view->rows) ? count($view->rows) : 0;

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total , $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	// Initial setup of default jobs services
	//----------------------------------------------------------

	protected function setupServices()
	{
		$database =& JFactory::getDBO();

		$objS = new Service($database);
		$now = date( 'Y-m-d H:i:s', time() );

		$default1 = array(
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
		$default2 = array(
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
			$this->setError( $objS->getError() );
			return false;
		}
		if (!$objS->store()) {
			$this->setError( $objS->getError() );
			return false;
		}
		if (!$objS->bind($default2)) {
			$this->setError( $objS->getError() );
			return false;
		}
		if (!$objS->store()) {
			$this->setError( $objS->getError() );
			return false;
		}
	}

	//---------------------
	// Service
	//---------------------

	public function service()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'service') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//---------------------
	// Save service
	//---------------------

	public function saveservice()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->_redirect = 'index.php?option='.$this->_option.'&task=services';
	}

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

