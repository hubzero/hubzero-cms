<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;
use Components\Support\Helpers\ACL;
use App;

require_once __DIR__ . DS . 'comment.php';
require_once __DIR__ . DS . 'status.php';
require_once __DIR__ . DS . 'category.php';
require_once __DIR__ . DS . 'watching.php';
require_once __DIR__ . DS . 'tags.php';
require_once __DIR__ . DS . 'message.php';
require_once __DIR__ . DS . 'queryfolder.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'acl.php';

/**
 * Support ticket model
 */
class Ticket extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'summary'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $always = array(
		'owner',
		'group_id',
		'target_date'
	);

	/**
	 * Tag cloud
	 *
	 * @var  object
	 */
	protected $_tags = null;

	/**
	 * Generates automatic summary field value
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticSummary($data)
	{
		$data['summary'] = (isset($data['summary']) ? $data['summary'] : $data['report']);
		$data['summary'] = trim($data['summary']);

		if (!$data['summary'])
		{
			$data['summary'] = $data['report'];
		}

		$data['summary'] = substr($data['summary'], 0, 70);

		if (strlen($data['report']) >= 70)
		{
			$data['summary'] .= '...';
		}

		return $data['summary'];
	}

	/**
	 * Generates automatic owner field value
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticOwner($data)
	{
		if (!isset($data['owner']) || !$data['owner'])
		{
			$data['owner'] = 0;
		}

		if ($data['owner'] && is_string($data['owner']))
		{
			$owner = User::getInstance($data['owner']);
			if ($owner && $owner->get('id'))
			{
				$data['owner'] = (int) $owner->get('id');
			}
		}

		return $data['owner'];
	}

	/**
	 * Generates automatic group_id field value
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticGroupId($data)
	{
		if (!isset($data['group_id']) || !$data['group_id'])
		{
			$data['group_id'] = 0;
		}

		if ($data['group_id'] && is_string($data['group_id']))
		{
			$group = \Hubzero\User\Group::getInstance($data['group_id']);
			if ($group && $group->get('gidNumber'))
			{
				$data['group_id'] = (int) $group->get('gidNumber');
			}
		}

		return $data['group_id'];
	}

	/**
	 * Generates automatic target_date value
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticTargetDate($data)
	{
		$data['target_date'] = isset($data['target_date']) ? $data['target_date'] : null;

		if (!$data['target_date'])
		{
			$data['target_date'] = null;
		}

		return $data['target_date'];
	}

	/**
	 * Is the ticket open?
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		return ($this->get('open') == 1);
	}

	/**
	 * Is the ticket in "waiting" status?
	 *
	 * @return  boolean
	 */
	public function isWaiting()
	{
		return ($this->isOpen() && $this->get('status') == 2);
	}

	/**
	 * Is the ticket owned?
	 *
	 * @return  boolean
	 */
	public function isOwned()
	{
		if ($this->get('owner'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Is the user the owner of the ticket?
	 *
	 * @param   integer  $id
	 * @return  boolean
	 */
	public function isOwner($id='')
	{
		if ($this->isOwned())
		{
			$id = $id ?: User::get('id');

			if ($this->get('owner') == $id)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Is the user the submitter of the ticket?
	 *
	 * @param   string  $username
	 * @return  boolean
	 */
	public function isSubmitter($username='')
	{
		$username = $username ?: User::get('username');

		if ($this->get('login') == $username)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the owner object
	 *
	 * @return  object
	 */
	public function assignee()
	{
		return $this->oneToOne('\Hubzero\User\User', 'id', 'owner');
	}

	/**
	 * Get a list of comments
	 *
	 * @return  object
	 */
	public function submitter()
	{
		return $this->oneToOne('\Hubzero\User\User', 'username', 'login');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  boolean
	 */
	public function created($as='')
	{
		$as = strtolower($as);
		$dt = $this->get('created');

		if ($as == 'date')
		{
			$dt = Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			$dt = Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as == 'local')
		{
			$dt = Date::of($this->get('created'))->toLocal();
		}

		return $dt;
	}

	/**
	 * Get the owner group object
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->oneToOne('\Hubzero\User\Group', 'id', 'group_id');
	}

	/**
	 * Get a list of comments
	 *
	 * @return  object
	 */
	public function comments()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Comment', 'ticket');
	}

	/**
	 * Get a list of attachments
	 *
	 * @return  object
	 */
	public function attachments()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Attachment', 'ticket')->whereEquals('comment_id', 0);
	}

	/**
	 * Get a list of watchers
	 *
	 * @return  object
	 */
	public function watchers()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Watching', 'ticket_id');
	}

	/**
	 * Get status
	 *
	 * @return  object
	 */
	public function transformStatus()
	{
		return Status::oneOrNew($this->get('status'));
	}

	/**
	 * Get category
	 *
	 * @return  object
	 */
	public function transformCategory()
	{
		return Category::oneByAlias($this->get('category'));
	}

	/**
	 * Get content
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		$text = $this->get('report_parsed', null);

		if ($text === null)
		{
			// Escape potentially bad characters
			$text = htmlentities($this->get('report'), ENT_COMPAT, 'UTF-8');

			// Convert line breaks to <br /> tags
			$text = nl2br($text);

			// Convert tabs to spaces to preserve indention
			$text = str_replace("\t", ' &nbsp; &nbsp;', $text);

			// Look for any attachments (old style) and remove
			// Attachments will be loaded through their relationship
			$text = preg_replace('/\{attachment#[0-9]*\}/sU', '', $text);

			$text = trim($text);

			if (!$text)
			{
				$text = Lang::txt('(no content found)');
			}

			$this->set('report_parsed', $text);
		}

		return $text;
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param   string   $as     Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param   integer  $admin  Include amdin tags? (defaults to no)
	 * @return  mixed
	 */
	public function tags($as='cloud', $admin=null)
	{
		if (!$this->_tags)
		{
			$this->_tags = new Tags($this->get('id'));
		}

		return $this->_tags->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags
	 * @param   integer  $useR_id
	 * @param   integer  $admin
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove data
		foreach ($this->comments()->rows() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->addError($comment->getError());
				return false;
			}
		}

		foreach ($this->attachments()->rows() as $attachment)
		{
			if (!$attachment->destroy())
			{
				$this->addError($attachment->getError());
				return false;
			}
		}

		foreach ($this->watchers()->rows() as $watch)
		{
			if (!$watch->destroy())
			{
				$this->addError($watch->getError());
				return false;
			}
		}

		$this->tag('');

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Check if a user is watching this ticket
	 *
	 * @param   integer  $user_id  User ID
	 * @return  boolean  True if watching, False if not
	 */
	public function isWatching($user_id=null)
	{
		$user_id = $user_id ?: User::get('id');

		$tbl = Watching::oneByUserAndTicket($user_id, $this->get('id'));

		if ($tbl->get('id'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Mark a user as "watching" this ticket
	 *
	 * @param   integer  $user_id  User ID
	 * @return  boolean
	 */
	public function watch($user_id)
	{
		$user_id = $user_id ?: User::get('id');

		$tbl = Watching::oneByUserAndTicket($user_id, $this->get('id'));

		if ($tbl->get('id'))
		{
			return true;
		}

		$tbl->set('ticket_id', $this->get('id'));
		$tbl->set('user_id', $user_id);

		if (!$tbl->save())
		{
			$this->addError($tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Remove a user from the watch list for this ticket
	 *
	 * @param   mixed   $user_id  User ID
	 * @return  boolean
	 */
	public function stopWatching($user_id)
	{
		$user_id = $user_id ?: User::get('id');

		$tbl = Watching::oneByUserAndTicket($user_id, $this->get('id'));

		if (!$tbl->get('id'))
		{
			return true;
		}

		if (!$tbl->destroy())
		{
			$this->addError($tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Mark a ticket as open
	 *
	 * @return  object
	 */
	public function open()
	{
		$this->set('open', 1)
		     ->set('status', 1)
		     ->set('resolved', '');

		return $this;
	}

	/**
	 * Mark a ticket as closed
	 *
	 * @param   string  $resolution
	 * @return  object
	 */
	public function close($resolution = '')
	{
		$this->set('open', 0)
		     ->set('status', 0)
		     ->set('resolved', $resolution);

		return $this;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		$link = 'index.php?option=com_support';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&controller=tickets&task=delete&id=' . $this->get('id');
			break;

			case 'update':
				$link .= '&controller=tickets&task=update';
			break;

			case 'stopwatching':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id') . '&watch=stop';
			break;

			case 'watch':
			case 'startwatching':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id') . '&watch=start';
			break;

			case 'comments':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id') . '#comments';
			break;

			case 'permalink':
			default:
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Access check
	 *
	 * @param   string  $action  The action to check
	 * @param   string  $item    The item to check the action against
	 * @return  boolean
	 */
	public function access($action='view', $item='tickets')
	{
		if (!$this->get('_access-check-done', false))
		{
			$this->_acl = ACL::getACL();

			if ($this->get('login') == User::get('username')
			 || $this->get('owner') == User::get('id'))
			{
				if (!$this->_acl->check('read', 'tickets'))
				{
					$this->_acl->setAccess('read', 'tickets', 1);
				}
				if (!$this->_acl->check('update', 'tickets'))
				{
					$this->_acl->setAccess('update', 'tickets', $this->isOwner() ? 1 : -1);
				}
				if (!$this->_acl->check('create', 'comments'))
				{
					$this->_acl->setAccess('create', 'comments', -1);
				}
				if (!$this->_acl->check('read', 'comments'))
				{
					$this->_acl->setAccess('read', 'comments', 1);
				}
			}

			if ($this->_acl->authorize($this->get('group_id')))
			{
				$this->_acl->setAccess('read', 'tickets', 1);
				$this->_acl->setAccess('update', 'tickets', 1);
				$this->_acl->setAccess('delete', 'tickets', 1);
				$this->_acl->setAccess('create', 'comments', 1);
				$this->_acl->setAccess('read', 'comments', 1);
				$this->_acl->setAccess('create', 'private_comments', 1);
				$this->_acl->setAccess('read', 'private_comments', 1);

				$this->set('_cc-check-done', true);
			}

			$this->set('_access-check-done', true);
		}

		if ($action == 'read' && $item == 'tickets' && !$this->_acl->check('read', 'tickets') && !$this->get('_cc-check-done'))
		{
			if (!User::get('guest') && $this->comments->count() > 0)
			{
				$last = $this->comments->last();

				$cc = $last->changelog()->get('cc');

				if (in_array(User::get('username'), $cc) || in_array(User::get('email'), $cc))
				{
					$this->_acl->setAccess('read', 'tickets', 1);
					$this->_acl->setAccess('create', 'comments', -1);
					$this->_acl->setAccess('read', 'comments', 1);
				}
			}
			$this->set('_cc-check-done', true);
		}

		return $this->_acl->check($action, $item);
	}

	/**
	 * Get a record count
	 *
	 * @param   string   $query    Filters to build query from
	 * @param   array    $filters
	 * @return  integer
	 */
	public static function countWithQuery($query, $filters=array())
	{
		if (!$query || $query->isNew())
		{
			return 0;
		}

		if (!is_string($query))
		{
			$query = $query->toSql();
		}

		$db = App::get('db');

		$having = '';
		if (preg_match('/GROUP BY f.id HAVING uniques=\'\d\'/i', $query, $matches)
		 || preg_match('/GROUP BY f.id/i', $query, $matches))
		{
			$having = $matches[0];
			$query = str_replace($matches[0], '', $query);

			$sql = "SELECT f.id, COUNT(DISTINCT t.tag) AS uniques ";
		}
		else
		{
			$sql = "SELECT count(DISTINCT f.id) ";
		}

		$sql .= "FROM `" . self::blank()->getTableName() . "` AS f";
		if (strstr($query, 't.`tag`') || (isset($filters['tag']) && $filters['tag'] != ''))
		{
			$sql .= " LEFT JOIN `#__tags_object` AS st on st.objectid=f.id AND st.tbl='support'
					LEFT JOIN `#__tags` AS t ON st.tagid=t.id";
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$sql .= " LEFT JOIN `#__support_comments` AS w ON w.ticket=f.id";
		}

		$sql .= self::parseFind($filters) . ($query ? " AND " . $query : "");

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$sql .= " AND ";
			$sql .= "(
						LOWER(f.report) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.name) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.login) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(w.comment) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%');
			if (is_numeric($filters['search']))
			{
				$sql .= " OR f.id=" . $filters['search'];
			}
			$sql .= ") ";
		}
		$sql .= $having;

		if ($having)
		{
			$db->setQuery($sql);
			$results = $db->loadObjectList();
			return count($results);
		}

		$db->setQuery($sql);
		return $db->loadResult();
	}

	/**
	 * Get a record count
	 *
	 * @param   string   $query    Filters to build query from
	 * @param   array    $filters
	 * @return  array
	 */
	public static function allWithQuery($query, $filters=array())
	{
		if (!$query || $query->isNew())
		{
			return array();
		}

		if (!is_string($query))
		{
			$query = $query->toSql();
		}

		$db = App::get('db');

		$having = '';
		if (preg_match('/GROUP BY f.id HAVING uniques=\'\d\'/i', $query, $matches))
		{
			$having = $matches[0];
			$query = str_replace($matches[0], '', $query);
		}

		$sql = "SELECT DISTINCT f.`id`, f.`summary`, f.`report`, f.`category`, f.`open`, f.`status`, f.`severity`, f.`resolved`, f.`group_id`, f.`owner`, f.`created`, f.`login`, f.`name`, f.`email`, f.`target_date` ";
		if ($having)
		{
			$sql .= ", COUNT(DISTINCT t.tag) AS uniques ";
		}
		$sql .= "FROM `" . self::blank()->getTableName() . "` AS f";
		if (strstr($query, 't.`tag`') || (isset($filters['tag']) && $filters['tag'] != ''))
		{
			$sql .= " LEFT JOIN `#__tags_object` AS st on st.objectid=f.id AND st.tbl='support'
					LEFT JOIN `#__tags` AS t ON st.tagid=t.id";
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$sql .= " LEFT JOIN `#__support_comments` AS w ON w.ticket=f.id";
		}

		$sql .= self::parseFind($filters) . ($query ? " AND " . $query : "");

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$sql .= " AND ";
			$sql .= "(
						LOWER(f.report) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.name) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.login) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(w.comment) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%');
			if (is_numeric($filters['search']))
			{
				$sql .= " OR f.id=" . $filters['search'];
			}
			$sql .= ") ";
		}
		$sql .= $having;

		if ($filters['sort'] == 'group')
		{
			$filters['sort'] = 'group_id';
		}

		if ($filters['sort'] == 'severity')
		{
			$sql .= " ORDER BY CASE severity ";
			$sql .= " WHEN 'critical' THEN 5";
			$sql .= " WHEN 'major'    THEN 4";
			$sql .= " WHEN 'normal'   THEN 3";
			$sql .= " WHEN 'minor'    THEN 2";
			$sql .= " WHEN 'trivial'  THEN 1";
			$sql .= " END " . $filters['sortdir'];
		}
		else
		{
			$sql .= " ORDER BY `" . $filters['sort'] . '` ' . $filters['sortdir'];
		}

		$sql .= ($filters['limit']) ? " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']) : "";

		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		foreach ($rows as $i => $row)
		{
			$rows[$i] = self::blank()->set($row);
		}

		return $rows;
	}

	/**
	 * Add tag and group filters previously supported in ticket system
	 * (ex: when clicking a tag within the ticket system)
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	public static function parseFind($filters)
	{
		$db = App::get('db');

		$filter = " WHERE report!=''";

		if (isset($filters['group']) && $filters['group'] != '')
		{
			if (!is_numeric($filters['group']))
			{
				if ($group = \Hubzero\User\Group::getInstance($filters['group']))
				{
					$filters['group'] = $group->get('gidNumber');
				}
			}
			$filter .= " AND `group_id`=" . $db->quote($filters['group']);
		}

		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			$filter .= " AND st.objectid=f.id AND st.tbl='support' AND st.tagid=t.id AND t.tag=" . $db->quote($filters['tag']);
		}

		return $filter;
	}
}
