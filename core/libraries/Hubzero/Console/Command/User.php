<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Utility\Date;

/**
 * User class for general user functions
 **/
class User extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * Generates list of available commands and their respective tasks
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Help doc for user command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview('General user functions for manipulating hub users.')
		     ->addTasks($this)
		     ->render();
	}

	/**
	 * Merge two user accounts into one
	 *
	 * @TODO: middleware tables?
	 *
	 * @museDescription  Merges two users together, disabling the source user
	 *
	 * @return  void
	 **/
	public function merge()
	{
		$sourceUser           = 0;
		$destinationUser      = 0;
		$directionalIndicator = $this->arguments->getOpt(4);

		if ($directionalIndicator)
		{
			switch ($directionalIndicator)
			{
				case 'into':
					$sourceUser      = (int)$this->arguments->getOpt(3);
					$destinationUser = (int)$this->arguments->getOpt(5);
					break;

				default:
					// Do nothing...can't assume
					break;
			}

			if ((!$sourceUser || !$destinationUser) && !$this->output->isInteractive())
			{
				$this->output->error('Please provide a source and destination user in the format: muse user merge [sourceUserId] into [destUserId]');
			}
		}
		else
		{
			if ($this->output->isInteractive())
			{
				$destinationUser = (int)$this->output->getResponse('What is the destination user ID (this is the user that will remain after the merge)?');
				$sourceUser      = (int)$this->output->getResponse('What is the source user ID (this is the user that will be deleted after the merge)?');
			}
			else
			{
				$this->output->error('Please provide a source and destination user in the format: muse user merge [sourceUserId] into [destUserId]');
			}
		}

		$suser  = \User::getInstance($sourceUser);
		$duser  = \User::getInstance($destinationUser);
		$dbo    = App::get('db');
		$tables = $dbo->getTableList();
		$prefix = $dbo->getPrefix();
		$fields = array(
			'created_by', 'modified_by', 'reviewed_by', 'user_id', 'userid', 'authorid', 'checked_out', 'uid',
			'uidNumber', 'created_user_id', 'modified_user_id', 'object_id', 'follower_id', 'following_id',
			'sent_by', 'redeemed_by', 'userid', 'creator_id', 'addedBy', 'editedBy', 'user_id_to', 'user_id_from',
			'commenter', 'uploaded_by', 'posted_by', 'assigned_to', 'closed_by', 'owned_by_user', 'created_by_user',
			'ran_by', 'foreign_key', 'taggerid', 'actor_id', 'voter', 'proposed_by', 'granted_by', 'assigned',
			'approved_by', 'action_by', 'authorid'
		);
		$unames = array(
			$prefix . 'event_registration.username',
			//$prefix . 'resource_stats_clusters.username',
			//$prefix . 'resource_stats_tools_users.user',
			//$prefix . 'session_geo.username',
			$prefix . 'support_acl_aros.alias',
			$prefix . 'support_comments.created_by',
			$prefix . 'support_tickets.login',
			$prefix . 'tool.team',
			$prefix . 'tool.registered_by',
			$prefix . 'tool_version.released_by',
			$prefix . 'wiki_page.authors'
		);
		$excludes = array(
			$prefix . 'xprofiles',
			$prefix . 'session_log',
			$prefix . 'session',
			$prefix . 'users_quotas'
		);
		$constraints = array(
			$prefix . 'collections.object_id'              => "AND `object_type` = 'member'",
			$prefix . 'collections_following.follower_id'  => "AND `follower_type` = 'member'",
			$prefix . 'collections_following.following_id' => "AND `following_type` = 'member'",
			$prefix . 'support_acl_aros.foreign_key'       => "AND `model` = 'user'",
			$prefix . 'support_acl_aros.alias'             => "AND `model` = 'user'"
		);

		// First, make sure we were given valid user ids
		if (!$suser->get('id') || !$duser->get('id'))
		{
			$this->output->error('User does not appear to be valid');
		}

		// Secondly, make sure this user hasn't been involved in a merge beforehand
		$query = "SELECT `id` FROM `#__users_merge_log` WHERE `source` = '{$sourceUser}'";
		$dbo->setQuery($query);
		if ($dbo->loadResult())
		{
			$this->output->error('This user appears to have already been merged into another user.');
		}

		foreach ($tables as $table)
		{
			// Ignore a few tables
			if (in_array($table, $excludes))
			{
				continue;
			}

			// Figure out what the table's primary key is
			$query = "SHOW INDEX FROM `{$table}` WHERE `Key_name` = 'PRIMARY'";
			$dbo->setQuery($query);
			$index = $dbo->loadObject();
			$tablePK = (isset($index->Column_name)) ? $index->Column_name : false;

			// Get the columns
			$columns = $dbo->getTableColumns($table);

			// Loop over columns and see if they're in our list from above
			foreach ($columns as $column => $type)
			{
				if (in_array($table.'.'.$column, $unames) || in_array($column, $fields))
				{
					$sUserName = $suser->get('username');
					$dUserName = $duser->get('username');

					// We have a match, now check if there are rows to merge
					$query  = "SELECT * FROM `{$table}` WHERE `{$column}` = '{$sourceUser}' OR `{$column}` LIKE '%{$sUserName}%'";
					$query .= ((isset($constraints[$table.'.'.$column])) ? ' ' . $constraints[$table.'.'.$column]: '');
					$dbo->setQuery($query);
					$results = $dbo->loadObjectList();

					if ($results && count($results) > 0)
					{
						$count = count($results);
						foreach ($results as $row)
						{
							if (!$tablePK)
							{
								$this->output->addLine("Ignoring {$table}.{$column} due to lack of primary key", 'warning');
								continue 2;
							}

							if (!$this->arguments->getOpt('dry-run'))
							{
								try
								{
									$numericUpdate = true;
									if (is_numeric($row->$column))
									{
										$query = "UPDATE `{$table}` SET `{$column}` = '{$destinationUser}' WHERE `{$tablePK}` = '{$row->$tablePK}'";
									}
									else
									{
										$numericUpdate = false;
										$query = "UPDATE `{$table}` SET `{$column}` = REPLACE({$column}, '{$sUserName}', '{$dUserName}') WHERE `{$tablePK}` = '{$row->$tablePK}'";
									}

									$dbo->setQuery($query);
									$dbo->query();

									// Now log it
									$log              = new \stdClass();
									$log->source      = ($numericUpdate) ? $sourceUser : $sUserName;
									$log->destination = ($numericUpdate) ? $destinationUser : $dUserName;
									$log->table       = $table;
									$log->column      = $column;
									$log->table_pk    = $tablePK;
									$log->table_id    = $row->$tablePK;
									$log->logged      = with(new Date('now'))->toSql();
									$dbo->insertObject('#__users_merge_log', $log);
								}
								catch (\Hubzero\Database\Exception\QueryFailedException $e)
								{
									if ($e->getPrevious()->getCode() == '23000')
									{
										$this->output->addLine("Ignoring {$table}.{$column} due to integrity constraint violation", 'warning');
										continue 2;
									}
									else
									{
										$this->output->error("Error: " . $e->getMessage());
									}
								}
							}
						}
						if ($this->arguments->getOpt('dry-run'))
						{
							$this->output->addLine("Would update ({$count}) item(s) in {$table}.{$column}");
						}
						else
						{
							$this->output->addLine("Updating ({$count}) item(s) in {$table}.{$column}", 'success');
						}
					}
				}
			}
		}

		// Lastly, block the user being merged
		if (!$this->arguments->getOpt('dry-run'))
		{
			$suser->set('block', 1);
			$suser->save();
		}
	}

	/**
	 * Reverse the merge process (via logs, not by mirroring the merge process)
	 *
	 * @museDescription  Unmerges a previous merge, reenabling the source user
	 *
	 * @return  void
	 **/
	public function unmerge()
	{
		$sourceUser           = 0;
		$destinationUser      = 0;
		$directionalIndicator = $this->arguments->getOpt(4);

		if ($directionalIndicator)
		{
			switch ($directionalIndicator)
			{
				case 'from':
					$sourceUser      = (int)$this->arguments->getOpt(5);
					$destinationUser = (int)$this->arguments->getOpt(3);
					break;

				default:
					// Do nothing...can't assume
					break;
			}

			if ((!$sourceUser || !$destinationUser) && !$this->output->isInteractive())
			{
				$this->output->error('Please provide a source and destination user in the format: muse user unmerge [destUserId] from [sourceUserId]');
			}
		}
		else
		{
			if ($this->output->isInteractive())
			{
				$destinationUser = (int)$this->output->getResponse('What is the destination user ID (this is the user that was deleted during the initial merge)?');
				$sourceUser      = (int)$this->output->getResponse('What is the source user ID (this is the user that was the recipient of the initially merged data)?');
			}
			else
			{
				$this->output->error('Please provide a source and destination user in the format: muse user unmerge [destUserId] from [sourceUserId]');
			}
		}

		// Now, make sure a merge between these two actually exists in the logs
		$suser  = \User::getInstance($sourceUser);
		$duser  = \User::getInstance($destinationUser);
		$dbo    = App::get('db');

		// First, make sure we were given valid user ids
		if (!$suser->get('id') || !$duser->get('id'))
		{
			$this->output->error('User does not appear to be valid');
		}

		$sUserName = $suser->get('username');
		$dUserName = $duser->get('username');

		$query  = "SELECT * FROM `#__users_merge_log`";
		$query .= " WHERE (`source` = '{$destinationUser}' AND `destination` = '{$sourceUser}')";
		$query .= " OR    (`source` = '{$dUserName}' AND `destination` = '{$sUserName}')";
		$query .= " ORDER BY `table` ASC, `COLUMN` ASC";
		$dbo->setQuery($query);
		if (!$results = $dbo->loadObjectList())
		{
			$this->output->error("Sorry, we couldn't find a preexisting merge between these two users to undo");
		}
		else
		{
			if (count($results) > 0)
			{
				if ($this->output->isInteractive())
				{
					$progress = $this->output->getProgressOutput();
					$count = count($results);
					$progress->init('Unmerging records: ', 'ratio', $count);
				}

				$counter = 0;
				foreach ($results as $result)
				{
					if (is_numeric($result->source))
					{
						$query = "UPDATE `{$result->table}` SET `{$result->column}` = '{$result->source}' WHERE `{$result->table_pk}` = '{$result->table_id}'";
					}
					else
					{
						$query = "UPDATE `{$result->table}` SET `{$result->column}` = REPLACE({$result->column}, '{$result->destination}', '{$result->source}') WHERE `{$result->table_pk}` = '{$result->table_id}'";
					}
					$dbo->setQuery($query);
					if ($dbo->query())
					{
						$query = "DELETE FROM `#__users_merge_log` WHERE `id` = '{$result->id}'";
						$dbo->setQuery($query);
						$dbo->query();

						if ($this->output->isInteractive())
						{
							++$counter;
							$progress->setProgress($counter, $count);
						}
						else
						{
							$this->output->addLine("Unmerging {$result->table}.{$result->column}");
						}
					}
				}

				if ($this->output->isInteractive())
				{
					$progress->done();
					$this->output->addLine("Unmerged ({$counter}/{$count}) records successfully!", 'success');
				}
			}
		}

		// Unblock the user
		$duser->set('block', 0);
		$duser->save();
	}

	/**
	 * Block a user (probably because of spamming)
	 *
	 * @museDescription  Disables a user completely, without deleting
	 *
	 * @return  void
	 **/
	public function disable()
	{
		$this->output->addLine('Not implemented', 'warning');
	}
}
