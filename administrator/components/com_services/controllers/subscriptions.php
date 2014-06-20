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
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class for service subscriptions
 */
class ServicesControllerSubscriptions extends \Hubzero\Component\AdminController
{
	/**
	 * Subscriptions List
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'admin.' . $this->_name . '.css');

		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		$this->view->filters = array();

		// Get paging variables
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		// Get sorting variables
		$this->view->filters['sortby']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortby',
			'sortby',
			'pending'
		));
		$this->view->filters['filterby'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filterby',
			'filterby',
			'all'
		));

		$obj = new Subscription($this->database);

		// Record count
		$this->view->total = $obj->getSubscriptionsCount($this->view->filters, true);

		// Fetch results
		$this->view->rows = $obj->getSubscriptions($this->view->filters, true);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new subscription
	 * Displays the edit form
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Subscription
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (is_object($row))
		{
			$this->view->subscription = $row;
		}
		else
		{
			$id = JRequest::getInt('id', 0);

			$row = new Subscription($this->database);
			$this->view->subscription = $row->getSubscription($id);
		}

		if (!$this->view->subscription)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SERVICES_SUBSCRIPTION_NOT_FOUND')
			);
			return;
		}

		$this->view->customer = JUser::getInstance($this->view->subscription->uid);

		// check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->view->subscription->uid);
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance;
		$this->view->funds = ($funds > 0) ? $funds : '0';

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save Subscription
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$id = JRequest::getInt('id', 0);

		$subscription = new Subscription($this->database);
		if (!$subscription->load($id))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SERVICES_SUBSCRIPTION_NOT_FOUND'),
				'error'
			);
			return;
		}

		// get service
		$service = new Service($this->database);
		if (!$service->loadService('', $subscription->serviceid))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SERVICES_SERVICE_NOT_FOUND') . ' ' .  $subscription->serviceid,
				'error'
			);
			return;
		}

		$author 	= JUser::getInstance($subscription->uid);
		$subscription->notes = rtrim(stripslashes(JRequest::getVar('notes', '')));
		$action	 	= JRequest::getVar('action', '');
		$message	= JRequest::getVar('message', '');
		$statusmsg  = '';
		$email		= 0;

		switch ($action)
		{
			case 'refund':
				$received_refund 				= JRequest::getInt('received_refund', 0);
				$newunits 		    			= JRequest::getInt('newunits', 0);
				$pending 						= $subscription->pendingpayment - $received_refund;
				$pendingunits 					= $subscription->pendingunits - $newunits;
				$subscription->pendingpayment 	= $pending <= 0 ? 0 : $pending;
				$subscription->pendingunits  	= $pendingunits <= 0 ? 0 : $pendingunits;
				$email = 0;
				$statusmsg .= JText::_('Refund has been processed.');
			break;

			case 'activate':
				$received_payment 				= JRequest::getInt('received_payment', 0);
				$newunits 		    			= JRequest::getInt('newunits', 0);
				$pending 						= $subscription->pendingpayment - $received_payment;
				$pendingunits 					= $subscription->pendingunits - $newunits;
				$subscription->pendingpayment 	= $pending <= 0 ? 0 : $pending;
				$subscription->pendingunits  	= $pendingunits <= 0 ? 0 : $pendingunits;
				$subscription->totalpaid 		= $subscription->totalpaid + $received_payment;
				$oldunits						= $subscription->units;

				$months 						= $newunits * $service->unitsize;
				$newexpire 						= ($oldunits > 0  && intval($subscription->expires) <> 0) ? JFactory::getDate(strtotime($subscription->expires . "+" . $months . "months"))->format("Y-m-d") : JFactory::getDate(strtotime("+" . $months . "months"))->format("Y-m-d");
				$subscription->expires 			= $newunits ? $newexpire : $subscription->expires;
				$subscription->status 			=  1;
				$subscription->units 			= $subscription->units + $newunits;

				$email = ($received_payment > 0 or $newunits > 0)  ? 1 : 0;
				$statusmsg .= JText::_('COM_SERVICES_SUBSCRIPTION_ACTIVATED');
				if ($newunits > 0)
				{
					$statusmsg .=  ' ' . JText::_('for') . ' ' . $newunits . ' ';
					$statusmsg .= $oldunits > 0 ? JText::_('additional') . ' ' : '';
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
				$refund = ($subscription->totalpaid > 0 && $unitsleft > 0 && ($subscription->totalpaid - $unitsleft * $unitcost) > 0) ? $unitsleft * $prevunitcost : 0;
				$subscription->status = 2;
				$subscription->pendingpayment = $refund;
				$subscription->pendingunits = $refund > 0  ? $unitsleft : 0;
				$email = 1;
				$statusmsg .= JText::_('COM_SERVICES_SUBSCRIPTION_CANCELLED');
			break;
		}

		if (($action && $action != 'message') || $message)
		{
			$subscription->notes .= '------------------------------' . "\r\n";
			$subscription->notes .= JText::_('COM_SERVICES_SUBSCRIPTION_STATUS_UPDATED') . ', '.JFactory::getDate() . "\r\n";
			$subscription->notes .= $statusmsg ? $statusmsg . "\r\n" : '';
			$subscription->notes .= $message   ? $message . "\r\n"   : '';
			$subscription->notes .= '------------------------------' . "\r\n";
		}

		if (!$subscription->check())
		{
			$this->addComponentMessage($subscription->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($subscription);
			return;
		}
		if (!$subscription->store())
		{
			$this->addComponentMessage($subscription->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($subscription);
			return;
		}

		if ($email || $message)
		{
			$jconfig = JFactory::getConfig();

			// E-mail "from" info
			$from = array();
			$from['email'] = $jconfig->getValue('config.mailfrom');
			$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_SERVICES_SUBSCRIPTIONS');

			// start email message
			$subject = JText::sprintf('COM_SERVICES_EMAIL_SUBJECT', $subscription->code);
			$emailbody  = $subject . ':' . "\r\n";
			$emailbody .= JText::_('COM_SERVICES_SUBSCRIPTION_SERVICE') . ' - ' . $service->title . "\r\n";
			$emailbody .= '----------------------------------------------------------' . "\r\n";

			$emailbody .= $action != 'message' && $statusmsg ? $statusmsg : '';
			if ($message)
			{
				$emailbody .= "\r\n";
				$emailbody .= $message;
			}

			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('subscriptions_message', $subject, $emailbody, $from, array($subscription->uid), $this->_option)))
			{
				$this->addComponentMessage(JText::_('COM_SERVICES_ERROR_FAILED_TO_MESSAGE'), 'error');
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_SERVICES_SUBSCRIPTION_SAVED') . ($statusmsg ? ' ' . $statusmsg : '')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

