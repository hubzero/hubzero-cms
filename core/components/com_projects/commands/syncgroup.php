<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Components\Projects\Tables\Owner;
use Hubzero\Utility\Ldap;

require_once dirname(__DIR__) . DS . 'tables' . DS . 'owner.php';

/**
 * Re-sync project login groups (pr-<alias>) to LDAP.
 *
 * Remediation for the historical gap where deleting a project left members in
 * its pr-<alias> LDAP group and thus with SFTP access (ticket 2802). Running
 * this on a deleted (state=2) project empties that group -- getIds() now
 * excludes deleted projects, so sysGroup() sets an empty membership -- and then
 * pushes it to LDAP explicitly (the onAfterStoreGroup event that does this in a
 * web request may not have its user plugins loaded under the CLI).
 **/
class Syncgroup extends Base implements CommandInterface
{
	/**
	 * Default -- show help.
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Re-sync the login group of deleted (state=2) projects. --alias=<alias> for
	 * one, --all for every deleted project, --limit=N to cap, --dry-run to only
	 * report what would change.
	 *
	 * @museDescription  Re-sync deleted projects' pr- login groups (ticket 2802)
	 * @return  void
	 **/
	public function deleted()
	{
		$db     = \App::get('db');
		$prefix = \Component::params('com_projects')->get('group_prefix', 'pr-');
		$alias  = $this->arguments->getOpt('alias');
		$all    = (bool) $this->arguments->getOpt('all');
		$dry    = (bool) $this->arguments->getOpt('dry-run');
		$limit  = (int) $this->arguments->getOpt('limit');

		if (!$alias && !$all)
		{
			$this->output->error('Specify --alias=<alias> or --all (optionally --dry-run, --limit=N).');
			return;
		}

		if ($alias)
		{
			$db->setQuery("SELECT id, alias FROM `#__projects` WHERE state = 2 AND alias = " . $db->quote($alias));
		}
		else
		{
			$q = "SELECT id, alias FROM `#__projects` WHERE state = 2 ORDER BY id";
			if ($limit > 0) { $q .= " LIMIT " . $limit; }
			$db->setQuery($q);
		}
		$rows = $db->loadObjectList();

		if (!$rows)
		{
			$this->output->addLine('No matching deleted projects.');
			return;
		}

		$owner   = new Owner($db);
		$changed = 0;

		foreach ($rows as $r)
		{
			$cn = $prefix . $r->alias;

			// current membership from the group store (what LDAP mirrors)
			$before = $this->groupMembers($db, $cn);

			if (empty($before))
			{
				$this->output->addLine(sprintf('  %-42s already empty', $cn));
				continue;
			}

			if ($dry)
			{
				$this->output->addLine(sprintf('  %-42s would empty (%d: %s)', $cn, count($before), implode(',', $before)));
				continue;
			}

			// Empty the group (getIds excludes state=2, so sysGroup sets no members)
			$owner->sysGroup($r->alias, $prefix);
			// Push to LDAP explicitly (CLI may not have the user plugins loaded)
			Ldap::syncGroup($cn);

			$after = $this->groupMembers($db, $cn);
			$changed++;
			$this->output->addLine(
				sprintf('  %-42s %d -> %d members', $cn, count($before), count($after)),
				empty($after) ? 'success' : 'warning'
			);
		}

		$this->output->addLine(
			($dry ? 'Dry run. ' : '') . 'Deleted projects processed: ' . count($rows)
			. ($dry ? '' : ', changed: ' . $changed)
		);
	}

	/**
	 * Usernames currently in the given group (via #__xgroups_members).
	 *
	 * @param   object  $db
	 * @param   string  $cn
	 * @return  array
	 **/
	protected function groupMembers($db, $cn)
	{
		$db->setQuery(
			"SELECT u.username FROM `#__xgroups_members` gm
			 JOIN `#__xgroups` g ON g.gidNumber = gm.gidNumber
			 JOIN `#__users`   u ON u.id = gm.uidNumber
			 WHERE g.cn = " . $db->quote($cn)
		);
		return (array) $db->loadColumn();
	}

	/**
	 * Help.
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->addOverview('Re-sync deleted projects\' pr-<alias> login groups so SFTP/LDAP access is revoked (ticket 2802).')
			->addTasks($this);
	}
}
