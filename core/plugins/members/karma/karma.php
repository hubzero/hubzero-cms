<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;

/**
 * Members plugin for karma
 *
 * Shows a member's standing on their profile at whatever granularity the
 * scale permits the viewer. Contains no visibility logic of its own: that
 * rule lives in Karma::describe(), so it cannot drift between the places it
 * is applied.
 */
class plgMembersKarma extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array();

		if ($this->visibleScales($user, $member))
		{
			$areas['karma']      = Lang::txt('PLG_MEMBERS_KARMA');
			$areas['icon']       = 'f005';
			$areas['icon-class'] = 'icon-star';
			$areas['menu']       = $this->params->get('display_tab', 1);
		}

		return $areas;
	}

	/**
	 * The scales this viewer may see something on for this member
	 *
	 * An empty list means the tab does not appear at all, which is the right
	 * outcome: a tab that always says "nothing to show here" advertises that
	 * there is something being withheld.
	 *
	 * @param   object  $user    Viewer
	 * @param   object  $member  Profile being viewed
	 * @return  array
	 */
	protected function visibleScales($user, $member)
	{
		$visible = array();
		$isAdmin = User::authorise('karma.viewexact', 'com_karma');

		foreach (Scale::all()->whereEquals('state', 1)->order('ordering', 'asc')->rows() as $scale)
		{
			$value = Karma::describe(
				$member->get('id'),
				$scale->get('alias'),
				$user->get('id'),
				$isAdmin
			);

			if (!is_null($value))
			{
				$visible[] = array('scale' => $scale, 'value' => $value);
			}
		}

		return $visible;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		if (!$returnhtml)
		{
			return $arr;
		}

		$view = $this->view('default', 'index');

		$view->option   = $option;
		$view->member   = $member;
		$view->user     = $user;
		$view->scales   = $this->visibleScales($user, $member);
		$view->isSelf   = ((int) $user->get('id') === (int) $member->get('id'));
		$view->standing = $view->isSelf ? Karma::standing($member->get('id')) : array();

		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
