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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'comment.php');

/**
 * Courses model class for a forum
 */
class WikiModelComment extends \Hubzero\Base\Model
{
	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * WikiModelIterator
	 * 
	 * @var object
	 */
	private $_comments = NULL;

	/**
	 * Comment count
	 * 
	 * @var integer
	 */
	private $_comments_count = NULL;

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new WikiTableComment($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Returns a reference to a forum model
	 *
	 * This method must be invoked as:
	 *     $offering = ForumModelCourse::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object ForumModelCourse
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
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isReported()
	{
		if ($this->get('reports', -1) > 0)
		{
			return true;
		}
		// Reports hasn't been set
		if ($this->get('reports', -1) == -1) 
		{
			if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php')) 
			{
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');
				$ra = new ReportAbuse($this->_db);
				$val = $ra->getCount(array(
					'id'       => $this->get('id'), 
					'category' => 'wiki'
				));
				$this->set('reports', $val);
				if ($this->get('reports') > 0)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     boolean
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
			//$this->_creator = JUser::getInstance($this->get('created_by'));
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile();
			}
		}
		if ($property) //JUser
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			return $this->_creator->get($property);
		}
		return $this->_creator;
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
				if ($this->get('chtml'))
				{
					return $this->get('chtml');
				}

				$p = WikiHelperParser::getInstance();

				$wikiconfig = array(
					'option'   => JRequest::getCmd('option', 'com_wiki'),
					'scope'    => JRequest::getVar('scope'),
					'pagename' => JRequest::getVar('pagename'),
					'pageid'   => $this->get('pageid'),
					'filepath' => '',
					'domain'   => JRequest::getVar('group', '')
				);

				$this->set('chtml', $p->parse(stripslashes($this->get('ctext')), $wikiconfig));

				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($this->get('chtml'), $shorten, array('html' => true));

					return $content;
				}

				return $this->get('chtml');
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($content, $shorten);
				}
				return $content;
			break;

			case 'raw':
			default:
				return $this->get('ctext');
			break;
		}
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 * 
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			$this->_base  = 'index.php?option=' . JRequest::getCmd('option', 'com_wiki') . '&scope=' . JRequest::getVar('scope') . '&pagename=' . JRequest::getVar('pagename');
		}
		$task = JRequest::getVar('cn', '') ? 'action' : 'task';

		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&' . $task . '=editcomment&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&' . $task . '=removecomment&id=' . $this->get('id');
			break;

			case 'reply':
				$link .= '&' . $task . '=addcomment&parent=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=wikicomment&id=' . $this->get('id') . '&parent=' . $this->get('pageid');
			break;

			case 'permalink':
			default:
				$link .= '&' . $task . '=comments#c' . $this->get('id');
			break;
		}

		return $link;
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
	public function replies($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['pageid']))
		{
			$filters['pageid'] = $this->get('pageid');
		}
		if (!isset($filters['parent']))
		{
			$filters['parent'] = $this->get('id');
		}
		if (!isset($filters['version']))
		{
			$filters['version'] = '';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->_comments_count) || $clear)
				{
					$this->_comments_count = 0;

					if (!$this->_comments) 
					{
						$c = $this->comments('list', $filters);
					}
					foreach ($this->_comments as $com)
					{
						$this->_comments_count++;
						if ($com->replies()) 
						{
							foreach ($com->replies() as $rep)
							{
								$this->_comments_count++;
								if ($rep->replies()) 
								{
									$this->_comments_count += $rep->replies()->total();
								}
							}
						}
					}
				}
				return $this->_comments_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_comments instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$results = $this->_tbl->getComments($filters['pageid'], $filters['parent'], $filters['version']);

					if ($results)
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new WikiModelComment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new \Hubzero\Base\ItemList($results);
				}
				return $this->_comments;
			break;
		}
	}
}

