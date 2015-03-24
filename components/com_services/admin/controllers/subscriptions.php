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

namespace Components\Services\Admin\Controllers;

use Components\Services\Tables\Subscription;
use Hubzero\Component\AdminController;

/**
 * Controller class for service subscriptions
 */
class Subscriptions extends AdminController
{
		/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Subscriptions List
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = \JFactory::getApplication();

		$this->view->filters = array(
			// Get paging variables
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Get sorting variables
			'sortby' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortby',
				'sortby',
				'pending'
			),
			'filterby' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.filterby',
				'filterby',
				'all'
			)
		);

		$obj = new Subscription($this->database);

		// Record count
		$this->view->total = $obj->getSubscriptionsCount($this->view->filters, true);

		// Fetch results
		$this->view->rows = $obj->getSubscriptions($this->view->filters, true);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit Subscription
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getInt('id', 0);

			$row = new Subscription($this->database);
			$this->view->subscription = $row->getSubscription($id);
		}

		$this->view->subscription = $row;

		if (!$this->view->subscription)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SERVICES_SUBSCRIPTION_NOT_FOUND')
			);
			return;
		}

		$this->view->customer = \JUser::getInstance($this->view->subscription->uid);

		// check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->view->subscription->uid);
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance;
		$this->view->funds = ($funds > 0) ? $funds : '0';

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save Subscription
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		$id = Request::getInt('id', 0);

		$subscription = new Subscription($this->database);
		if (!$subscription->load($id))
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SERVICES_SUBSCRIPTION_NOT_FOUND'),
				'error'
			);
			return;
		}

		// get service
		$service = new Service($this->database);
		if (!$service->loadService('', $subscription->serviceid))
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SERVICES_SERVICE_NOT_FOUND') . ' ' .  $subscription->serviceid,
				'error'
			);
			return;
		}

		$author    = \JUser::getInstance($subscription->uid);
		$subscription->notes = rtrim(stripslashes(Request::getVar('notes', '')));
		$action    = Request::getVar('action', '');
		$message   = Request::getVar('message', '');
		$statusmsg = '';
		$email     = 0;

		switch ($action)
		{
			case 'refund':
				$received_refund = Request::getInt('received_refund', 0);
				$newunits = Request::getInt('newunits', 0);
				$pending = $subscription->pendingpayment - $received_refund;
				$pendingunits = $subscription->pendingunits - $newunits;
				$subscription->pendingpayment = $pending <= 0 ? 0 : $pending;
				$subscription->pendingunits = $pendingunits <= 0 ? 0 : $pendingunits;
				$email = 0;
				$statusmsg .= Lang::txt('Refund has been processed.');
			break;

			case 'activate':
				$received_payment = Request::getInt('received_payment', 0);
				$newunits = Request::getInt('newunits', 0);
				$pending = $subscription->pendingpayment - $received_payment;
				$pendingunits = $subscription->pendingunits - $newunits;
				$subscription->pendingpayment = $pending <= 0 ? 0 : $pending;
				$subscription->pendingunits = $pendingunits <= 0 ? 0 : $pendingunits;
				$subscription->totalpaid = $subscription->totalpaid + $received_payment;
				$oldunits = $subscription->units;

				$months = $newunits * $service->unitsize;
				$newexpire = ($oldunits > 0  && intval($subscription->expires) <> 0) ? \JFactory::getDate(strtotime($subscription->expires . "+" . $months . "months"))->format("Y-m-d") : \JFactory::getDate(strtotime("+" . $months . "months"))->format("Y-m-d");
				$subscription->expires = $newunits ? $newexpire : $subscription->expires;
				$subscription->status =  1;
				$subscription->units = $subscription->units + $newunits;

				$email = ($received_payment > 0 or $newunits > 0)  ? 1 : 0;
				$statusmsg .= Lang::txt('COM_SERVICES_SUBSCRIPTION_ACTIVATED');
				if ($newunits > 0)
				{
					$statusmsg .=  ' ' . Lang::txt('for') . ' ' . $newunits . ' ';
					$statusmsg .= $oldunits > 0 ? Lang::txt('additional') . ' ' : '';
					$statusmsg .= Lang::txt('month(s)');
				}
			break;

			case 'message':
				$statusmsg .= Lang::txt('Your message has been sent.');
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
				$statusmsg .= Lang::txt('COM_SERVICES_SUBSCRIPTION_CANCELLED');
			break;
		}

		if (($action && $action != 'message') || $message)
		{
			$subscription->notes .= '------------------------------' . "\r\n";
			$subscription->notes .= Lang::txt('COM_SERVICES_SUBSCRIPTION_STATUS_UPDATED') . ', '.\JFactory::getDate() . "\r\n";
			$subscription->notes .= $statusmsg ? $statusmsg . "\r\n" : '';
			$subscription->notes .= $message   ? $message . "\r\n"   : '';
			$subscription->notes .= '------------------------------' . "\r\n";
		}

		if (!$subscription->check())
		{
			$this->setMessage($subscription->getError(), 'error');
			$this->editTask($subscription);
			return;
		}
		if (!$subscription->store())
		{
			$this->setMessage($subscription->getError(), 'error');
			$this->editTask($subscription);
			return;
		}

		if ($email || $message)
		{
			// E-mail "from" info
			$from = array(
				'email' => Config::get('mailfrom'),
				'name'  => Config::get('sitename') . ' ' . Lang::txt('COM_SERVICES_SUBSCRIPTIONS')
			);

			// start email message
			$subject = Lang::txt('COM_SERVICES_EMAIL_SUBJECT', $subscription->code);
			$emailbody  = $subject . ':' . "\r\n";
			$emailbody .= Lang::txt('COM_SERVICES_SUBSCRIPTION_SERVICE') . ' - ' . $service->title . "\r\n";
			$emailbody .= '----------------------------------------------------------' . "\r\n";

			$emailbody .= $action != 'message' && $statusmsg ? $statusmsg : '';
			if ($message)
			{
				$emailbody .= "\r\n";
				$emailbody .= $message;
			}

			\JPluginHelper::importPlugin('xmessage');
			$dispatcher = \JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('subscriptions_message', $subject, $emailbody, $from, array($subscription->uid), $this->_option)))
			{
				$this->addComponentMessage(Lang::txt('COM_SERVICES_ERROR_FAILED_TO_MESSAGE'), 'error');
			}
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SERVICES_SUBSCRIPTION_SAVED') . ($statusmsg ? ' ' . $statusmsg : '')
		);
	}
}
