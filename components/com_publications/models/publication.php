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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');

/**
 * Courses model class for a forum
 */
class PublicationsModelPublication extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'Publication';

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
	 * Set a property
	 *
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
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
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_creator->get($property);
		}
		return $this->_creator;
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
	public function description($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('description.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_publications',
						'scope'    => '',
						'pagename' => 'publications',
						'pageid'   => '',
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get('description', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						'com_publications.publication.description',
						&$this,
						&$config
					));

					$this->set('description.parsed', (string) $this->get('description', ''));
					$this->set('description', $content);

					return $this->description($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->description('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('description'));
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
	public function notes($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('release_notes.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_publications',
						'scope'    => '',
						'pagename' => 'publications',
						'pageid'   => '',
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get('release_notes', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						'com_publications.publication.release_notes',
						&$this,
						&$config
					));

					$this->set('release_notes.parsed', (string) $this->get('release_notes', ''));
					$this->set('release_notes', $content);

					return $this->notes($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->notes('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('release_notes'));
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
	 * Get the content of nbtag in metadata field
	 *
	 * @return     mixed String or Integer
	 */
	public function getNbtag ($aliasmap = '')
	{
		$data = array();

		// Parse data
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->get('metadata', ''), $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = PublicationsHtml::_txtUnpee($match[2]);
			}
		}

		$value = isset($data[$aliasmap]) ? $data[$aliasmap] : NULL;

		return $value;
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
	public function parse($aliasmap = '', $field = '', $as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		if (!$this->get($field, ''))
		{
			return false;
		}

		switch ($as)
		{
			case 'parsed':
				$content = $this->get($field . '.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_publications',
						'scope'    => '',
						'pagename' => 'publications',
						'pageid'   => '',
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get($field, ''));
					if ($field == 'metadata')
					{
						$content = (string) stripslashes($this->getNbtag($aliasmap));
					}

					$this->importPlugin('content')->trigger('onContentPrepare', array(
						'com_publications.publication.' . $field,
						&$this,
						&$config
					));

					$parsed = (string) stripslashes($this->get($field, ''));

					if ($field == 'metadata')
					{
						$parsed = (string) stripslashes($this->getNbtag($aliasmap));
					}

					$this->set($field . '.parsed', $parsed);
					$this->set($field, $content);

					return $this->parse($aliasmap, $field, $as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->parse($aliasmap, $field, 'parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get($field));
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

