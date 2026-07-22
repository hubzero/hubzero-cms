<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Preserve the historic com_events posting behavior after the controller was
 * changed to honor the component ACL (core.create / core.edit / core.edit.state
 * / core.edit.own) instead of hardcoding core.admin/core.manage.
 *
 * The stock root asset grants those actions to the standard content groups
 * (Author => create/edit.own, Editor => edit, Publisher => edit.state). Because
 * com_events left those actions empty (= inherit), the ACL-based controller
 * would silently let those groups post and manage events on upgrade. That was
 * NOT possible before (only site managers/admins could), so we pin the prior
 * behavior by denying those groups at the component level.
 *
 * This is applied per-action ONLY where the hub is still inheriting (empty
 * rule); any action a hub has already customized is left untouched, so a hub
 * that has deliberately opened posting to a group is never clobbered.
 */
class Migration20260708160000ComEvents extends Base
{
	/**
	 * Action => standard content group (by title) that stock root grants it to,
	 * and that we deny at the component level to reproduce the old behavior.
	 * Looked up by name so a hub with non-default group ids still matches.
	 *
	 * @var  array
	 */
	protected $actions = array(
		'core.create'     => 'Author',
		'core.edit'       => 'Editor',
		'core.edit.state' => 'Publisher',
		'core.edit.own'   => 'Author',
	);

	/**
	 * Resolve a user group id by its title.
	 *
	 * @param   string   $title
	 * @return  integer  group id, or 0 if not found
	 */
	protected function groupId($title)
	{
		$this->db->setQuery("SELECT `id` FROM `#__usergroups` WHERE `title` = " . $this->db->quote($title) . " LIMIT 1");
		return (int) $this->db->loadResult();
	}

	/**
	 * Load the com_events asset and its decoded rules.
	 *
	 * @return  object|null  {id, rules(array)} or null when unavailable
	 */
	protected function eventsAsset()
	{
		if (!$this->db->tableExists('#__assets'))
		{
			return null;
		}

		$this->db->setQuery("SELECT `id`, `rules` FROM `#__assets` WHERE `name` = 'com_events' LIMIT 1");
		$asset = $this->db->loadObject();

		if (!$asset)
		{
			return null;
		}

		$rules = json_decode($asset->rules, true);
		$asset->rules = is_array($rules) ? $rules : array();

		return $asset;
	}

	/**
	 * Persist decoded rules back to the com_events asset.
	 *
	 * @param   integer  $id
	 * @param   array    $rules
	 * @return  void
	 */
	protected function saveRules($id, $rules)
	{
		$this->db->setQuery("UPDATE `#__assets` SET `rules` = " . $this->db->quote(json_encode($rules)) . " WHERE `id` = " . $this->db->quote($id));
		$this->db->query();
	}

	/**
	 * Up
	 *
	 * @return  void
	 */
	public function up()
	{
		if (!($asset = $this->eventsAsset()))
		{
			return;
		}

		$rules = $asset->rules;

		foreach ($this->actions as $action => $title)
		{
			// Only pin the default where the hub is still inheriting this action.
			// A non-empty rule means an admin has already decided; leave it alone.
			if (!empty($rules[$action]))
			{
				continue;
			}

			// Skip if the standard group isn't present (renamed/removed on this hub).
			if (!($group = $this->groupId($title)))
			{
				continue;
			}

			$rules[$action] = array((string) $group => 0);
		}

		$this->saveRules($asset->id, $rules);
	}

	/**
	 * Down
	 *
	 * Reverses only the exact denies this migration added, so any later
	 * customization survives a rollback.
	 *
	 * @return  void
	 */
	public function down()
	{
		if (!($asset = $this->eventsAsset()))
		{
			return;
		}

		$rules = $asset->rules;

		foreach ($this->actions as $action => $title)
		{
			if (!($group = $this->groupId($title)))
			{
				continue;
			}

			if (isset($rules[$action]) && $rules[$action] === array((string) $group => 0))
			{
				$rules[$action] = array();
			}
		}

		$this->saveRules($asset->id, $rules);
	}
}
