<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Categories\Helpers;

use Component;
use Lang;
use App;

include_once __DIR__ . '/node.php';

/**
 * Categories Class.
 */
class Categories
{
	/**
	 * Array to hold the object instances
	 *
	 * @var  array
	 */
	public static $instances = array();

	/**
	 * Array of category nodes
	 *
	 * @var  mixed
	 */
	protected $_nodes;

	/**
	 * Array of checked categories -- used to save values when _nodes are null
	 *
	 * @var  array
	 */
	protected $_checkedCategories;

	/**
	 * Name of the extension the categories belong to
	 *
	 * @var  string
	 */
	protected $_extension = null;

	/**
	 * Name of the linked content table to get category content count
	 *
	 * @var  string
	 */
	protected $_table = null;

	/**
	 * Name of the category field
	 *
	 * @var  string
	 */
	protected $_field = null;

	/**
	 * Name of the key field
	 *
	 * @var  string
	 */
	protected $_key = null;

	/**
	 * Name of the items state field
	 *
	 * @var  string
	 */
	protected $_statefield = null;

	/**
	 * Array of options
	 *
	 * @var  array
	 */
	protected $_options = null;

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 * @return  void
	 */
	public function __construct($options)
	{
		$this->_extension = $options['extension'];
		$this->_table = $options['table'];
		$this->_field = (isset($options['field']) && $options['field']) ? $options['field'] : 'catid';
		$this->_key = (isset($options['key']) && $options['key']) ? $options['key'] : 'id';
		$this->_statefield = (isset($options['statefield'])) ? $options['statefield'] : 'state';

		$options['access'] = (isset($options['access'])) ? $options['access'] : 'true';
		$options['published'] = (isset($options['published'])) ? $options['published'] : 1;

		$this->_options = $options;
	}

	/**
	 * Returns a reference to a Categories object
	 *
	 * @param   string  $extension  Name of the categories extension
	 * @param   array   $options    An array of options
	 * @return  object  Categories object
	 */
	public static function getInstance($extension, $options = array())
	{
		$hash = md5($extension . serialize($options));

		if (isset(self::$instances[$hash]))
		{
			return self::$instances[$hash];
		}

		$parts = explode('.', $extension);
		$component = 'com_' . strtolower($parts[0]);
		$section = count($parts) > 1 ? $parts[1] : '';
		//$classname = ucfirst(substr($component, 4)) . ucfirst($section) . 'Categories';
		$classname = '\\Components\\' . ucfirst(substr($component, 4)) . '\\Site\\Helpers\\Category';

		if (!class_exists($classname))
		{
			$path  = Component::path($component) . '/site/helpers/category.php';

			if (is_file($path))
			{
				include_once $path;
			}
			else
			{
				return false;
			}
		}

		self::$instances[$hash] = new $classname($options);

		return self::$instances[$hash];
	}

	/**
	 * Loads a specific category and all its children in a JCategoryNode object
	 *
	 * @param   mixed    $id         an optional id integer or equal to 'root'
	 * @param   boolean  $forceload  True to force  the _load method to execute
	 * @return  mixed    Node object or null if $id is not valid
	 */
	public function get($id = 'root', $forceload = false)
	{
		if ($id !== 'root')
		{
			$id = (int) $id;

			if ($id == 0)
			{
				$id = 'root';
			}
		}

		// If this $id has not been processed yet, execute the _load method
		if ((!isset($this->_nodes[$id]) && !isset($this->_checkedCategories[$id])) || $forceload)
		{
			$this->_load($id);
		}

		// If we already have a value in _nodes for this $id, then use it.
		if (isset($this->_nodes[$id]))
		{
			return $this->_nodes[$id];
		}
		// If we processed this $id already and it was not valid, then return null.
		elseif (isset($this->_checkedCategories[$id]))
		{
			return null;
		}

		return false;
	}

	/**
	 * Load method
	 *
	 * @param   integer  $id  Id of category to load
	 * @return  void
	 */
	protected function _load($id)
	{
		$db = App::get('db');

		$extension = $this->_extension;
		// Record that has this $id has been checked
		$this->_checkedCategories[$id] = true;

		$query = $db->getQuery();

		// Right join with c for category
		$query->select('c.*');
		$case_when = ' CASE WHEN ';
		$case_when .= 'CHAR_LENGTH(c.alias)';
		$case_when .= ' THEN ';
		//$c_id = $query->castAsChar('c.id');
		$case_when .= 'CONCAT_WS(' . $db->quote(':') . ', ' . implode(', ', array('c.id', 'c.alias')) . ')'; //$query->concatenate(array('c.id', 'c.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= 'c.id END as slug';
		$query->select($case_when);

		$query->from('#__categories', 'c');
		$query->whereEquals('c.extension', $extension, 1)
			->orWhereEquals('c.extension', 'system', 1)
			->resetDepth();

		if ($this->_options['access'])
		{
			$query->whereIn('c.access', \User::getAuthorisedViewLevels());
		}

		if ($this->_options['published'] == 1)
		{
			$query->whereEquals('c.published', 1);
		}

		$query->order('c.lft', 'asc');

		// s for selected id
		if ($id != 'root')
		{
			// Get the selected category
			$query->whereEquals('s.id', (int) $id);

			if (App::isSite() && App::get('language.filter'))
			{
				$query->joinRaw('#__categories AS s', '(s.lft < c.lft AND s.rgt > c.rgt AND c.language in (' . $db->quote(Lang::getTag()) . ',' . $db->quote('*') . ')) OR (s.lft >= c.lft AND s.rgt <= c.rgt)', 'left');
			}
			else
			{
				$query->joinRaw('#__categories AS s', '(s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)', 'left');
			}
		}
		else
		{
			if (App::isSite() && App::get('language.filter'))
			{
				$query->whereIn('c.language', array(Lang::getTag(), '*'));
			}
		}

		$subQuery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ' .
			'ON cat.lft BETWEEN parent.lft AND parent.rgt WHERE parent.extension = ' . $db->quote($extension) .
			' AND parent.published != 1 GROUP BY cat.id) AS badcats';
		$query->joinRaw($subQuery, 'badcats.id = c.id', 'left');
		$query->whereRaw('badcats.id is null');

		// i for item
		if (isset($this->_options['countItems']) && $this->_options['countItems'] == 1)
		{
			if ($this->_options['published'] == 1)
			{
				$query->joinRaw(
					$db->quoteName($this->_table) . ' AS i',
					'i.' . $db->quoteName($this->_field) . ' = c.id AND i.' . $this->_statefield . ' = 1',
					'left'
				);
			}
			else
			{
				$query->joinRaw($db->quoteName($this->_table) . ' AS i', 'i.' . $db->quoteName($this->_field) . ' = c.id', 'left');
			}

			$query->select('COUNT(i.' . $db->quoteName($this->_key) . ')', 'numitems');
		}

		// Group by
		$query->group('c.id')
			->group('c.asset_id')
			->group('c.access')
			->group('c.alias')
			->group('c.checked_out')
			->group('c.checked_out_time')
			->group('c.created_time')
			->group('c.created_user_id')
			->group('c.description')
			->group('c.extension')
			->group('c.hits')
			->group('c.language')
			->group('c.level')
			->group('c.lft')
			->group('c.metadata')
			->group('c.metadesc')
			->group('c.metakey')
			->group('c.modified_time')
			->group('c.note')
			->group('c.params')
			->group('c.parent_id')
			->group('c.path')
			->group('c.published')
			->group('c.rgt')
			->group('c.title')
			->group('c.modified_user_id');

		// Get the results
		$db->setQuery($query->toString());
		$results = $db->loadObjectList('id');
		$childrenLoaded = false;

		if (count($results))
		{
			// Foreach categories
			foreach ($results as $result)
			{
				// Deal with root category
				if ($result->id == 1)
				{
					$result->id = 'root';
				}

				// Deal with parent_id
				if ($result->parent_id == 1)
				{
					$result->parent_id = 'root';
				}

				// Create the node
				if (!isset($this->_nodes[$result->id]))
				{
					// Create the JCategoryNode and add to _nodes
					$this->_nodes[$result->id] = new Node($result, $this);

					// If this is not root and if the current node's parent is in the list or the current node parent is 0
					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id == 1))
					{
						// Compute relationship between node and its parent - set the parent in the _nodes field
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
				elseif ($result->id == $id || $childrenLoaded)
				{
					// Create the JCategoryNode
					$this->_nodes[$result->id] = new Node($result, $this);

					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id))
					{
						// Compute relationship between node and its parent
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					if (!isset($this->_nodes[$result->parent_id]))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
			}
		}
		else
		{
			$this->_nodes[$id] = null;
		}
	}
}
