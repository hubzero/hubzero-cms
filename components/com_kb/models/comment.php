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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'comment.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'vote.php');

/**
 * Knowledgebase model for a comment
 */
class KbModelComment extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'KbTableComment';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_kb.comment.content';

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
	 * Base URL
	 *
	 * @var string
	 */
	private $_base = null;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_creator = null;

	/**
	 * Constructor
	 *
	 * @param      mixed $oid Integer (ID), string (alias), object or array
	 * @return     void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);

		if ($this->get('section', null) === null)
		{
			$this->set('section', JRequest::getVar('section', ''));
		}
		if ($this->get('category', null) === null)
		{
			$this->set('category', JRequest::getVar('category', ''));
		}
		if ($this->get('article', null) === null)
		{
			$this->set('article', JRequest::getVar('alias', ''));
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
	 * it returns the entire object
	 *
	 * @param      string $property Property to retrieve
	 * @param      mixed  $default  Default value if property not set
	 * @return     mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->getPicture($this->get('anonymous'));
			}
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Was the entry reported?
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
	 * Get either a count of or list of replies
	 *
	 * @param      string  $rtrn    Data type to return [count, list]
	 * @param      array   $filters Filters to apply to query
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed Returns an integer or iterator object depending upon format chosen
	 */
	public function replies($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['parent']))
		{
			$filters['parent'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count))
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
				if (!$this->_comments instanceof \Hubzero\Base\ItemList || $clear)
				{
					if ($this->get('replies', null) !== null)
					{
						$results = $this->get('replies');
					}
					else
					{
						$results = $this->_tbl->getComments($filters);
					}

					if ($results)
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new KbModelComment($result);
							$results[$key]->set('section', $this->get('section'));
							$results[$key]->set('category', $this->get('category'));
							$results[$key]->set('article', $this->get('article'));
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
	 * Get the content of the entry
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
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
						'option'   => 'com_kb',
						'scope'    => '',
						'pagename' => $this->get('article'),
						'pageid'   => $this->get('id'),
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get('content', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content.parsed', (string) $this->get('content', ''));
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
			$this->_base = 'index.php?option=com_kb';
		}
		$link  = $this->_base;
		if (!$this->get('section'))
		{
			$article = KbModelArticle::getInstance($this->get('entry_id'));

			$this->set('section', $article->get('calias'));
			$this->set('category', $article->get('ccalias'));
			$this->set('article', $article->get('alias'));
		}
		$link .= '&section=' . $this->get('section');
		$link .= ($this->get('category')) ? '&category= '. $this->get('category') : '';
		$link .= '&alias=' . $this->get('article');

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'component':
			case 'base':
				return $this->_base;
			break;

			case 'article':
				// Return as is
			break;

			case 'edit':
				$link .= '&action=edit&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&action=delete&comment=' . $this->get('id');
			break;

			case 'reply':
				$link .= '&reply=' . $this->get('id') . '#c' . $this->get('id');
			break;

			case 'vote':
				$link  = $this->_base . '&task=vote&category=comment&id=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=kb&id=' . $this->get('id') . '&parent=' . $this->get('entry_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Check if a user has voted for this entry
	 *
	 * @param      integer $user_id Optinal user ID to set as voter
	 * @return     integer
	 */
	public function voted($user_id=0)
	{
		if ($this->get('voted', -1) == -1)
		{
			$juser = ($user_id) ? JUser::getInstance($user_id) : JFactory::getUser();

			// See if a person from this IP has already voted in the last week
			$tbl = new KbTableVote($this->_db);
			$this->set(
				'voted',
				$tbl->getVote($this->get('id'), $juser->get('id'), JRequest::ip(), 'comment')
			);
		}

		return $this->get('voted', 0);
	}

	/**
	 * Vote for the entry
	 *
	 * @param      integer $vote    The vote [0, 1]
	 * @param      integer $user_id Optinal user ID to set as voter
	 * @return     boolean False if error, True on success
	 */
	public function vote($vote=0, $user_id=0)
	{
		if (!$this->exists())
		{
			$this->setError(JText::_('No record found'));
			return false;
		}

		$vote = $this->_normalizeVote($vote);

		if ($vote === 0)
		{
			$this->setError(JText::_('No vote provided'));
			return false;
		}

		$juser = ($user_id) ? JUser::getInstance($user_id) : JFactory::getUser();

		$al = new KbTableVote($this->_db);
		$al->object_id = $this->get('id');
		$al->type      = 'comment';
		$al->ip        = JRequest::ip();
		$al->user_id   = $juser->get('id');
		$al->vote      = $vote;

		// Has user voted before?
		if ($voted = $al->getVote($al->object_id, $al->user_id, $al->ip, $al->type))
		{
			$voted = $this->_normalizeVote($voted);
			// If the old vote is not the same as the new vote
			if ($voted != $vote)
			{
				// Remove old vote
				$al->deleteVote($al->object_id, $al->user_id, $al->ip, $al->type);

				// Reset the vote count
				switch ($voted)
				{
					case 'like':
						$this->set('helpful', (int) $this->get('helpful') - 1);
					break;

					case 'dislike':
						$this->set('nothelpful', (int) $this->get('nothelpful') - 1);
					break;
				}
			}
			else
			{
				return true;
			}
		}

		if ($this->get('created_by') == $juser->get('id'))
		{
			$this->setError(JText::_('COM_KB_NOTICE_CANT_VOTE_FOR_OWN'));
			return false;
		}

		switch ($vote)
		{
			case 'like':
				$this->set('helpful', (int) $this->get('helpful') + 1);
			break;

			case 'dislike':
				$this->set('nothelpful', (int) $this->get('nothelpful') + 1);
			break;
		}

		// Store the changes to vote count
		if (!$this->store())
		{
			return false;
		}

		// Store the vote log
		if (!$al->check())
		{
			$this->setError($al->getError());
			return false;
		}
		if (!$al->store())
		{
			$this->setError($al->getError());
			return false;
		}

		return true;
	}

	/**
	 * Normalize a vote to a common format
	 *
	 * @param      mixed $vote String or integer
	 * @return     string like|dislike
	 */
	private function _normalizeVote($vote)
	{
		switch (strtolower($vote))
		{
			case 1:
			case '1':
			case 'yes':
			case 'positive':
			case 'like':
				return 'like';
			break;

			case -1:
			case '-1':
			case 'no':
			case 'negative':
			case 'dislike':
				return 'dislike';
			break;

			default:
				return 0;
			break;
		}
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
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

