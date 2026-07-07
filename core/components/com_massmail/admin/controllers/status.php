<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Massmail\Models\Mailing;
use Components\Massmail\Models\Recipient;
use Components\Massmail\Models\Status as StatusModel;
use Components\Massmail\Helpers\Queue;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'queue.php';

/**
 * Admin controller: mass-mail status dashboard.
 */
class Status extends AdminController
{
	/**
	 * Show the dashboard
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$db     = App::get('db');
		$params = \Component::params('com_massmail');

		$totalMailings = Mailing::all()->total();
		$activeMailings = Mailing::all()
			->whereIn('status', array(
				Mailing::STATUS_PREPARING,
				Mailing::STATUS_SENDING,
				Mailing::STATUS_PAUSED,
				Mailing::STATUS_PENDING_APPROVAL
			))
			->total();

		$db->setQuery("SELECT COUNT(*) FROM `#__massmail_recipients` WHERE status = " . Recipient::SENT);
		$messagesSent = (int) $db->loadResult();

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__massmail_recipients` r
			 JOIN `#__massmail_mailings` m ON m.id = r.mailing_id
			 WHERE r.status = " . Recipient::PENDING . "
			 AND m.status IN (" . $db->quote(Mailing::STATUS_SENDING) . ", " . $db->quote(Mailing::STATUS_PREPARING) . ", " . $db->quote(Mailing::STATUS_PAUSED) . ")"
		);
		$messagesPending = (int) $db->loadResult();

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__massmail_recipients`
			 WHERE status = " . Recipient::SENT . " AND sent_on IS NOT NULL
			 AND sent_on >= (UTC_TIMESTAMP() - INTERVAL 1 HOUR)"
		);
		$sentLastHour = (int) $db->loadResult();

		$db->setQuery(
			"SELECT state, last_run FROM `#__cron_jobs`
			 WHERE plugin = 'massmail' AND event = 'processMassmailQueue' LIMIT 1"
		);
		$cron = $db->loadObject();

		$this->view
			->set('params', $params)
			->set('snapshot', StatusModel::instance())
			->set('liveQueue', Queue::status())
			->set('totalMailings', $totalMailings)
			->set('activeMailings', $activeMailings)
			->set('messagesSent', $messagesSent)
			->set('messagesPending', $messagesPending)
			->set('sentLastHour', $sentLastHour)
			->set('cron', $cron)
			->display();
	}
}
