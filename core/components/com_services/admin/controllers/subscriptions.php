<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Admin\Controllers;

use Components\Services\Models\Subscription;
use Hubzero\Component\AdminController;
use Request;
use Notify;
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
		$filters = array(
			'status' => Request::getState(
				$this->_option . '.' . $this->_controller . '.status',
				'filter_status',
				'all'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// get all available subscriptions
		$query = Subscription::all();

		switch ($filters['status'])
		{
			case 'pending':
				$query->whereEquals('status', 0)
					->orWhere('pendingpayment', '>', 0)
					->orWhere('pendingunits', '>', 0);
			break;

			case 'cancelled':
				$query->whereEquals('status', 2);
			break;

			default:
			break;
		}

		// Get records
		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Edit Subscription
	 *
	 * @param   object  $subscription
	 * @return  void
	 */
	public function editTask($subscription=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($subscription))
		{
			$id = Request::getArray('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			$subscription = Subscription::oneOrNew($id);
		}

		if (!$subscription->get('id'))
		{
			Notify::warning(Lang::txt('COM_SERVICES_SUBSCRIPTION_NOT_FOUND'));
			return $this->cancelTask();
		}

		$customer = $subscription->user;

		// check available user funds
		$BTL = new \Hubzero\Bank\Teller($subscription->get('uid'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance;
		$funds   = ($funds > 0) ? $funds : 0;

		// Output the HTML
		$this->view
			->set('subscription', $subscription)
			->set('funds', $funds)
			->set('customer', $customer)
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

		$subscription = Subscription::oneOrFail($id);

		// get service
		$service = $subscription->service;

		if (!$service->get('id'))
		{
			Notify::error(Lang::txt('COM_SERVICES_SERVICE_NOT_FOUND') . ' ' .  $subscription->get('serviceid'));

			return $this->cancelTask();
		}

		$author    = User::getInstance($subscription->uid);
		$subscription->notes = rtrim(stripslashes(Request::getString('notes', '')));
		$action    = Request::getString('action', '');
		$message   = Request::getString('message', '');
		$statusmsg = '';
		$email     = 0;

		switch ($action)
		{
			case 'refund':
				$received_refund = Request::getInt('received_refund', 0);
				$newunits        = Request::getInt('newunits', 0);

				$pending      = $subscription->get('pendingpayment') - $received_refund;
				$pendingunits = $subscription->get('pendingunits') - $newunits;

				$subscription->set('pendingpayment', ($pending <= 0 ? 0 : $pending));
				$subscription->set('pendingunits', ($pendingunits <= 0 ? 0 : $pendingunits));

				$email = 0;
				$statusmsg .= Lang::txt('Refund has been processed.');
			break;

			case 'activate':
				$received_payment = Request::getInt('received_payment', 0);
				$newunits         = Request::getInt('newunits', 0);

				$pending      = $subscription->get('pendingpayment') - $received_payment;
				$pendingunits = $subscription->get('pendingunits') - $newunits;

				$subscription->set('pendingpayment', ($pending <= 0 ? 0 : $pending));
				$subscription->set('pendingunits', ($pendingunits <= 0 ? 0 : $pendingunits));
				$subscription->set('totalpaid', ($subscription->get('totalpaid') + $received_payment));

				$oldunits  = $subscription->get('units');
				$months    = $newunits * $service->get('unitsize');
				$newexpire = ($oldunits > 0  && intval($subscription->get('expires')) <> 0)
					? Date::of(strtotime($subscription->get('expires') . "+" . $months . "months"))->format("Y-m-d")
					: Date::of(strtotime("+" . $months . "months"))->format("Y-m-d");

				$subscription->set('expires', ($newunits ? $newexpire : $subscription->get('expires')));
				$subscription->set('status', 1);
				$subscription->set('units', ($subscription->get('units') + $newunits));

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
				$unitsleft = $subscription->getRemaining('unit', $service->get('maxunits'), $service->get('unitsize'));

				// get cost per unit (to compute required refund)
				$refund = ($subscription->get('totalpaid') > 0 && $unitsleft > 0 && ($subscription->get('totalpaid') - $unitsleft * $unitcost) > 0) ? $unitsleft * $prevunitcost : 0;

				$subscription->set('status', 2);
				$subscription->set('pendingpayment', $refund);
				$subscription->set('pendingunits', ($refund > 0 ? $unitsleft : 0));

				$email = 1;
				$statusmsg .= Lang::txt('COM_SERVICES_SUBSCRIPTION_CANCELLED');
			break;
		}

		if (($action && $action != 'message') || $message)
		{
			$notes  = '------------------------------' . "\r\n";
			$notes .= Lang::txt('COM_SERVICES_SUBSCRIPTION_STATUS_UPDATED') . ', '. Date::toSql() . "\r\n";
			$notes .= $statusmsg ? $statusmsg . "\r\n" : '';
			$notes .= $message   ? $message . "\r\n"   : '';
			$notes .= '------------------------------' . "\r\n";

			$subscription->set('notes', $subscription->get('notes') . $notes);
		}

		if (!$subscription->save())
		{
			Notify::error($subscription->getError());
			return $this->editTask($subscription);
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
				Notify::error(Lang::txt('COM_SERVICES_ERROR_FAILED_TO_MESSAGE'));
			}
		}

		Notify::success(Lang::txt('COM_SERVICES_SUBSCRIPTION_SAVED') . ($statusmsg ? ' ' . $statusmsg : ''));

		$this->cancelTask();
	}
}
