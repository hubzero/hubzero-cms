<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models;

use Hubzero\Base\ItemList;
use Components\Tags\Models\Cloud;
use Components\Tags\Models\Tag;
use Components\Tags\Models\Objct;

require_once \Component::path('com_tags') . DS . 'models' . DS . 'cloud.php';

/**
 * Collections Tagging class
 */
class Tags extends Cloud
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var  string
	 */
	protected $_scope = 'bulletinboard';

	/**
	 * Get tags for a list of IDs
	 * 
	 * @param      array   $ids       Bulletin ids
	 * @param      integer $admin     Admin flag
	 * @return     array
	 */
	public function getTagsForIds($ids=array(), $admin=0)
	{
		$tt = Tag::blank();
		$tj = Objct::blank();

		if (!is_array($ids) || empty($ids))
		{
			return false;
		}

		$ids = array_map('intval', $ids);

		$sql = "SELECT t.tag, t.raw_tag, t.admin, rt.objectid
				FROM " . $tt->getTableName() . " AS t 
				INNER JOIN " . $tj->getTableName() . " AS rt ON (rt.tagid = t.id) AND rt.tbl='" . $this->_scope . "' 
				WHERE rt.objectid IN (" . implode(',', $ids) . ") ";

		switch ($admin)
		{
			case 1:
				$sql .= "";
			break;
			case 0:
			default:
				$sql .= "AND t.admin=0 ";
			break;
		}
		$sql .= "ORDER BY raw_tag ASC";
		$db = \App::get('db');
		$db->setQuery($sql);

		$tags = array();
		if ($items = $db->loadObjectList())
		{
			foreach ($items as $item)
			{
				if (!isset($tags[$item->objectid]))
				{
					$tags[$item->objectid] = array();
				}
				$tags[$item->objectid][] = $item;
			}
		}
		return $tags;
	}

	/**
	 * Append a tag to the internal cloud
	 * 
	 * @param   mixed   $tag
	 * @return  object
	 */
	public function append($tag)
	{
		if (!($this->_cache['tags.list'] instanceof ItemList))
		{
			$this->_cache['tags.list'] = new ItemList(array());
		}

		if (!$tag)
		{
			return $this;
		}

		if (!($tag instanceof Tag))
		{
			if (is_array($tag))
			{
				foreach ($tag as $t)
				{
					$t = is_object($t) ? $t->tag : $t;
					$this->_cache['tags.list']->add(Tag::oneByTag($t));
				}
				return $this;
			}
			else
			{
				$tag = Tag::oneByTag($tag);
			}
		}
		$this->_cache['tags.list']->add($tag);

		return $this;
	}
}
