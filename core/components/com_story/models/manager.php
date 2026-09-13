<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Base\Obj;

require_once __DIR__ . DS . 'story.php';
require_once __DIR__ . DS . 'section.php';
require_once __DIR__ . DS . 'topic.php';
require_once __DIR__ . DS . 'discussion.php';
require_once __DIR__ . DS . 'comment.php';
require_once __DIR__ . DS . 'preference.php';
require_once __DIR__ . DS . 'adapters' . DS . 'site.php';

/**
 * One scope's worth of stories
 *
 *     $stories = new Manager('site', 0);
 *
 * Everything the site controllers ask for goes through here, so that serving
 * a research group's own front page later is a different adapter rather than
 * a different component.
 */
class Manager extends Obj
{
	/**
	 * Which scope
	 *
	 * @var  string
	 */
	protected $_scope = 'site';

	/**
	 * Which thing within it
	 *
	 * @var  integer
	 */
	protected $_scope_id = 0;

	/**
	 * The adapter for this scope
	 *
	 * @var  object
	 */
	protected $_adapter = null;

	/**
	 * Constructor
	 *
	 * @param   string   $scope
	 * @param   integer  $scopeId
	 * @return  void
	 */
	public function __construct($scope = 'site', $scopeId = 0)
	{
		$this->_scope    = (string) $scope;
		$this->_scope_id = (int) $scopeId;
	}

	/**
	 * The adapter for this scope
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!$this->_adapter)
		{
			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst(strtolower($this->_scope));

			if (!class_exists($cls))
			{
				$cls = __NAMESPACE__ . '\\Adapters\\Site';
			}

			$this->_adapter = new $cls($this->_scope_id);
		}

		return $this->_adapter;
	}

	/**
	 * This scope's sections
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public function sections($filters = array())
	{
		$query = Section::all()
			->whereEquals('scope', $this->_scope)
			->whereEquals('scope_id', $this->_scope_id);

		if (!isset($filters['state']) || $filters['state'] !== null)
		{
			$query->whereEquals('state', isset($filters['state']) ? $filters['state'] : Section::STATE_PUBLISHED);
		}

		return $query->order('ordering', 'asc');
	}

	/**
	 * Stories readable right now
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public function stories($filters = array())
	{
		$query = Story::published($this->_scope, $this->_scope_id);

		if (!empty($filters['section_id']))
		{
			$query->whereEquals('section_id', (int) $filters['section_id']);
		}

		if (!empty($filters['topic_id']))
		{
			$query->whereEquals('topic_id', (int) $filters['topic_id']);
		}

		if (!empty($filters['year']))
		{
			$query->whereRaw('YEAR(`publish_up`) = ?', array((int) $filters['year']));
		}

		if (!empty($filters['month']))
		{
			$query->whereRaw('MONTH(`publish_up`) = ?', array((int) $filters['month']));
		}

		if (!empty($filters['day']))
		{
			$query->whereRaw('DAY(`publish_up`) = ?', array((int) $filters['day']));
		}

		if (!empty($filters['search']))
		{
			$query->whereLike('title', $filters['search']);
		}

		return $query->order('publish_up', 'desc');
	}

	/**
	 * One story by its slug and the day it went out
	 *
	 * The date is part of the address, so it is part of the lookup: two
	 * stories may share a slug as long as they did not run the same day.
	 *
	 * @param   string  $alias
	 * @param   array   $on  [year, month, day]
	 * @return  object
	 */
	public function story($alias, array $on = array())
	{
		$filters = array();

		if (count($on) === 3)
		{
			list($filters['year'], $filters['month'], $filters['day']) = $on;
		}

		return $this->stories($filters)->whereEquals('alias', (string) $alias)->row();
	}

	/**
	 * Topics that have something published under them
	 *
	 * @return  object
	 */
	public function topics()
	{
		return Topic::all()
			->whereEquals('state', Topic::STATE_PUBLISHED)
			->order('ordering', 'asc');
	}
}
