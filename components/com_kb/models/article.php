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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'article.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'vote.php');
require_once(__DIR__ . '/tags.php');
require_once(__DIR__ . '/comment.php');
if (!class_exists('KbModelCategory'))
{
	require_once(__DIR__ . '/category.php');
}

/**
 * Knowledgebase model for an article
 */
class KbModelArticle extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'KbTableArticle';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_kb.article.fulltxt';

	/**
	 * KbModelCategory
	 *
	 * @var object
	 */
	private $_category = null;

	/**
	 * KbModelCategory
	 *
	 * @var object
	 */
	private $_section = null;

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
	private $_base = 'index.php?option=com_kb';

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_params = null;

	/**
	 * User object
	 *
	 * @var object
	 */
	private $_creator = null;

	/**
	 * Constructor
	 *
	 * @param      mixed  $oid      Integer (ID), string (alias), object or array
	 * @param      string $category Category alias
	 * @return     void
	 */
	public function __construct($oid, $category=null)
	{
		$this->_db = \JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				if ($oid)
				{
					if ($category)
					{
						$this->_tbl->loadAlias($oid, $category);
					}
					else
					{
						$this->_tbl->load($oid);
					}
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}

		if (!$this->get('calias'))
		{
			$section = KbModelCategory::getInstance($this->get('section'));

			$this->set('calias', $section->get('alias'));

			if ($this->get('category'))
			{
				$category = KbModelCategory::getInstance($this->get('category'));

				$this->set('ccalias', $section->get('alias'));
			}
		}

		$params = new JRegistry($this->get('params'));

		$this->_params = JComponentHelper::getParams('com_kb');
		$this->_params->merge($params);
	}

	/**
	 * Returns a reference to an article model
	 *
	 * @param      mixed  $oid      Article ID or alias
	 * @param      string $category Category alias
	 * @return     object KbModelArticle
	 */
	static function &getInstance($oid=null)
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
	 * Are comments open?
	 *
	 * @return     boolean
	 */
	public function commentsOpen()
	{
		if (!$this->param('allow_comments'))
		{
			return false;
		}

		if ($this->param('close_comments') == 'never')
		{
			return true;
		}

		$d = $this->modified();
		$year  = intval(substr($d, 0, 4));
		$month = intval(substr($d, 5, 2));
		$day   = intval(substr($d, 8, 2));

		switch ($this->param('comments_close', 'never'))
		{
			case 'day':
				$dt = mktime(0, 0, 0, $month, ($day+1), $year);
			break;
			case 'week':
				$dt = mktime(0, 0, 0, $month, ($day+7), $year);
			break;
			case 'month':
				$dt = mktime(0, 0, 0, ($month+1), $day, $year);
			break;
			case '6months':
				$dt = mktime(0, 0, 0, ($month+6), $day, $year);
			break;
			case 'year':
				$dt = mktime(0, 0, 0, $month, $day, ($year+1));
			break;
			case 'never':
			default:
				$dt = mktime(0, 0, 0, $month, $day, $year);
			break;
		}

		$pdt = strftime('Y', $dt) . '-' . strftime('m', $dt) . '-' . strftime('d', $dt) . ' 00:00:00';
		$today = JFactory::getDate()->toSql();

		if ($this->param('close_comments') != 'now' && $today < $pdt)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		return $this->_datetime($as, 'created');
	}

	/**
	 * Return a formatted timestamp for modified date
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function modified($as='')
	{
		if (!$this->get('modified') || $this->get('modified') == $this->_db->getNullDate())
		{
			$this->set('modified', $this->get('created'));
		}
		return $this->_datetime($as, 'modified');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	private function _datetime($as='', $key='created')
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
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Get a list of responses
	 *
	 * @param      string  $rtrn    Data type to return [count, list]
	 * @param      array   $filters Filters to apply to query
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed   Returns an integer or array depending upon format chosen
	 */
	public function comments($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new KbTableComment($this->_db);

		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state']    = array(self::APP_STATE_PUBLISHED, self::APP_STATE_FLAGGED);
		}

		$filters['sort']     = 'created';
		$filters['sort_Dir'] = 'DESC';

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || $clear)
				{
					$total = 0;

					if (!($c = $this->get('comments')))
					{
						$c = $this->comments('list', $filters);
					}
					foreach ($c as $com)
					{
						$total++;
						if ($com->replies())
						{
							foreach ($com->replies() as $rep)
							{
								$total++;
								if ($rep->replies())
								{
									$total += $rep->replies()->total();
								}
							}
						}
					}

					$this->_comments_count = $total;
				}
				return $this->_comments_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_comments instanceof \Hubzero\Base\ItemList || $clear)
				{
					if ($results = $tbl->getAllComments($filters['entry_id']))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new KbModelComment($result);
							$results[$key]->set('section', $this->get('calias'));
							$results[$key]->set('category', $this->get('ccalias'));
							$results[$key]->set('article', $this->get('alias'));
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
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param      string  $as    Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param      integer $admin Include amdin tags? (defaults to no)
	 * @return     mixed
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

		$cloud = new KbModelTags($this->get('id'));

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @return     boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new KbModelTags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
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
		$link  = $this->_base;
		if (!$this->get('calias'))
		{
			$this->set('calias', $this->section()->get('alias'));
		}
		$link .= '&section=' . $this->get('calias');
		if (!$this->get('ccalias') && $this->get('category'))
		{
			$this->set('ccalias', $this->category()->get('alias'));
		}
		$link .= ($this->get('ccalias')) ? '&category= '. $this->get('ccalias') : '';
		$link .= ($this->get('alias'))   ? '&alias=' . $this->get('alias')      : '&alias=' . $this->get('id');

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'component':
			case 'base':
				return $this->_base;
			break;

			case 'vote':
				$link  = $this->_base . '&task=vote&category=entry&id=' . $this->get('id');
			break;

			case 'edit':
				$link .= '&task=edit';
			break;

			case 'delete':
				$link .= '&task=delete';
			break;

			case 'comments':
				$link .= '#comments';
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=kb&id=' . $this->get('id');
			break;

			case 'feed':
				$link .= '/comments.rss';

				$feed = JRoute::_($link);
				if (substr($feed, 0, 4) != 'http')
				{
					$jconfig = JFactory::getConfig();
					$live_site = rtrim(JURI::base(), '/');

					$feed = $live_site . '/' . ltrim($feed, '/');
				}
				$link = str_replace('https://', 'http://', $feed);
			break;

			case 'permalink':
			default:

			break;
		}

		return $link;
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
	 * @return     mixed   String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('fulltxt.parsed', null);

				if ($content == null)
				{
					$config = array();

					$content = (string) stripslashes($this->get('fulltxt', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('fulltxt.parsed', (string) $this->get('fulltxt', ''));
					$this->set('fulltxt', $content);

					// Wackadoodle way of running content parses on the article
					$article = new stdClass;
					$article->id = $this->get('id');
					$article->text = $this->get('fulltxt.parsed');

					$this->trigger('onContentPrepare', array(
						'com_content.article',
						&$article,
						&$this->_params
					));
					$this->set('fulltxt.parsed', $article->text);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('fulltxt'));
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
				$tbl->getVote($this->get('id'), $juser->get('id'), JRequest::ip(), 'entry')
			);
		}

		return $this->get('voted', 0);
	}

	/**
	 * Vote for the entry
	 *
	 * @param      integer $vote    The vote [-1, 1, like, dislike, yes, no, positive, negative]
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
		$al->type      = 'entry';
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
	 * @return     mixed like|dislike
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
		foreach ($this->comments('list') as $comment)
		{
			if (!$comment->delete())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		// Remove vote logs
		$tbl = new KbTableVote($this->_db);
		if (!$tbl->deleteVote($this->get('id'), null, null, 'entry'))
		{
			$this->setError($tbl->getError());
			return false;
		}

		// Attempt to delete the record
		return parent::delete();
	}

	/**
	 * Get a param value
	 *
	 * @param      string $key     Property to return
	 * @param      mixed  $default Value to return if key is not found
	 * @return     mixed
	 */
	public function param($key='', $default=null)
	{
		if ($key)
		{
			return $this->_params->get((string) $key, $default);
		}
		return $this->_params;
	}

	/**
	 * Get parent category
	 *
	 * @return     object KbModelCategory
	 */
	public function category()
	{
		if (!($this->_category instanceof KbModelCategory))
		{
			$this->_category = KbModelCategory::getInstance($this->get('category'));
		}
		return $this->_category;
	}

	/**
	 * Get parent section
	 *
	 * @return     object KbModelCategory
	 */
	public function section()
	{
		if (!($this->_section instanceof KbModelCategory))
		{
			$this->_section = KbModelCategory::getInstance($this->get('section'));
		}
		return $this->_section;
	}
}

