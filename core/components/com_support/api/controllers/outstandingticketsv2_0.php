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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

use Components\Support\Models\Criterion;
use Hubzero\Component\ApiController;

require_once dirname(dirname(__DIR__)) . '/models/criterion.php';
require_once dirname(dirname(__DIR__)) . '/helpers/acl.php';

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
