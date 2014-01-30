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

require_once(__DIR__ . '/abstract.php');

/**
 * Answers model for a comment
 */
class AnswersModelComment extends AnswersModelAbstract
{
	/**
	 * Table class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'Hubzero_Comment';

	/**
	 * Class scope
	 * 
	 * @var string
	 */
	protected $_scope = 'answercomment';

	/**
	 * \Hubzero\Base\ItemList
	 * 
	 * @var object
	 */
	private $_comments = null;

	/**
	 * Comment count
	 * 
	 * @var integer
	 */
	private $_comments_count = null;

	/**
	 * URL for this entry
	 * 
	 * @var string
	 */
	private $_base = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);

		$this->set('created', $this->get('added'));
		$this->set('created_by', $this->get('added_by'));
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
		if (!isset($filters['id']))
		{
			$filters['id'] = $this->get('id');
		}
		if (!isset($filters['category']))
		{
			$filters['category'] = 'answercomment';
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
				if (!($this->_comments instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($this->get('replies', null) !== null)
					{
						$results = $this->get('replies');
					}
					else
					{
						$results = $this->_tbl->getResults($filters);
					}

					if ($results)
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new AnswersModelComment($result);
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

	/**
	 * Get the contents of this entry in various formats
	 * 
	 * @param      string  $as      Format to return state in [raw, parsed]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				if ($this->get('comment_parsed'))
				{
					return $this->get('comment_parsed');
				}

				$p = Hubzero_Wiki_Parser::getInstance();

				$wikiconfig = array(
					'option'   => 'com_answers',
					'scope'    => 'question',
					'pagename' => $this->get('id'),
					'pageid'   => 0,
					'filepath' => '',
					'domain'   => ''
				);

				$this->set('comment_parsed', $p->parse(stripslashes($this->get('comment')), $wikiconfig));

				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($this->get('comment_parsed'), $shorten, array('html' => true));
					return $content;
				}

				return $this->get('comment_parsed');
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
				$content = $this->get('comment');
				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($content, $shorten);
				}
				return $content;
			break;
		}
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
			$this->_base = 'index.php?option=com_answers&task=question&id=' . $this->get('question_id');
		}
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&action=edit&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&action=delete&comment=' . $this->get('id');
			break;

			case 'reply':
				$link .= '&reply=' . $this->get('id') . '#c' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=comment&id=' . $this->get('id') . '&parent=' . $this->get('question_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			$this->setError(JText::_('Database not found.'));
			return false;
		}

		// Can't delete what doesn't exist
		if (!$this->exists()) 
		{
			return true;
		}

		// Remove comments
		foreach ($this->replies('list') as $comment)
		{
			if (!$comment->delete())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		return parent::delete();
	}
}

