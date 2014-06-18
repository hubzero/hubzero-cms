<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Model class for a group announcement
 */
class GroupsModelAnnouncement extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Hubzero\\Item\\Announcement';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'plg_groups_announcements.announcement.content';

	/**
	 * Scope
	 *
	 * @var string
	 */
	protected $_scope = 'group';

	/**
	 * Group associated with
	 * this announcement
	 *
	 * @var object
	 */
	protected $_group = null;

	/**
	 * User profile
	 *
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * URL for this entry
	 *
	 * @var string
	 */
	private $_base = null;

	/**
	 * Check if the entry is available
	 *
	 * @return     boolean
	 */
	public function isAvailable()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished() || $this->isDeleted())
		{
			return false;
		}

		$now = JFactory::getDate();

		// Is a publish up date set and, if so,
		// is it after "now"?
		if ($this->get('publish_up')
		 && $this->get('publish_up') != $this->_db->getNullDate()
		 && $this->get('publish_up') > $now)
		{
			return false;
		}

		// Is a publish down date set and, if so,
		// is it before "now"?
		if ($this->get('publish_down')
		 && $this->get('publish_down') != $this->_db->getNullDate()
		 && $this->get('publish_down') <= $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	public function published($as='')
	{
		$dt = ($this->get('publish_up') && $this->get('publish_up') != '0000-00-00 00:00:00')
			? $this->get('publish_up')
			: $this->get('created');
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $dt, JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $dt, JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $dt;
			break;
		}
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->getPicture($this->get('anonymous'));
			}
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	public function group($property='')
	{
		if (!($this->_group instanceof \Hubzero\User\Group))
		{
			$this->_group = \Hubzero\User\Group::getInstance($this->get('scope_id'));
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'gidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_group->getLogo();
			}
			return $this->_group->get($property);
		}
		return $this->_group;
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
				$content = $this->get('content_parsed', null);
				if ($content == null)
				{
					$config = array(
						'option'   => 'com_groups',
						'scope'    => 'groups',
						'pagename' => $this->group('cn'),
						'pageid'   => 0,
						'filepath' => JPATH_ROOT . DS . 'site' . DS . 'groups' . DS . $this->group('gidNumber'),
						'domain'   => ''
					);

					$content = stripslashes($this->get('content'));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content_parsed', $this->get('content'));
					$this->set('content', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('content'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			$this->_base = 'index.php?option=' . JRequest::getCmd('option', 'com_groups') . '&cn=' . $this->group('cn') . '&active=announcements';
		}
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&action=edit&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&action=delete&id=' . $this->get('id');
			break;

			case 'permalink':
			default:

			break;
		}

		return $link;
	}
}

