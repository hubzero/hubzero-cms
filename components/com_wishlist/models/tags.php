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
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

/**
 * Forum Tagging class
 */
class WishlistModelTags extends TagsModelCloud
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var string
	 */
	protected $_scope = 'wishlist';

	/**
	 * Turn a string of tags to an array
	 *
	 * @param      string $tag Tag string
	 * @return     mixed
	 */
	public function parseTags($tag, $remove='')
	{
		if (is_array($tag))
		{
			$bunch = $tag;
		}
		else
		{
			$bunch = $this->_parse($tag);
		}

		$tags = array();
		if ($remove)
		{
			foreach ($bunch as $t)
			{
				if ($remove == $t)
				{
					continue;
				}
				$tags[] = $t;
			}
		}
		else
		{
			return $bunch;
		}

		return $tags;
	}

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
		switch (strtolower($rtrn))
		{
			case 'string':
				if (!isset($this->_cache['tags_string']) || $clear)
				{
					$tags = array();
					foreach ($this->tags('list', $filters, $clear) as $tag)
					{
						$tags[] = $tag->get('raw_tag');
					}
					$this->_cache['tags_string'] = implode(', ', $tags);
				}
				return $this->_cache['tags_string'];
			break;

			case 'array':
				return $this->tags('list', $filters, $clear);
			break;

			case 'cloud':
			case 'html':
			default:
				if (!isset($this->_cache['tags_cloud']) || $clear)
				{
					$view = new \Hubzero\Component\View(array(
						'base_path' => JPATH_ROOT . '/components/com_wishlist',
						'name'      => 'wishlist',
						'layout'    => '_tags'
					));
					if (isset($filters['filters']))
					{
						$view->base    = $filters['base'];
						$view->filters = $filters['filters'];
					}
					$view->config = $this->_config;
					$view->tags   = $this->tags('list', $filters, $clear);

					$this->_cache['tags_cloud'] = $view->loadTemplate();
				}
				return $this->_cache['tags_cloud'];
			break;
		}
	}
}
