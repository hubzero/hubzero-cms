<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Admin\Controllers;

use Components\Services\Tables\Subscription;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Event;
use Route;
use Lang;
use Date;
use User;
use App;

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
		$this->view->filters = array(
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Get sorting variables
			'sortby' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortby',
				'sortby',
				'pending'
			),
			'filterby' => Request::getState(
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
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SERVICES_SUBSCRIPTION_NOT_FOUND')
			);
			return;
		}

		$this->view->customer = User::getInstance($this->view->subscription->uid);

		// check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->view->subscription->uid);
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
		Request::checkToken();

		$id = Request::getInt('id', 0);

		$subscription = new Subscription($this->database);
		if (!$subscription->load($id))
		{
			App::redirect(
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
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SERVICES_SERVICE_NOT_FOUND') . ' ' .  $subscription->serviceid,
				'error'
			);
			return;
		}

		$author    = User::getInstance($subscription->uid);
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
				$newexpire = ($oldunits > 0  && intval($subscription->expires) <> 0) ? Date::of(strtotime($subscription->expires . "+" . $months . "months"))->format("Y-m-d") : Date::of(strtotime("+" . $months . "months"))->format("Y-m-d");
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
			$subscription->notes .= Lang::txt('COM_SERVICES_SUBSCRIPTION_STATUS_UPDATED') . ', '. Date::toSql() . "\r\n";
			$subscription->notes .= $statusmsg ? $statusmsg . "\r\n" : '';
			$subscription->notes .= $message   ? $message . "\r\n"   : '';
			$subscription->notes .= '------------------------------' . "\r\n";
		}

		if (!$subscription->check())
		{
			$this->setError($subscription->getError());
			$this->editTask($subscription);
			return;
		}
		if (!$subscription->store())
		{
			$this->setError($subscription->getError());
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

			if (!Event::trigger('xmessage.onSendMessage', array('subscriptions_message', $subject, $emailbody, $from, array($subscription->uid), $this->_option)))
			{
				\Notify::error(Lang::txt('COM_SERVICES_ERROR_FAILED_TO_MESSAGE'));
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SERVICES_SUBSCRIPTION_SAVED') . ($statusmsg ? ' ' . $statusmsg : '')
		);
	}
}
