<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Orm;

//use Hubzero\Base\ItemList;
use Components\Tags\Models\Cloud;
//use Components\Tags\Models\Tag;

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
	 * @param   array    $ids    Bulletin ids
	 * @param   integer  $admin  Admin flag
	 * @return  array
	 */
	/*public function getTagsForIds($ids=array(), $admin=0)
	{
		$tt = new Tables\Tag($this->_db);
		$tj = new Tables\Object($this->_db);

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
		$this->_db->setQuery($sql);

		$tags = array();
		if ($items = $this->_db->loadObjectList())
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
	}*/

	/**
	 * Append a tag to the internal cloud
	 * 
	 * @param   mixed   $tag
	 * @return  object
	 */
	/*public function append($tag)
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
					$this->_cache['tags.list']->add(new Tag($t));
				}
				return $this;
			}
			else
			{
				$tag = new Tag($tag);
			}
		}
		$this->_cache['tags.list']->add($tag);

		return $this;
	}*/
}
