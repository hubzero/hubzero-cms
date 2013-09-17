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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'comment.php');

/**
 * Courses model class for a forum
 */
class AnswersModelResponse extends AnswersModelAbstract
{
	/**
	 * ForumTablePost
	 * 
	 * @var object
	 */
	protected $_tbl_name = 'AnswersTableResponse';

	/**
	 * Class scope
	 * 
	 * @var string
	 */
	protected $_scope = 'answer';

	/**
	 * BlogModelComment
	 * 
	 * @var object
	 */
	private $_comment = null;

	/**
	 * BlogModelIterator
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
	 * URL to this entry
	 * 
	 * @var string
	 */
	private $_base = null;

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function replies($rtrn='list', $filters=array())
	{
		if (!isset($filters['referenceid']))
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
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count))
				{
					$this->_comments_count = 0;
					//$this->_comments_count = (int) $tbl->count($filters);
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
				if (!isset($this->_comments) || !is_a($this->_comments, 'AnswersModelIterator'))
				{
					$tbl = new Hubzero_Comment($this->_db);

					if ($this->get('replies', null) !== null)
					{
						$results = $this->get('replies');
					}
					else
					{
						$results = $tbl->getResults($filters);
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
					$this->_comments = new AnswersModelIterator($results);
				}
				return $this->_comments;
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
				if ($this->get('answer_parsed'))
				{
					return $this->get('answer_parsed');
				}

				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				//$config = JComponentHelper::getParams($option);

				$p =& Hubzero_Wiki_Parser::getInstance();

				$wikiconfig = array(
					'option'   => 'com_answers',
					'scope'    => 'question',
					'pagename' => $this->get('id'),
					'pageid'   => 0,
					'filepath' => '',
					'domain'   => ''
				);

				$this->set('answer_parsed', $p->parse(stripslashes($this->get('answer')), $wikiconfig));

				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($this->get('answer_parsed'), $shorten, 0, 0);
					if (substr($content, -7) == '&#8230;') 
					{
						$content .= '</p>';
					}
					return $content;
				}

				return $this->get('answer_parsed');
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
				$content = $this->get('answer');
				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 1);
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
	 * @return     boolean
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			$this->_base = 'index.php?option=com_answers&task=question&id=' . $this->get('qid');
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

			case 'accept':
				$link  = 'index.php?option=com_answers&task=accept&id=' . $this->get('qid') . '&rid=' . $this->get('id');
				//$link .= '&task=accept&id' . $this->get('qid') . '&rid=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=comment&id=' . $this->get('id') . '&parent=' . $this->get('qid');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Reset the vote count and log
	 *
	 * @return    boolean False if error, True on success
	 */
	public function reset()
	{
		// Can't manipulate what doesn't exist
		if (!$this->exists()) 
		{
			return true;
		}

		// Reset the vote counts
		$this->set('helpful', 0);
		$this->set('nothelpful', 0);

		if (!$this->store())
		{
			return false;
		}

		// Clear the history of "helpful" clicks
		$al = new AnswersTableLog($this->_db);
		if (!$al->deleteLog($this->get('id')))
		{
			$this->setError($al->getError());
			return false;
		}

		return true;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function accept()
	{
		$this->set('state', 1);
		if (!$this->store())
		{
			return false;
		}

		return true;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$res = parent::store($check);

		// If marked as chosen answer
		if ($res && $this->get('state') == 1)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');

			$aq = new AnswersModelQuestion($this->get('qid'));
			if ($aq->exists() && $aq->get('state') != 1)
			{
				$aq->set('state', 1);
				$aq->set('reward', 0);

				if ($aq->config('banking'))
				{
					// Calculate and distribute earned points
					$AE = new AnswersEconomy($this->_db);
					$AE->distribute_points($this->get('qid'), $aq->get('created_by'), $this->get('created_by'), 'closure');

					// Load the plugins
					JPluginHelper::importPlugin('xmessage');
					$dispatcher =& JDispatcher::getInstance();

					// Call the plugin
					if (
						!$dispatcher->trigger('onTakeAction', array(
							'answers_reply_submitted', 
							array($aq->creator('id')), 
							'com_answers', 
							$this->get('qid')
						))
					)
					{
						$this->setError(JText::_('Failed to remove alert.'));
					}
				}

				if (!$aq->store())
				{
					$this->setError($aq->getError());
					return false;
				}
			}
		}

		return $res;
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

		// Clear the history of "helpful" clicks
		$al = new AnswersTableLog($this->_db);
		if (!$al->deleteLog($this->get('id')))
		{
			$this->setError($al->getError());
			return false;
		}

		return parent::delete();
	}
}

