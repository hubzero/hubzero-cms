<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Moderation\Reviewer;
use Hubzero\Moderation\Reason;
use Hubzero\Moderation\Log;
use Document;
use Request;
use Pathway;
use Notify;
use Route;
use Event;
use Lang;
use User;
use App;

/**
 * Judging whether other people's moderations were fair
 *
 * A batch at a time. A member is dealt a handful, judges them together and
 * submits the lot — which is deliberate, because judging one at a time would
 * let somebody watch the consensus move and then place themselves on the
 * winning side of it.
 */
class Review extends SiteController
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
	 * The types a hub has review switched on for
	 *
	 * Read from the moderation plugins rather than configured here: the
	 * plugin that speaks for a type is the thing that knows whether its
	 * hub wants review on it.
	 *
	 * @return  array
	 */
	protected function reviewableTypes()
	{
		$out = array();

		foreach ((array) Event::trigger('moderation.onModerationItemTypes') as $set)
		{
			foreach ((array) $set as $itemType => $settings)
			{
				if (!empty($settings['review_enabled']))
				{
					$out[$itemType] = $settings;
				}
			}
		}

		return $out;
	}

	/**
	 * Breadcrumbs
	 *
	 * @return  void
	 */
	protected function trail()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_KARMA'), 'index.php?option=' . $this->_option);
		}

		Pathway::append(Lang::txt('COM_KARMA_REVIEW'), 'index.php?option=' . $this->_option . '&controller=review');
	}

	/**
	 * A batch to judge
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('COM_KARMA_REVIEW_LOGIN_REQUIRED'));
		}

		if (!User::authorise('story.review', 'com_story')
		 && !User::authorise('core.admin', $this->_option))
		{
			App::abort(403, Lang::txt('COM_KARMA_REVIEW_NOT_PERMITTED'));
		}

		$this->trail();

		Document::setTitle(Lang::txt('COM_KARMA_REVIEW'));

		$types = $this->reviewableTypes();

		if (!$types)
		{
			// Not an error. Review being off is the shipped state and the
			// right one for most hubs; saying so plainly beats a blank page.
			return $this->view
				->set('off', true)
				->set('batch', array())
				->set('refusal', '')
				->display();
		}

		$itemType = Request::getString('type', key($types));

		if (!isset($types[$itemType]))
		{
			$itemType = key($types);
		}

		$reviewer = new Reviewer(User::get('id'));
		$refusal  = '';
		$batch    = array();

		if (!$reviewer->isEligible($itemType, $types[$itemType]))
		{
			$refusal = $this->refusalText($reviewer->why());
		}
		else
		{
			$batch = $this->decorate(
				$reviewer->deal($itemType, (int) $this->config->get('review_batch', 10))
			);
		}

		$this->view
			->set('off', false)
			->set('itemType', $itemType)
			->set('types', $types)
			->set('batch', $batch)
			->set('refusal', $refusal)
			->set('config', $this->config)
			->display();
	}

	/**
	 * Put the reason and the moderated words alongside each moderation
	 *
	 * A judgement on a bare "somebody moved this down one" is not a judgement
	 * at all; the reviewer needs to see what was moderated and why.
	 *
	 * @param   array  $rows
	 * @return  array
	 */
	protected function decorate($rows)
	{
		$out = array();

		foreach ($rows as $row)
		{
			$item = null;

			foreach ((array) Event::trigger('moderation.onModerationResolveItem', array($row->get('item_type'), $row->get('item_id'))) as $found)
			{
				if (is_object($found))
				{
					$item = $found;
					break;
				}
			}

			$reason = Reason::oneOrNew($row->get('reason_id'));

			$out[] = (object) array(
				'log'    => $row,
				'item'   => $item,
				'reason' => $reason->get('id') ? $reason : null
			);
		}

		return $out;
	}

	/**
	 * Say why somebody was not dealt anything
	 *
	 * @param   string  $code
	 * @return  string
	 */
	protected function refusalText($code)
	{
		$known = array('nobody', 'karma', 'record', 'too_soon');

		if (!in_array($code, $known))
		{
			$code = 'nobody';
		}

		return Lang::txt('COM_KARMA_REVIEW_REFUSED_' . strtoupper($code));
	}

	/**
	 * Take a judged batch
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		if (User::isGuest())
		{
			App::abort(403, Lang::txt('COM_KARMA_REVIEW_LOGIN_REQUIRED'));
		}

		$itemType  = Request::getString('type', '');
		$judgments = Request::getArray('judgment', array(), 'post');

		$types = $this->reviewableTypes();

		if (!isset($types[$itemType]))
		{
			App::abort(404, Lang::txt('COM_KARMA_REVIEW_NOT_OPEN'));
		}

		$reviewer = new Reviewer(User::get('id'));

		if (!$reviewer->isEligible($itemType, $types[$itemType]))
		{
			Notify::warning($this->refusalText($reviewer->why()));

			return $this->cancel();
		}

		foreach ($judgments as $logId => $value)
		{
			// Skipping is a real answer. Somebody who cannot tell should not
			// be pushed into guessing, so an unanswered row is left alone.
			if ($value === '' || $value === null)
			{
				continue;
			}

			$reviewer->record((int) $logId, (int) $value);
		}

		$written = $reviewer->commit();

		$written
			? Notify::success(Lang::txt('COM_KARMA_REVIEW_THANKS', $written))
			: Notify::warning(Lang::txt('COM_KARMA_REVIEW_NOTHING_RECORDED'));

		$this->cancel();
	}

	/**
	 * Back to the queue
	 *
	 * @return  void
	 */
	protected function cancel()
	{
		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=review'));
	}
}
