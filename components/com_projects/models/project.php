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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.php');

/**
 * Project model
 */
class ProjectsModelProject extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'Project';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_projects.project.about';

	/**
	 * JParameter
	 *
	 * @var object
	 */
	protected $_config = NULL;

	/**
	 * Returns a reference to an article model
	 *
	 * @param      mixed $oid Article ID or alias
	 * @return     object KbModelArticle
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		return $this->_date('created', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function modified($as='')
	{
		return $this->_date('modified', $as);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $key Field to return
	 * @param      string $as  What data to return
	 * @return     string
	 */
	protected function _date($key, $as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get($key), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get($key), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param      string  $as      Format to return content in [parsed, clean, raw]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function about($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('about.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => JRequest::getCmd('option', 'com_projects'),
						'scope'    => $this->get('alias') . DS . 'notes',
						'pagename' => 'projects',
						'pageid'   => $this->get('id'),
						'filepath' => $this->config('webpath'),
						'domain'   => $this->get('alias')
					);

					$content = (string) stripslashes($this->get('about', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						'com_projects.project.about',
						&$this,
						&$config
					));

					$this->set('about.parsed', (string) $this->get('about', ''));
					$this->set('about', $content);

					return $this->about($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->about('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('about'));
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
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param      string $key Config property to retrieve
	 * @return     mixed
	 */
	public function config($key=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = JComponentHelper::getParams('com_projects');
		}
		if ($key)
		{
			return $this->_config->get($key);
		}
		return $this->_config;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Do nothing here yet.
	}
}

