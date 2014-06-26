<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

/**
 * Support Tagging class
 */
class SupportModelTags extends TagsModelCloud
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
				$bits[] = '<a' . ($tag->admin ?  ' class="admin"' : '') . ' href="'.JRoute::_('index.php?option=com_support&task=tickets&find=tag:' . $tag->tag) . '">' . stripslashes($tag->raw_tag) . '</a>';
			}
			return implode(', ', $bits);
		}
		return parent::render($rtrn, $filters, $clear);
	}

	/**
	 * Check tag existence for tickets
	 *
	 * @param      integer $id        Resource ID
	 * @param      integer $tagger_id Tagger ID
	 * @param      integer $strength  Tag strength
	 * @param      integer $admin     Admin flag
	 * @return     array
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
		$where[] = "rt.tbl=" . $this->_db->Quote($this->_scope);

		if ($tagger_id != 0)
		{
			$where[] = "rt.taggerid=" . $this->_db->Quote($tagger_id);
		}
		if ($strength)
		{
			$where[] = "rt.strength=" . $this->_db->Quote($strength);
		}

		$sql .= implode(" AND ", $where) . " GROUP BY rt.objectid";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function append($tag)
	{
		if (!isset($this->_cache['tags']))
		{
			$this->_cache['tags'] = new \Hubzero\Base\ItemList(array());
		}

		if (!$tag)
		{
			return;
		}

		if (!is_object($tag))
		{
			$tg = new TagsTableTag($this->_db);
			$tg->set('raw_tag', $tag);

			$tag = $tg;
		}

		$this->_cache['tags']->add($tag);
	}
}

