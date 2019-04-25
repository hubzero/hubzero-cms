<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving projects activity to the global activity tables
 **/
class Migration20171005110402ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$newids = array();

		if ($this->db->tableExists('#__activity_logs')
		 && $this->db->tableExists('#__activity_recipients')
		 && $this->db->tableExists('#__project_activity'))
		{
			$query = "SELECT * FROM `#__project_activity` ORDER BY id ASC";
			$this->db->setQuery($query);
			$activities = $this->db->loadObjectList();

			foreach ($activities as $activity)
			{
				$action = 'created';
				$scope = 'project';
				$scope_id = $activity->projectid;
				$parent = 0;
				$log_id = 0;
				$blog = false;
				$todo = false;

				switch ($activity->activity)
				{
					case 'started the project':
						$action = 'created';
						$scope = 'project';
						$scope_id = $activity->projectid;
						break;

					case 'deleted project':
						$action = 'deleted';
						$scope = 'project';
						$scope_id = $activity->projectid;
						break;

					case 'joined the project':
						$action = 'joined';
						$scope = 'project';
						$scope_id = $activity->projectid;
						break;

					case 'left the project':
						$action = 'cancelled';
						$scope = 'project';
						$scope_id = $activity->projectid;
						break;

					case 'posted a to-do item':
					case 'posted a to do item':
						$action = 'created';
						$scope = 'project.todo';
						$scope_id = $activity->referenceid;
						$todo = true;
						break;

					case 'checked off a to-do item':
					case 'checked off a to do item':
						$action = 'updated';
						$scope = 'project.todo';
						$scope_id = $activity->referenceid;
						$todo = true;
						break;

					case 'said':
						$action = 'created';
						$scope = 'project.comment';
						$scope_id = $activity->projectid;
						$blog = true;
						if ($this->db->tableExists('#__project_microblog'))
						{
							$query = "SELECT * FROM `#__project_microblog` WHERE id=" . $activity->referenceid;
							$this->db->setQuery($query);
							$comment = $this->db->loadObject();
							$activity->activity = $comment->blogentry;
						}
						break;

					case 'commented on a to do item':
					case 'commented on a to-do item':
						$action = 'created';
						$scope = 'project.comment';
						$scope_id = $activity->referenceid;

						if ($this->db->tableExists('#__project_comments'))
						{
							$query = "SELECT * FROM `#__project_comments` WHERE id=" . $activity->referenceid;
							$this->db->setQuery($query);
							$comment = $this->db->loadObject();
							//$parent = $newids[$comment->itemid];
							$parent = (isset($tdoids[$comment->itemid]) ? $tdoids[$comment->itemid] : 0);
							$activity->activity = $comment->comment;
						}
						break;

					case 'commented on a blog post':
						$action = 'created';
						$scope = 'project.comment';
						$scope_id = $activity->projectid;

						if ($this->db->tableExists('#__project_comments'))
						{
							$query = "SELECT * FROM `#__project_comments` WHERE id=" . $activity->referenceid;
							$this->db->setQuery($query);
							$comment = $this->db->loadObject();
							$parent = (isset($blgids[$comment->itemid]) ? $blgids[$comment->itemid] : 0);
							//$parent = $newids[$comment->itemid];
							$activity->activity = $comment->comment;
						}
						break;

					case 'commented on an activity':
						$action = 'created';
						$scope = 'project.comment';
						$scope_id = $activity->projectid;

						if ($this->db->tableExists('#__project_comments'))
						{
							$query = "SELECT * FROM `#__project_comments` WHERE id=" . $activity->referenceid;
							$this->db->setQuery($query);
							$comment = $this->db->loadObject();
							$parent = (isset($newids[$comment->itemid]) ? $newids[$comment->itemid] : 0);
							$activity->activity = $comment->comment;
						}
						break;

					case 'added a new page in project notes':
						$action = 'created';
						$scope = 'project.note';
						$scope_id = $activity->referenceid;
						break;

					case 'changed the project settings':
					case 'edited project information':
					case 'replaced project picture':
						$action = 'updated';
						$scope = 'project';
						$scope_id = $activity->projectid;
						break;

					default:
						if (substr($activity->activity, 0, strlen('uploaded')) == 'uploaded')
						{
							$action = 'uploaded';
							$scope = 'project.file';
							$scope_id = $activity->projectid;
						}
						if (substr($activity->activity, 0, strlen('updated file')) == 'updated file')
						{
							$action = 'updated';
							$scope = 'project.file';
							$scope_id = $activity->projectid;
						}
						if (substr($activity->activity, 0, strlen('restored deleted file')) == 'restored deleted file')
						{
							$action = 'updated';
							$scope = 'project.file';
							$scope_id = $activity->projectid;
						}
						if (substr($activity->activity, 0, strlen('created database')) == 'created database')
						{
							$action = 'created';
							$scope = 'project.database';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('removed database')) == 'removed database')
						{
							$action = 'deleted';
							$scope = 'project.database';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('updated database')) == 'updated database')
						{
							$action = 'updated';
							$scope = 'project.database';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('started a new publication')) == 'started a new publication'
						 || substr($activity->activity, 0, strlen('started draft')) == 'started draft')
						{
							$action = 'created';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('started new version')) == 'started new version')
						{
							$action = 'created';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('published version')) == 'published version'
						 || substr($activity->activity, 0, strlen('re-published version')) == 're-published version')
						{
							$action = 'published';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('submitted draft')) == 'submitted draft'
						 || substr($activity->activity, 0, strlen('re-submitted draft')) == 're-submitted draft')
						{
							$action = 'submitted';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('deleted draft')) == 'deleted draft')
						{
							$action = 'deleted';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('reviewed')) == 'reviewed')
						{
							$action = 'reviewed';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('approved')) == 'approved')
						{
							$action = 'approved';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('reverted to draft')) == 'reverted to draft')
						{
							$action = 'reverted';
							$scope = 'publication';
							$scope_id = $activity->referenceid;
						}
						if (substr($activity->activity, 0, strlen('unpublished')) == 'unpublished')
						{
							$action = 'unpublished';
							$scope = 'publication';
						}
						if (substr($activity->activity, 0, strlen('edited page')) == 'edited page')
						{
							$action = 'updated';
							$scope = 'project.note';
							$scope_id = $activity->referenceid;
						}
						break;
				}

				$query = "INSERT INTO `#__activity_logs` (`id`, `created`, `created_by`, `description`, `action`, `scope`, `scope_id`, `details`, `anonymous`, `parent`)
						VALUES (null,
							" . $this->db->quote($activity->recorded) . ",
							" . $this->db->quote($activity->userid) . ",
							" . $this->db->quote($activity->activity) . ",
							" . $this->db->quote($action) . ",
							" . $this->db->quote($scope) . ",
							" . $this->db->quote($scope_id) . ",
							" . $this->db->quote(json_encode($activity)) . ",
							" . $this->db->quote(0) . ",
							" . $this->db->quote($parent) . ")";
				$this->db->setQuery($query);
				$this->db->query();

				$newids[$activity->id] = $this->db->insertid();
				if ($blog)
				{
					$blgids[$activity->referenceid] = $newids[$activity->id];
				}
				if ($todo)
				{
					$tdoids[$activity->referenceid] = $newids[$activity->id];
				}

				// Add to the project's feed
				$query = "INSERT INTO `#__activity_recipients` (`id`, `log_id`, `scope`, `scope_id`, `created`, `viewed`, `state`, `starred`)
						VALUES (null,
							" . $this->db->quote($newids[$activity->id]) . ",
							" . $this->db->quote('project') . ",
							" . $this->db->quote($activity->projectid) . ",
							" . $this->db->quote($activity->recorded) . ",
							" . $this->db->quote($activity->recorded) . ",
							" . $this->db->quote($activity->state == 2 ? $activity->state : 1) . ",
							" . $this->db->quote(0) . ")";
				$this->db->setQuery($query);
				$this->db->query();

				// We have a child comment
				// So, we want to force the parent to show up more recent in the list
				// to reflect the new comment.
				if ($parent && $activity->state != 2)
				{
					// Unset the parent's recipient record
					$query = "UPDATE `#__activity_recipients`
						SET `state`=0
						WHERE `state`=1
						AND `log_id`=" . $this->db->quote($parent) . "
						AND `scope`='project'
						AND `scope_id`=" . $this->db->quote($activity->projectid);
					$this->db->setQuery($query);
					$this->db->query();

					// And add a new recipient record with an updated timestamp
					$query = "INSERT INTO `#__activity_recipients` (`id`, `log_id`, `scope`, `scope_id`, `created`, `viewed`, `state`, `starred`)
							VALUES (null,
								" . $this->db->quote($parent) . ",
								" . $this->db->quote('project') . ",
								" . $this->db->quote($activity->projectid) . ",
								" . $this->db->quote($activity->recorded) . ",
								" . $this->db->quote($activity->recorded) . ",
								" . $this->db->quote(1) . ",
								" . $this->db->quote(0) . ")";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			//$query = "DROP TABLE IF EXISTS `#__project_activity`";
			//$this->db->setQuery($query);
			//$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$newids = array();
		$commentids = array();

		// Transfer articles
		if ($this->db->tableExists('#__activity_logs'))
		{
			if (!$this->db->tableExists('#__project_activity'))
			{
				$query = "CREATE TABLE `#__project_activity` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `projectid` int(11) NOT NULL DEFAULT '0',
				  `userid` int(11) NOT NULL DEFAULT '0',
				  `referenceid` varchar(255) NOT NULL DEFAULT '0',
				  `managers_only` tinyint(2) DEFAULT '0',
				  `admin` tinyint(2) DEFAULT '0',
				  `commentable` tinyint(2) NOT NULL DEFAULT '0',
				  `state` tinyint(2) NOT NULL DEFAULT '0',
				  `recorded` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `activity` varchar(255) NOT NULL DEFAULT '',
				  `highlighted` varchar(100) NOT NULL DEFAULT '',
				  `url` varchar(255) DEFAULT NULL,
				  `class` varchar(150) DEFAULT NULL,
				  `preview` mediumtext,
				  PRIMARY KEY (`id`),
				  KEY `idx_projectid` (`projectid`),
				  KEY `idx_state` (`state`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "SELECT * FROM `#__activity_logs` WHERE `scope` IN ('project', 'project.note', 'project.todo', 'publication', 'project.comment', 'project.file', 'project.todo.comment', 'project.database')";
			$this->db->setQuery($query);
			$activities = $this->db->loadObjectList();

			foreach ($activities as $activity)
			{
				if ($activity->details)
				{
					$details = json_decode($activity->details);
				}

				$query = "INSERT INTO `#__project_activity` (
							`id`,
							`projectid`,
							`userid`,
							`referenceid`,
							`managers_only`,
							`admin`,
							`commentable`,
							`state`,
							`recorded`,
							`activity`,
							`highlighted`,
							`url`,
							`class`,
							`preview`
						) VALUES (
							null,
							" . $this->db->quote($details->projectid) . ",
							" . $this->db->quote($details->userid) . ",
							" . $this->db->quote($details->referenceid) . ",
							" . $this->db->quote($details->managers_only) . ",
							" . $this->db->quote($details->admin) . ",
							" . $this->db->quote($details->commentable) . ",
							" . $this->db->quote($details->state) . ",
							" . $this->db->quote($details->recorded) . ",
							" . $this->db->quote($details->activity) . ",
							" . $this->db->quote($details->highlighted) . ",
							" . $this->db->quote($details->url) . ",
							" . $this->db->quote($details->class) . ",
							" . $this->db->quote($details->preview) . "
						)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableExists('#__activity_recipients'))
			{
				$query = "DELETE FROM `#__activity_recipients` WHERE `scope` IN ('project', 'project_managers')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
