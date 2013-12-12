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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'announcement.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

if (!defined('ANNOUNCEMENTS_DATE_FORMAT'))
{
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		define('ANNOUNCEMENTS_DATE_TIMEZONE', true);
		define('ANNOUNCEMENTS_DATE_FORMAT', 'd M Y');
		define('ANNOUNCEMENTS_TIME_FORMAT', 'g:i a');
	}
	else
	{
		define('ANNOUNCEMENTS_DATE_TIMEZONE', 0);
		define('ANNOUNCEMENTS_DATE_FORMAT', '%d %b %Y');
		define('ANNOUNCEMENTS_TIME_FORMAT', '%I:%M %p');
	}
}

/**
 * Courses model class for a course
 */
class CoursesModelAnnouncement extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableAnnouncement';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'announcement';

	/**
	 * Returns a reference to a course model
	 *
	 * This method must be invoked as:
	 *     $offering = CoursesModelAnnouncement::getInstance($alias);
	 *
	 * @param      integer $oid ID (int)
	 * @return     object CoursesModelCourse
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new CoursesModelAnnouncement($oid);
		}

		return $instances[$oid];
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
				return JHTML::_('date', $dt, ANNOUNCEMENTS_DATE_FORMAT, ANNOUNCEMENTS_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $dt, ANNOUNCEMENTS_TIME_FORMAT, ANNOUNCEMENTS_DATE_TIMEZONE);
			break;

			default:
				return $dt;
			break;
		}
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

		switch ($as)
		{
			case 'parsed':
				if ($this->get('content_parsed'))
				{
					return $this->get('content_parsed');
				}

				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				$p = Hubzero_Wiki_Parser::getInstance();

				$wikiconfig = array(
					'option'   => 'com_courses',
					'scope'    => 'courses',
					'pagename' => $this->get('id'),
					'pageid'   => 0,
					'filepath' => '',
					'domain'   => ''
				);

				$this->set('content_parsed', $p->parse(stripslashes($this->get('content')), $wikiconfig));

				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($this->get('content_parsed'), $shorten, 0, 0);
					if (substr($content, -7) == '&#8230;') 
					{
						$content .= '</p>';
					}
					return $content;
				}

				return $this->get('content_parsed');
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 1);
				}
				return $content;
			break;

			case 'raw':
			default:
				return $this->get('content');
			break;
		}
	}
}

