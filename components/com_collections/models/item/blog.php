<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__DIR__) . DS . 'item.php');

/**
 * Collections model for a blog post
 */
class CollectionsModelItemBlog extends CollectionsModelItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'blog';

	/**
	 * Get the item type
	 *
	 * @param   string  $as  Return type as?
	 * @return  string
	 */
	public function type($as=null)
	{
		if ($as == 'title')
		{
			return JText::_('Blog post');
		}
		return parent::type($as);
	}

	/**
	 * Chck if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (JRequest::getCmd('option') != 'com_blog')
		{
			return false;
		}

		if (!JRequest::getVar('alias'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create an item entry
	 *
	 * @param   integer  $id  Optional ID to use
	 * @return  boolean
	 */
	public function make($id=null)
	{
		if ($this->exists())
		{
			return true;
		}

		$id = ($id ?: JRequest::getInt('id', 0));

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'post.php');
		$post = null;

		if (!$id)
		{
			$alias = JRequest::getVar('alias', '');

			$post = new BlogModelEntry($alias, 'site');
			$id = $post->get('id');
		}

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$post)
		{
			$post = new BlogModelEntry($id);
		}

		if (!$post->exists())
		{
			$this->setError(JText::_('Blog post not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $post->get('id'))
		     ->set('created', $post->get('created'))
		     ->set('created_by', $post->get('created_by'))
		     ->set('title', $post->get('title'))
		     ->set('description', $post->content('clean', 200))
		     ->set('url', JRoute::_($post->link()));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
