<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Script class for moving tag alias field into associative table
 * that allows for multiple aliases
 */
class MoveTagAlias extends SystemHelperScript
{
	/**
	 * Description for '_description'
	 * 
	 * @var string
	 */
	protected $_description = 'Move tag alias to substitution tbl';

	/**
	 * Run the script
	 * 
	 * @return     void
	 */
	public function run()
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');

		$this->_db->setQuery("SELECT * FROM #__tags WHERE alias!='' AND alias IS NOT NULL");
		$tags = $this->_db->loadObjectList();

		$i = 0;
		if ($tags)
		{
			foreach ($tags as $tag)
			{
				$sub = new TagsTableSubstitute($this->_db);
				$sub->raw_tag = trim($tag->alias);
				$sub->tag_id  = $tag->id;
				if ($sub->check())
				{
					if (!$sub->store())
					{
						$this->setError($sub->getError());
					}
					else 
					{
						$i++;
					}
				}
			}
		}

		if (!$this->getError())
		{
			echo '<p class="passed">' . $i . 'tags successfully moved.</p>';
		}
		else 
		{
			echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
		}
	}
}
