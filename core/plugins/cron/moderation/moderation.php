<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Moderation\Economy;
use Hubzero\Moderation\Eligibility;
use Hubzero\Moderation\Activity;
use Hubzero\Moderation\Grantor\IntervalGrantor;
use Hubzero\Utility\Date;

/**
 * Cron plugin for moderation
 *
 * Keeps credits circulating. Which item types have an economy at all is the
 * hub's to say: a component announces itself by answering
 * onModerationItemTypes, and a hub that has registered none does nothing here.
 */
class plgCronModeration extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  object
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'grantModerationCredits',
				'label'  => Lang::txt('PLG_CRON_MODERATION_GRANT'),
				'params' => ''
			),
			array(
				'name'   => 'expireModerationCredits',
				'label'  => Lang::txt('PLG_CRON_MODERATION_EXPIRE'),
				'params' => ''
			),
			array(
				'name'   => 'pruneModerationActivity',
				'label'  => Lang::txt('PLG_CRON_MODERATION_PRUNE'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Hand out credits for every registered item type
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function grantModerationCredits(\Components\Cron\Models\Job $job)
	{
		foreach ($this->itemTypes() as $itemType => $settings)
		{
			$economy = new Economy(
				$itemType,
				new IntervalGrantor($settings),
				new Eligibility($settings, array($this, 'authorise'))
			);

			$grant = $economy->grant();

			Log::debug(sprintf(
				'plg_cron_moderation: %s — %s eligible, %s granted, %s credits issued',
				$itemType,
				$grant->get('eligible'),
				$grant->get('granted'),
				$grant->get('credits_issued')
			));
		}

		return true;
	}

	/**
	 * Take back credits nobody spent in time
	 *
	 * Recorded against the next grant rather than discarded, so the ratio of
	 * issued to expired stays visible — that ratio is the main thing telling
	 * an administrator whether the settings suit the hub.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function expireModerationCredits(\Components\Cron\Models\Job $job)
	{
		foreach ($this->itemTypes() as $itemType => $settings)
		{
			$economy = new Economy($itemType);
			$expired = $economy->expire();

			if ($expired)
			{
				Log::debug('plg_cron_moderation: ' . $itemType . ' — ' . $expired . ' credits expired unspent');
			}
		}

		return true;
	}

	/**
	 * Forget reading older than the eligibility window
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function pruneModerationActivity(\Components\Cron\Models\Job $job)
	{
		$days   = max(2, (int) $this->params->get('activity_window_days', 2)) + 1;
		$before = with(new Date('-' . $days . ' days'))->format('Y-m-d');

		Activity::prune($before);

		return true;
	}

	/**
	 * Which item types have an economy, and how it is tuned
	 *
	 * A component answers with its type and settings. Nothing is assumed:
	 * a hub that has registered none gets no credits and no cron work.
	 *
	 * @return  array
	 */
	protected function itemTypes()
	{
		$types = array();

		$responses = Event::trigger('moderation.onModerationItemTypes', array());

		foreach ((array) $responses as $response)
		{
			foreach ((array) $response as $itemType => $settings)
			{
				$types[$itemType] = is_array($settings) ? $settings : array();
			}
		}

		return $types;
	}

	/**
	 * Answer whether a member holds a permission
	 *
	 * Handed to Eligibility so the library never reaches for a facade.
	 *
	 * @param   integer  $userId
	 * @param   string   $permission
	 * @return  bool
	 */
	public function authorise($userId, $permission)
	{
		if (!$permission)
		{
			return true;
		}

		// A permission is given as "<component>.<action>", so story.moderate
		// is the moderate action on com_story.
		$parts = explode('.', $permission, 2);
		$asset = (count($parts) > 1) ? 'com_' . $parts[0] : 'com_karma';

		return (bool) User::getInstance($userId)->authorise($permission, $asset);
	}
}
