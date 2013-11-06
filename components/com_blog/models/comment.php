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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'comment.php');

/**
 * Courses model class for a forum
 */
class BlogModelComment extends \Hubzero\Model
{
	/**
	 * ForumTablePost
	 * 
	 * @var object
	 */
	protected $_tbl_name = 'BlogTableComment';

	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * \Hubzero\ItemList
	 * 
	 * @var object
	 */
	private $_comments = NULL;

	/**
	 * Commen count
	 * 
	 * @var integer
	 */
	private $_comments_count = NULL;

	/**
	 * Returns a reference to a blog comment model
	 *
	 * This method must be invoked as:
	 *     $comment = BlogModelComment::getInstance($id);
	 *
	 * @param      mixed $oid ID (int) or alias (string)
	 * @return     object BlogModelComment
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
			$instances[$oid] = new BlogModelComment($oid);
		}

		return $instances[$oid];
	}

	/**
	 * HAs this comment been reported
	 * 
	 * @return     boolean True if reported, False if not
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
					'category' => 'blogcomment'
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
	 * @param      string $as What format to return
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
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = Hubzero_User_Profile::getInstance($this->get('created_by'));
		}
		if ($property && $this->_creator instanceof Hubzero_User_Profile)
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
	 * Get a list or count of comments
	 * 
	 * @param      string  $rtrn    Data format to return
	 * @param      array   $filters Filters to apply to data fetch
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function replies($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('entry_id');
		}
		if (!isset($filters['parent']))
		{
			$filters['parent'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count) || $clear)
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
				if (!($this->_comments instanceof \Hubzero\ItemList) || $clear)
				{
					if ($this->get('replies', null) !== null)
					{
						$results = $this->get('replies');
					}
					else
					{
						$results = $this->_tbl->getAllComments($this->get('entry_id'), $this->get('id'));
					}

					if ($results)
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new BlogModelComment($result);
							$results[$key]->set('option', $this->get('option'));
							$results[$key]->set('scope', $this->get('scope'));
							$results[$key]->set('alias', $this->get('alias'));
							$results[$key]->set('path', $this->get('path'));
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new \Hubzero\ItemList($results);
				}
				return $this->_comments;
			break;
		}
	}

	/**
	 * Get the content of the entry
	 * 
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				if (($content = $this->get('content_parsed')))
				{
					if ($shorten)
					{
						$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 0);
						if (substr($content, -7) == '&#8230;') 
						{
							$content .= '</p>';
						}
						
					}
					return $content;
				}

				$config = array(
					'option'   => $this->get('option', JRequest::getCmd('option')),
					'scope'    => $this->get('scope', 'blog'),
					'pagename' => $this->get('alias'),
					'pageid'   => 0,
					'filepath' => $this->get('path'),
					'domain'   => ''
				);

				$content = $this->importPlugin('hubzero')->trigger('onWikiParseText', array(
					stripslashes($this->get('content')), 
					$config,  // options
					false,     // full parse
					false      // new parser?
				));

				$this->set('content_parsed', implode('', $content));

				return $this->content($as, $shorten);
			break;

			case 'clean':
				$content = strip_tags($this->content('content_parsed'));
				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 1);
				}
				return $content;
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('content'));
				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 1);
				}
				return $content;
			break;
		}
	}
}

