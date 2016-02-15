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

namespace Components\Support\Models;

use Components\Tags\Models\Cloud;
use Components\Tags\Models\Tag;
use Hubzero\Base\ItemList;
use Route;

require_once(dirname(dirname(__DIR__)) . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

/**
 * Support Tagging class
 */
class Tags extends Cloud
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var string
	 */
	protected $_scope = 'support';

	/**
	 * Render a tag cloud
	 *
	 * @param      string  $rtrn    Format to render
	 * @param      array   $filters Filters to apply
	 * @param      boolean $clear   Clear cached data?
	 * @return     string
	 */
	public function render($rtrn='html', $filters=array(), $clear=false)
	{
		if (strtolower($rtrn) == 'linkedlist')
		{
			$bits = array();
			foreach ($this->tags('list', $filters, $clear) as $tag)
			{
				$bits[] = '<a' . ($tag->get('admin') ?  ' class="admin"' : '') . ' href="' . Route::url('index.php?option=com_support&task=tickets&find=tag:' . $tag->get('tag')) . '">' . stripslashes($tag->get('raw_tag')) . '</a>';
			}
			return implode(', ', $bits);
		}
		return parent::render($rtrn, $filters, $clear);
	}

	/**
	 * Check tag existence for tickets
	 *
	 * @param   integer  $id         Resource ID
	 * @param   integer  $tagger_id  Tagger ID
	 * @param   integer  $strength   Tag strength
	 * @param   integer  $admin      Admin flag
	 * @return  array
	 */
	public function checkTags($id, $tagger_id=0, $strength=0, $admin=0)
	{
		$sql = "SELECT t.tag, t.raw_tag, t.description, t.admin, rt.id, rt.objectid FROM `#__tags_object` AS rt JOIN `#__tags` AS t ON t.id=rt.tagid WHERE ";

		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}

		$where = array();
		$where[] = "rt.objectid IN ($id)";
		$where[] = "rt.tbl=" . $this->_db->quote($this->_scope);

		if ($tagger_id != 0)
		{
			$where[] = "rt.taggerid=" . $this->_db->quote($tagger_id);
		}
		if ($strength)
		{
			$where[] = "rt.strength=" . $this->_db->quote($strength);
		}

		$sql .= implode(" AND ", $where) . " GROUP BY rt.objectid";

		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList('objectid');
	}

	/**
	 * Append a tag to the existing tag list
	 *
	 * @param   mixed  $tag
	 * @return  void
	 */
	public function append($tag)
	{
		if (!isset($this->_cache['tags']))
		{
			$this->_cache['tags'] = new ItemList(array());
		}

		if (!$tag)
		{
			return;
		}

		if (!($tag instanceof Tag))
		{
			$tg = Tag::oneByTag($tag);
			$tg->set('raw_tag', $tag);

			$tag = $tg;
		}

		$this->_cache['tags']->add($tag);
	}
}

