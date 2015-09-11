<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Models;

use Components\Answers\Tables;
use Components\Answers\Helpers;
use Hubzero\Base\ItemList;
use Hubzero\Item;
use Hubzero\Utility\String;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'log.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'response.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'comment.php');

/**
 * Answers model for a question response
 */
class Response extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Answers\\Tables\\Response';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_answers.response.answer';

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
	 * URL to this entry
	 *
	 * @var string
	 */
	private $_base = null;

	/**
	 * Get a list or count of comments
	 *
	 * @param   string  $rtrn    Data format to return
	 * @param   array   $filters Filters to apply to data fetch
	 * @param   boolean $clear   Clear cached data?
	 * @return  mixed
	 */
	public function replies($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('id');
		}
		if (!isset($filters['item_type']))
		{
			$filters['item_type'] = 'answer';
		}
		if (!isset($filters['parent']))
		{
			$filters['parent'] = 0;
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = array(self::APP_STATE_PUBLISHED, self::APP_STATE_FLAGGED);
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
				if (!($this->_comments instanceof ItemList) || $clear)
				{
					$tbl = new Item\Comment($this->_db);

					if ($this->get('replies', null) !== null)
					{
						$results = $this->get('replies');
					}
					else
					{
						$results = $tbl->find($filters);
					}

					if ($results)
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Comment($result);
							$results[$key]->set('question_id', $this->get('question_id'));
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new ItemList($results);
				}
				return $this->_comments;
			break;
		}
	}

	/**
	 * Get the contents of this entry in various formats
	 *
	 * @param   string  $as      Format to return state in [raw, parsed]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('answer.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_answers',
						'scope'    => 'question',
						'pagename' => $this->get('id'),
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => ''
					);

					$content = str_replace(array('\"', "\'"), array('"', "'"), (string) $this->get('answer', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('answer.parsed', (string) $this->get('answer', ''));
					$this->set('answer', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = html_entity_decode(strip_tags($this->content('parsed')), ENT_COMPAT, 'UTF-8');
			break;

			case 'raw':
			default:
				$content = str_replace(array('\"', "\'"), array('"', "'"), $this->get('answer'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}

		return $content;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string $type The type of link to return
	 * @return  string
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

			case 'accept':
				$link  = 'index.php?option=com_answers&task=accept&id=' . $this->get('question_id') . '&rid=' . $this->get('id');
				//$link .= '&task=accept&id' . $this->get('question_id') . '&rid=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=answer&id=' . $this->get('id') . '&parent=' . $this->get('question_id');
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
	 * @return  boolean False if error, True on success
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
		$al = new Tables\Log($this->_db);
		if (!$al->deleteLog($this->get('id')))
		{
			$this->setError($al->getError());
			return false;
		}

		return true;
	}

	/**
	 * Mark a response as "Accepted"
	 *
	 * @param   integer $question_id Question ID
	 * @return  boolean False if error, True on success
	 */
	public function accept($question_id)
	{
		/*$question = new Question($question_id);
		if (!$question->exists())
		{
			$this->setError(Lang::txt('Question not found.'));
			return false;
		}
		// Mark it at the chosen one
		$question->set('state', 1);
		if (!$question->store(true))
		{
			$this->setError($question->getError());
			return false;
		}*/

		$this->set('state', 1);
		if (!$this->store())
		{
			return false;
		}

		return true;
	}

	/**
	 * Mark a response as "Rejected"
	 *
	 * @param   integer $question_id Question ID
	 * @return  boolean False if error, True on success
	 */
	public function reject($question_id)
	{
		/*$question = new Question($question_id);
		if (!$question->exists())
		{
			$this->setError(Lang::txt('Question not found.'));
			return false;
		}
		// Mark it at the chosen one
		$question->set('state', 0);
		if (!$question->store(true))
		{
			$this->setError($question->getError());
			return false;
		}*/

		$this->set('state', 0);
		if (!$this->store())
		{
			return false;
		}

		return true;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param   boolean $check Perform data validation check?
	 * @return  boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$res = parent::store($check);

		// If marked as chosen answer
		if ($res && $this->get('state') == 1)
		{
			require_once(__DIR__ . DS . 'question.php');

			$aq = new Question($this->get('question_id'));
			if ($aq->exists() && $aq->get('state') != 1)
			{
				$aq->set('state', 1);
				//$aq->set('reward', 0);
				// This was giving out points twice for one closed question
				/*
				if ($aq->config('banking'))
				{
					// Calculate and distribute earned points
					$AE = new Economy($this->_db);
					$AE->distribute_points($this->get('question_id'), $aq->get('created_by'), $this->get('created_by'), 'closure');

					// Call the plugin
					if (
						!Event::trigger('xmessage.onTakeAction', array(
							'answers_reply_submitted',
							array($aq->creator('id')),
							'com_answers',
							$this->get('question_id')
						))
					)
					{
						$this->setError(Lang::txt('Failed to remove alert.'));
					}
				}
				*/

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
	 * @return  boolean False if error, True on success
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

		// Clear the history of "helpful" clicks
		$al = new Tables\Log($this->_db);
		if (!$al->deleteLog($this->get('id')))
		{
			$this->setError($al->getError());
			return false;
		}

		return parent::delete();
	}
}

