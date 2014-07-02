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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'attachment.php');

/**
 * Courses model class for a forum
 */
class ForumModelPost extends ForumModelAbstract
{
	/**
	 * Table class name
	 *
	 * @var object
	 */
	protected $_tbl_name = 'ForumTablePost';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_forum.post.comment';

	/**
	 * ForumModelAttachment
	 *
	 * @var object
	 */
	protected $_attachment = null;

	/**
	 * Returns a reference to a forum post model
	 *
	 * @param      mixed $oid ID (int) or array or object
	 * @return     object ForumModelPost
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new ForumModelPost($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Set and get a specific offering
	 *
	 * @return     void
	 */
	public function attachment()
	{
		if (!isset($this->_attachment))
		{
			$this->_attachment = ForumModelAttachment::getInstance(0, $this->get('id'));
		}
		return $this->_attachment;
	}

	/**
	 * Has this post been reported
	 *
	 * @return     boolean True if reported, False if not
	 */
	public function isReported()
	{
		if ($this->get('state') == self::APP_STATE_FLAGGED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	public function modified($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return JHTML::_('date', $this->get('modified'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('modified'), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('modified');
			break;
		}
	}

	/**
	 * Determine if record was modified
	 *
	 * @return     boolean True if modified, false if not
	 */
	public function wasModified()
	{
		if ($this->get('modified') && $this->get('modified') != '0000-00-00 00:00:00')
		{
			return true;
		}
		return false;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$new = true;
		if ($this->get('id'))
		{
			$old = new ForumModelPost($this->get('id'));
			$new = false;
		}

		if (!$this->get('anonymous'))
		{
			$this->set('anonymous', 0);
		}

		if (!parent::store($check))
		{
			return false;
		}

		if (!$new)
		{
			if ($old->get('category_id') != $this->get('category_id'))
			{
				$this->_tbl->updateReplies(array(
					'category_id' => $this->get('category_id'),
					$this->get('id')
				));
			}
		}

		return true;
	}

		/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param      string  $as    Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param      integer $admin Include amdin tags? (defaults to no)
	 * @return     boolean
	 */
	public function tags($as='cloud', $admin=0)
	{
		if (!$this->exists())
		{
			switch (strtolower($as))
			{
				case 'array':
					return array();
				break;

				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}

		$cloud = new ForumModelTags($this->get('thread'));

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @return     boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new ForumModelTags($this->get('thread'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @param      mixed  $params Optional string or associative array of params to append
	 * @return     string
	 */
	public function link($type='', $params=null)
	{
		return $this->adapter()->build($type, $params);
	}

	/**
	 * Get the adapter
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!$this->_adapter)
		{
			$this->_adapter = $this->_adapter();
			$this->_adapter->set('thread', $this->get('thread'));
			$this->_adapter->set('parent', $this->get('parent'));
			$this->_adapter->set('post', $this->get('id'));

			if (!$this->get('category'))
			{
				$category = ForumModelCategory::getInstance($this->get('category_id'));
				$this->set('category', $category->get('alias'));
			}
			$this->_adapter->set('category', $this->get('category'));

			if (!$this->get('section'))
			{
				$category = ForumModelCategory::getInstance($this->get('category_id'));
				$this->set('section', ForumModelSection::getInstance($category->get('section_id'))->get('alias'));
			}
			$this->_adapter->set('section', $this->get('section'));
		}

		return $this->_adapter;
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('content.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_forum',
						'scope'    => 'forum',
						'pagename' => 'forum',
						'pageid'   => $this->get('thread'),
						'filepath' => '',
						'domain'   => $this->get('thread')
					);

					$attach = new ForumTableAttachment($this->_db);

					$content = (string) stripslashes($this->get('comment', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content.parsed', (string) $this->get('comment', ''));
					$this->set('content.parsed', $this->get('content.parsed') . $attach->getAttachment(
						$this->get('id'),
						$this->link('download'),
						$this->_config
					));
					$this->set('comment', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = $this->get('comment');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}
}

