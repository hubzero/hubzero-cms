<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

use Components\Support\Models\Criterion;
use Hubzero\Component\ApiController;
use Component;

require_once Component::path('com_support') . '/models/criterion.php';
require_once Component::path('com_support') . '/helpers/acl.php';

/**
 * API controller class for outstanding support tickets
 */
class OutstandingTicketsv2_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->acl = \Components\Support\Helpers\ACL::getACL();
		$this->acl->setUser(\User::get('id'));

		parent::execute();
	}

	/**
	 * Display a list of tickets that violate criteria
	 *
	 * @apiMethod GET
	 * @apiUri    /support/outstandingtickets
	 * @return    void
	 */
	public function listTask()
	{
		$this->requiresAuthentication();

		$criteria = Criterion::all();
		$outstandingTicketData = array(
			'tickets'  => array(),
			'criteria' => array()
		);

		foreach ($criteria as $criterion)
		{
			$criterionId = $criterion->get('id');
			$outstanding = $criterion->getViolations();

			$outstandingTicketData['criteria'][$criterionId] = $criterion->toArray();
			$outstandingTicketData['tickets'][$criterionId] = $outstanding;
		}

		$this->send($outstandingTicketData);
	}
}
