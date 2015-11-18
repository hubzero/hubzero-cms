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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Config\Registry;
use stdClass;
use Request;
use Route;
use Lang;
use Date;
use User;

require_once(__DIR__ . DS . 'vote.php');
require_once(__DIR__ . DS . 'comment.php');
require_once(__DIR__ . DS . 'tags.php');

/**
 * Knowledgebase model for an article
 */
class Article extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'kb';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'title';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'    => 'notempty',
		'category' => 'positive|nonzero',
		'fulltxt'  => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'alias'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 **/
	protected $parsed = array(
		'fulltxt'
	);

	/**
	 * Base URL
	 *
	 * @var  string
	 */
	private $_base = 'index.php?option=com_kb';

	/**
	 * Runs extra setup code when creating a new model
	 *
	 * @return  void
	 */
	public function setup()
	{
		$params = new Registry($this->get('params'));

		$this->params = Component::params('com_kb');
		$this->params->merge($params);
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Are comments open?
	 *
	 * @return  boolean
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
		$today = Date::toSql();

		if ($this->param('close_comments') != 'now' && $today < $pdt)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		return $this->_datetime($as, 'created');
	}

	/**
	 * Defines a belongs to one relationship between article and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Return a formatted timestamp for modified date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function modified($as='')
	{
		if (!$this->get('modified') || $this->get('modified') == '0000-00-00 00:00:00')
		{
			$this->set('modified', $this->get('created'));
		}
		return $this->_datetime($as, 'modified');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	private function _datetime($as='', $key='created')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Get parent category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->belongsToOne('Category', 'category');
	}

	/**
	 * Get a list of comments
	 *
	 * @return  object
	 */
	public function comments()
	{
		return $this->oneToMany('Comment', 'entry_id');
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param   string   $as     Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param   integer  $admin  Include amdin tags? (defaults to no)
	 * @return  mixed
	 */
	public function tags($as='cloud', $admin=0)
	{
		if (!$this->get('id'))
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

		$cloud = new Tags($this->get('id'));

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags     Comma-separated list of tags to apply
	 * @param   integer  $user_id  Tagger ID
	 * @param   integer  $admin    Include amdin tags? (defaults to no)
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		$link  = $this->_base;
		if (!$this->get('calias'))
		{
			$category = Category::oneOrNew($this->get('category'));
			$this->set('calias', $category->get('path'));
		}
		$link .= '&section=' . $this->get('calias');
		$link .= ($this->get('alias'))   ? '&alias=' . $this->get('alias')      : '&alias=' . $this->get('id');

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'component':
			case 'base':
				return $this->_base;
			break;

			case 'vote':
				$link  = $this->_base . '&task=vote&category=article&id=' . $this->get('id');
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

				$feed = Route::url($link);
				if (substr($feed, 0, 4) != 'http')
				{
					$live_site = rtrim(Request::base(), '/');

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
	 * Get a list of votes
	 *
	 * @return  object
	 */
	public function votes()
	{
		return $this->oneShiftsToMany('Vote', 'object_id', 'type');
	}

	/**
	 * Check if a user has voted for this entry
	 *
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @param   string   $ip       IP Address
	 * @return  integer
	 */
	public function voted($user_id = 0, $ip = null)
	{
		if ($this->get('voted', -1) == -1)
		{
			$user = ($user_id) ? User::getInstance($user_id) : User::getRoot();
			$ip   = ($ip ?: Request::ip());

			// See if a person from this IP has already voted in the last week
			$previous = Vote::find($this->get('id'), $user->get('id'), $ip, 'article');

			$this->set('voted', $previous->get('vote'));
		}

		return $this->get('voted', 0);
	}

	/**
	 * Vote for the entry
	 *
	 * @param   integer  $vote     The vote [-1, 1, like, dislike, yes, no, positive, negative]
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @return  boolean  False if error, True on success
	 */
	public function vote($vote = 0, $user_id = 0)
	{
		if ($this->isNew())
		{
			$this->setError(Lang::txt('No record found'));
			return false;
		}

		$al = new Vote();

		$vote = $al->automaticVote(array('vote' => $vote));

		if ($vote === 0)
		{
			$this->setError(Lang::txt('No vote provided'));
			return false;
		}

		$user = ($user_id) ? User::getInstance($user_id) : User::getRoot();

		$al->set('object_id', $this->get('id'));
		$al->set('type', 'article');
		$al->set('ip', Request::ip());
		$al->set('user_id', $user->get('id'));
		$al->set('vote', $vote);

		// Has user voted before?
		$previous = $al->find($al->get('object_id'), $al->get('user_id'), $al->get('ip'), $al->get('type'));
		if ($previous->get('vote'))
		{
			$voted = $al->automaticVote(array('vote' => $previous->get('vote')));

			// If the old vote is not the same as the new vote
			if ($voted != $vote)
			{
				// Remove old vote
				$previous->destroy();

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

		if ($this->get('created_by') == $user->get('id'))
		{
			$this->setError(Lang::txt('COM_KB_NOTICE_CANT_VOTE_FOR_OWN'));
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
		if (!$this->save())
		{
			return false;
		}

		// Store the vote log
		if (!$al->save())
		{
			$this->setError($al->getError());
			return false;
		}

		return true;
	}

	/**
	 * Saves the current model to the database
	 *
	 * @return  bool
	 */
	public function save()
	{
		$params = $this->get('params');
		if (is_object($params))
		{
			$this->set('params', $params->toString());
		}

		$result = parent::save();

		$this->set('params', $params);

		return $result;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->comments() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		// Remove vote logs
		foreach ($this->votes() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->setError($vote->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Get a param value
	 *
	 * @param   string  $key      Property to return
	 * @param   mixed   $default  Value to return if key is not found
	 * @return  mixed
	 */
	public function param($key='', $default=null)
	{
		if (!is_object($this->get('params')))
		{
			$params = new Registry($this->get('params'));

			$p = Component::params('com_kb');
			$p->merge($params);

			$this->set('params', $p);
		}

		if ($key)
		{
			return $this->get('params')->get((string) $key, $default);
		}
		return $this->get('params');
	}
}

