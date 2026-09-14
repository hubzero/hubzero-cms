<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Moderation\Grant;
use Hubzero\Utility\Date;
use Request;

/**
 * What the credit economy has actually been doing
 *
 * The screen that tells an administrator whether their grantor is the right
 * one. Both failure modes are visible here rather than inferred: a scheme
 * issuing nothing shows as zeros in the granted column, and a scheme issuing
 * more than anybody spends shows as a ratio drifting toward nothing.
 */
class Grants extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'display');

		parent::execute();
	}

	/**
	 * The record, and what it adds up to
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$days  = max(1, Request::getInt('days', 14));
		$since = Date::of('now')->subtract($days . ' days')->toSql();

		$rows = Grant::all()
			->where('created', '>=', $since)
			->order('created', 'desc')
			->rows();

		$totals = array();

		foreach ($rows as $row)
		{
			$type = $row->get('item_type');

			if (!isset($totals[$type]))
			{
				$totals[$type] = (object) array(
					'item_type' => $type,
					'grantor'   => $row->get('grantor'),
					'passes'    => 0,
					'issued'    => 0,
					'spent'     => 0,
					'expired'   => 0,
					'granted'   => 0,
					'idle'      => 0
				);
			}

			$totals[$type]->passes++;
			$totals[$type]->issued  += (int) $row->get('credits_issued');
			$totals[$type]->spent   += (int) $row->get('credits_spent');
			$totals[$type]->expired += (int) $row->get('credits_expired');
			$totals[$type]->granted += (int) $row->get('granted');

			if (!(int) $row->get('granted'))
			{
				$totals[$type]->idle++;
			}
		}

		// The number worth looking at. A healthy economy spends most of what
		// it issues; a ratio near zero means credits are being handed to
		// people who do not want them, and a ratio near one with nothing
		// expiring means the supply is too tight.
		foreach ($totals as $total)
		{
			$total->ratio = $total->issued ? round($total->spent / $total->issued, 2) : null;
		}

		$this->view
			->set('rows', $rows)
			->set('totals', $totals)
			->set('days', $days)
			->display();
	}
}
