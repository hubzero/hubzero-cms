<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Moderation\Consequences;
use Hubzero\Moderation\Reviewer;
use Hubzero\Moderation\Log;
use Hubzero\Utility\Date;
use Request;
use Notify;
use Event;
use Lang;
use User;
use App;

/**
 * Whether moderators are judged, and on what terms
 *
 * The screen exists mostly to stop review being switched on somewhere it
 * cannot work. A review system that never reaches consensus is worse than
 * none: moderations sit unresolved indefinitely, the fairness record never
 * fills, and the eligibility rules that read it quietly stop meaning anything.
 * So this shows a hub what it is actually producing before it decides.
 */
class Review extends AdminController
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
	 * The types that have a moderation plugin speaking for them
	 *
	 * @return  array
	 */
	protected function itemTypes()
	{
		$out = array();

		foreach ((array) Event::trigger('moderation.onModerationItemTypes') as $set)
		{
			foreach ((array) $set as $itemType => $settings)
			{
				$out[$itemType] = $settings;
			}
		}

		return $out;
	}

	/**
	 * What the hub is producing, against what review would need
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$rows = array();

		foreach ($this->itemTypes() as $itemType => $settings)
		{
			$consensus = isset($settings['review_consensus']) ? (int) $settings['review_consensus'] : 9;
			$needed    = $consensus * 10;

			$since = Date::of('now')->subtract('30 days')->toSql();

			$produced = Log::all()
				->whereEquals('item_type', $itemType)
				->where('created', '>=', $since)
				->total();

			$pending = Log::all()
				->whereEquals('item_type', $itemType)
				->whereEquals('review_status', Log::REVIEW_PENDING)
				->total();

			$rows[] = (object) array(
				'item_type'    => $itemType,
				'enabled'      => !empty($settings['review_enabled']),
				'consensus'    => $consensus,
				'produced'     => (int) $produced,
				'needed'       => $needed,
				'has_volume'   => Reviewer::hasVolumeFor($itemType, $consensus, 30),
				'pending'      => (int) $pending,
				'consequences' => new Consequences(isset($settings['review_consequences']) ? $settings['review_consequences'] : '')
			);
		}

		$this->view
			->set('rows', $rows)
			->display();
	}

	/**
	 * Check a consequences table before anybody relies on it
	 *
	 * Refused rather than corrected where it is not monotonic: a table that
	 * pays a moderator more for being judged less fair is never what anybody
	 * meant, and guessing which line was the mistake would be worse than
	 * saying so.
	 *
	 * @return  void
	 */
	public function checkTask()
	{
		Request::checkToken(array('get', 'post'));

		if (!User::authorise('core.admin', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$text  = Request::getString('consequences', '', 'post');
		$table = Consequences::parse($text);

		if (!$table)
		{
			Notify::error(Lang::txt('COM_KARMA_REVIEW_TABLE_UNREADABLE'));
		}
		elseif (!Consequences::isMonotonic($table))
		{
			Notify::error(Lang::txt('COM_KARMA_REVIEW_TABLE_NOT_MONOTONIC'));
		}
		else
		{
			Notify::success(Lang::txt('COM_KARMA_REVIEW_TABLE_OK', count($table)));
		}

		$this->cancelTask();
	}
}
