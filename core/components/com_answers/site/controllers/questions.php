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

namespace Components\Answers\Site\Controllers;

use Components\Answers\Models\Question;
use Components\Answers\Models\Response;
use Components\Answers\Models\Comment;
use Components\Answers\Tables;
use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Hubzero\Utility\Sanitize;
use Hubzero\Bank\Teller;
use Hubzero\Bank\Transaction;
use Exception;
use Document;
use Pathway;
use Request;
use Config;
use Event;
use Route;
use Lang;
use Date;
use User;

/**
 * Answers controller class for questions
 */
class Questions extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->config->set('banking', \Component::params('com_members')->get('bankAccounts'));

		$this->registerTask('__default', 'search');
		$this->registerTask('display', 'search');
		$this->registerTask('latest', 'latest.rss');

		parent::execute();
	}

	/**
	 * Build the document pathway (breadcrumbs)
	 *
	 * @param   object  $question
	 * @return  void
	 */
	protected function _buildPathway($question=null)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && in_array($this->_task, array('new', 'myquestions', 'search')))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
		if (is_object($question) && $question->get('subject'))
		{
			Pathway::append(
				String::truncate($question->subject('clean'), 50),
				$question->link()
			);
		}
	}

	/**
	 * Build the document title
	 *
	 * @param   object  $question
	 * @return  void
	 */
	protected function _buildTitle($question=null)
	{
		$this->view->title = Lang::txt(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'view')
		{
			$this->view->title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		if (is_object($question) && $question->get('subject'))
		{
			$this->view->title .= ': ' . String::truncate($question->subject('clean'), 50);
		}

		Document::setTitle($this->view->title);
	}

	/**
	 * Redirect to login form
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false, true), 'server');

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn), false),
			($this->getError() ? $this->getError() : null),
			($this->getError() ? 'warning' : 'success')
		);
	}

	/**
	 * Save a reply
	 *
	 * @return  void
	 */
	public function savereplyTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_LOGIN_TO_COMMENT'));
			$this->loginTask();
			return;
		}

		// Incoming
		$questionID = Request::getVar('rid');
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		// clean input
		array_walk($comment, function(&$field, $key)
		{
			$field = \Hubzero\Utility\Sanitize::clean($field);
		});

		if (!$comment['item_id'])
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_QUESTION_ID_NOT_FOUND'), 500);
		}

		if ($comment['item_type'])
		{
			$row = new Comment(0);
			if (!$row->bind($comment))
			{
				throw new Exception($row->getError(), 500);
			}

			// Perform some text cleaning, etc.
			$row->set('anonymous', ($row->get('anonymous') ? 1 : 0));
			$row->set('created', Date::toSql());
			$row->set('state', 0);
			$row->set('created_by', User::get('id'));

			// Save the data
			if (!$row->store(true))
			{
				throw new Exception($row->getError(), 500);
			}
		}

		//For email
		// Load question
		$question = new Question($questionID);

		// Get users who need to be notified on updates
		$apu = $this->config->get('notify_users', '');
		$apu = explode(',', $apu);
		$apu = array_map('trim', $apu);

		$receivers = array();

		// Build the "from" info
		$from = array(
			'email'     => Config::get('mailfrom'),
			'name'      => Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS'),
			'multipart' => md5(date('U'))
		);

		// Build the message subject
		$subject = Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS') . ', ' . Lang::txt('COM_ANSWERS_QUESTION') . ' #' . $question->get('id') . ' ' . Lang::txt('COM_ANSWERS_RESPONSE');
		$message = array();

		// Plain text message
		$eview = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'response_plaintext'
		));
		$eview->option   = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->question = $question;
		$eview->row      = $row;
		$eview->boundary = $from['multipart'];

		$message['plaintext'] = $eview->loadTemplate();
		$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

		// HTML message
		$eview->setLayout('response_html');

		$message['multipart'] = $eview->loadTemplate();
		$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

		// ---

		$authorid = $question->creator('id');

		$apu = $this->config->get('notify_users', '');
		$apu = explode(',', $apu);
		$apu = array_map('trim', $apu);

		$receivers = array();

		if (!empty($apu))
		{
			foreach ($apu as $u)
			{
				$user = User::getInstance($u);
				if ($user)
				{
					$receivers[] = $user->get('id');
				}
			}
			$receivers = array_unique($receivers);
		}

		// send the response, unless the author is also in the admin list.
		if (!in_array($authorid, $receivers) && $question->get('email'))
		{
			if (!Event::trigger('xmessage.onSendMessage', array('answers_reply_comment', $subject, $message, $from, array($authorid), $this->_option)))
			{
				$this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		// admin emails
		if (!empty($receivers))
		{
			if (!Event::trigger('xmessage.onSendMessage', array('new_answer_admin', $subject, $message, $from, $receivers, $this->_option)))
			{
				$this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=question&id=' . Request::getInt('rid', 0))
		);
	}

	/**
	 * Reply to an answer
	 *
	 * @return  void
	 */
	public function replyTask()
	{
		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_LOGIN_TO_COMMENT'));
			$this->loginTask();
			return;
		}

		// Retrieve a review or comment ID and category
		$id    = Request::getInt('id', 0);
		$refid = Request::getInt('refid', 0);
		$cat   = Request::getVar('category', '');

		// Do we have an ID?
		if (!$id)
		{
			// Cannot proceed
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Do we have a category?
		if (!$cat)
		{
			// Cannot proceed
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=question&id=' . $id)
			);
			return;
		}

		// Store the comment object in our registry
		$this->category = $cat;
		$this->referenceid = $refid;
		$this->qid = $id;
		$this->questionTask();
	}

	/**
	 * Rate an item
	 *
	 * @return     void
	 */
	public function rateitemTask()
	{
		$no_html = Request::getInt('no_html', 0);

		// Is the user logged in?
		if (User::isGuest())
		{
			if (!$no_html)
			{
				$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN_TO_VOTE'));
				$this->loginTask();
			}
			return;
		}

		// Incoming
		$id      = Request::getInt('refid', 0);
		$cat     = Request::getVar('category', '');
		$vote    = Request::getVar('vote', '');
		$ip      = Request::ip();

		// Check for reference ID
		if (!$id)
		{
			// cannot proceed
			if (!$no_html)
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option),
					Lang::txt('No ID provided.'),
					'error'
				);
			}
			return;
		}

		// load answer
		$row = new Response($id);

		$qid = $row->get('question_id');

		// Can't vote for your own comment
		if ($row->get('created_by') == User::get('username'))
		{
			if (!$no_html)
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&task=question&id=' . $qid),
					Lang::txt('Cannot vote for your own entries.'),
					'warning'
				);
			}
			return;
		}

		// Can't vote for your own comment
		if (!$vote)
		{
			if (!$no_html)
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&task=question&id=' . $qid),
					Lang::txt('No vote provided.'),
					'warning'
				);
			}
			return;
		}

		// Get vote log
		$al = new Tables\Log($this->database);
		$al->loadByIp($id, $ip);

		if (!$al->id)
		{
			// new vote;
			// record if it was helpful or not
			switch ($vote)
			{
				case 'yes':
				case 'like':
				case 'up':
				case 1:
					$row->set('helpful', $row->get('helpful') + 1);
				break;

				case 'no':
				case 'dislike':
				case 'down':
				case -1:
					$row->set('nothelpful', $row->get('nothelpful') + 1);
				break;
			}
		}
		else if ($al->helpful != $vote)
		{
			// changing vote;
			// Adjust values to reflect vote change
			switch ($vote)
			{
				case 'yes':
				case 'like':
				case 'up':
				case 1:
					$row->set('helpful', $row->get('helpful') + 1);
					$row->set('nothelpful', $row->get('nothelpful') - 1);
				break;

				case 'no':
				case 'dislike':
				case 'down':
				case -1:
					$row->set('helpful', $row->get('helpful') - 1);
					$row->set('nothelpful', $row->get('nothelpful') + 1);
				break;
			}
		}
		else
		{
			// no vote change;
		}

		if (!$row->store(false))
		{
			$this->setError($row->getError());
			return;
		}

		// Record user's vote (old way)
		$al = new Tables\Log($this->database);
		$al->response_id = $row->get('id');
		$al->ip      = $ip;
		$al->helpful = $vote;
		if (!$al->check())
		{
			echo $al->getError();
			$this->setError($al->getError());
			return;
		}
		if (!$al->store())
		{
			echo $al->getError();
			$this->setError($al->getError());
			return;
		}

		// Record user's vote (new way)
		if ($cat)
		{
			require_once(dirname(dirname(__DIR__)) . DS  . 'tables' . DS . 'vote.php');

			$v = new Tables\Vote($this->database);
			$v->referenceid = $row->get('id');
			$v->category    = $cat;
			$v->voter       = User::get('id');
			$v->ip          = $ip;
			$v->voted       = Date::toSql();
			$v->helpful     = $vote;
			if (!$v->check())
			{
				echo $v->getError();
				$this->setError($v->getError());
				return;
			}
			if (!$v->store())
			{
				echo $v->getError();
				$this->setError($v->getError());
				return;
			}
		}

		// update display
		if ($no_html)
		{
			$row->set('vote', $vote);

			$this->view->option = $this->_option;
			$this->view->item   = $row;

			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->display();
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=question&id=' . $qid)
			);
		}
	}

	/**
	 * Search entries
	 *
	 * @return     void
	 */
	public function searchTask()
	{
		$this->view->config = $this->config;
		$this->view->task   = $this->_task;

		// Incoming
		$this->view->filters = array(
			'limit'    => Request::getInt('limit', Config::get('list_limit')),
			'start'    => Request::getInt('limitstart', 0),
			'tag'      => Request::getVar('tags', ''),
			'q'        => Request::getVar('q', ''),
			'filterby' => Request::getWord('filterby', ''),
			'sortby'   => Request::getWord('sortby', 'date'),
			'sort_Dir' => Request::getWord('sortdir', 'DESC'),
			'area'     => Request::getVar('area', '')
		);

		// Validate inputs
		$this->view->filters['tag'] = ($this->view->filters['tag'] ? $this->view->filters['tag'] : Request::getVar('tag', ''));

		if ($this->view->filters['filterby']
		 && !in_array($this->view->filters['filterby'], array('open', 'closed')))
		{
			$this->view->filters['filterby'] = '';
		}

		if (!in_array($this->view->filters['sortby'], array('date', 'votes', 'rewards')))
		{
			$this->view->filters['sortby'] = 'date';
		}

		if ($this->view->filters['area']
		 && !in_array($this->view->filters['area'], array('mine', 'assigned', 'interest')))
		{
			$this->view->filters['area'] = '';
		}

		// Get questions of interest
		if ($this->view->filters['area'] == 'interest')
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'tags.php');

			// Get tags of interest
			$mt = new \Components\Members\Models\Tags(User::get('id'));
			$mytags  = $mt->render('string');

			$this->view->filters['tag']  = ($this->view->filters['tag']) ? $this->view->filters['tag'] : $mytags;
			$this->view->filters['mine'] = 0;
		}

		// Get assigned questions
		if ($this->view->filters['area'] == 'assigned')
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');

			// What tools did this user contribute?
			$TA = new \Components\Tools\Tables\Author($this->database);
			$tools = $TA->getToolContributions(User::get('id'));
			$mytooltags = array();
			if ($tools)
			{
				foreach ($tools as $tool)
				{
					$mytooltags[] = 'tool' . $tool->toolname;
				}
			}

			$this->view->filters['tag'] = ($this->view->filters['tag']) ? $this->view->filters['tag'] : implode(',', $mytooltags);

			$this->view->filters['mine'] = 0;
		}

		if ($this->view->filters['area'] == 'mine')
		{
			$this->view->filters['mine'] = 1;
		}

		// Instantiate a Questions object
		$aq = new Tables\Question($this->database);

		if (($this->view->filters['area'] == 'interest' || $this->view->filters['area'] == 'assigned') && !$this->view->filters['tag'])
		{
			// Get a record count
			$this->view->total = 0;

			// Get records
			$this->view->results = array();
		}
		else
		{
			// Get a record count
			$this->view->total = $aq->getCount($this->view->filters);

			// Get records
			$this->view->results = $aq->getResults($this->view->filters);
		}

		// Did we get any results?
		if (count($this->view->results) > 0)
		{
			// Do some processing on the results
			foreach ($this->view->results as $i => $result)
			{
				$this->view->results[$i] = new Question($result);
			}
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('search')
			->display();
	}

	/**
	 * Display a question
	 *
	 * @return     void
	 */
	public function questionTask()
	{
		// Incoming
		$this->view->id   = Request::getInt('id', 0);
		$this->view->note = $this->_note(Request::getInt('note', 0));

		$this->view->question = Question::getInstance($this->view->id);

		// Ensure we have an ID to work with
		if (!$this->view->id)
		{
			throw new Exception(Lang::txt('COM_ANSWERS_ERROR_QUESTION_ID_NOT_FOUND'), 500);
		}

		// Check if person voted
		$this->view->voted = 0;
		if (!User::isGuest())
		{
			$this->view->voted = $this->view->question->voted();
		}

		// Set the page title
		$this->_buildTitle($this->view->question);

		// Set the pathway
		$this->_buildPathway($this->view->question);

		// Output HTML
		$this->view->config = $this->config;

		if (!isset($this->view->responding))
		{
			$this->view->responding = 0;
		}

		$this->view->notifications = array();
		foreach ($this->getErrors() as $error)
		{
			$this->view->notifications[] = array(
				'type'    => 'error',
				'message' => $error
			);
		}

		$this->view
			->setLayout('question')
			->display();
	}

	/**
	 * Show a form for answering a question
	 *
	 * @return  void
	 */
	public function answerTask()
	{
		$this->view->responding = 1;
		$this->questionTask();
	}

	/**
	 * Show a confirmation form for deleting a question
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		$this->view->responding = 4;
		$this->questionTask();
	}

	/**
	 * Create a new question
	 *
	 * @return  void
	 */
	public function newTask($question = null)
	{
		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			$this->loginTask();
			return;
		}

		if (!User::authorise('core.create', $this->_option)
		 && !User::authorise('core.manage', $this->_option))
		{
			throw new Exception(Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Instantiate a new view
		$this->view->config = $this->config;
		$this->view->task   = $this->_task;

		// Incoming
		$this->view->tag = Request::getVar('tag', '');

		if (is_object($question))
		{
			$this->view->question = $question;
		}
		else
		{
			$this->view->question = new Question(0);
		}

		// Is banking turned on?
		$this->view->funds = 0;
		if ($this->config->get('banking'))
		{
			$BTL = new Teller($this->database, User::get('id'));
			$funds = $BTL->summary() - $BTL->credit_summary();
			$this->view->funds = ($funds > 0) ? $funds : 0;
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->notifications = array();
		foreach ($this->getErrors() as $error)
		{
			$this->view->notifications[] = array(
				'type'    => 'error',
				'message' => $error
			);
		}
		$this->view
			->setLayout('new')
			->display();
	}

	/**
	 * Save a question
	 *
	 * @return     void
	 */
	public function saveqTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			$this->loginTask();
			return;
		}

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.manage', $this->_option))
		{
			throw new Exception(Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$tags   = Request::getVar('tags', '');
		if (!isset($fields['reward']))
		{
			$fields['reward'] = 0;
		}

		// If offering a reward, do some checks
		if ($fields['reward'])
		{
			// Is it an actual number?
			if (!is_numeric($fields['reward']))
			{
				throw new Exception(Lang::txt('COM_ANSWERS_REWARD_MUST_BE_NUMERIC'), 500);
			}
			// Are they offering more than they can afford?
			if ($fields['reward'] > $fields['funds'])
			{
				throw new Exception(Lang::txt('COM_ANSWERS_INSUFFICIENT_FUNDS'), 500);
			}
		}

		// clean input
		array_walk($fields, function(&$field, $key)
		{
			$field = \Hubzero\Utility\Sanitize::clean($field);
		});

		// Initiate class and bind posted items to database fields
		$row = new Question($fields['id']);
		if (!$row->bind($fields))
		{
			throw new Exception($row->getError(), 500);
		}

		if ($fields['reward'] && $this->config->get('banking'))
		{
			$row->set('reward', 1);
		}

		// Store new content
		if (!Request::checkHoneypot())
		{
			$this->setError(Lang::txt('JLIB_APPLICATION_ERROR_INVALID_CONTENT'));
			$this->newTask($row);
			return;
		}

		// Ensure the user added a tag
		if (!$tags)
		{
			$this->setError(Lang::txt('COM_ANSWERS_QUESTION_MUST_HAVE_TAG'));
			$this->newTask($row);
			return;
		}

		// We need to temporarily set this so the store() method
		// has access to the tags string to be able to run it
		// through spam checkers and validation.
		$row->set('tags', $tags);

		// Store new content
		if (!$row->store(true))
		{
			Request::setVar('tag', $tags);

			$this->setError($row->getError());
			$this->newTask($row);
			return;
		}

		// Hold the reward for this question if we're banking
		if ($fields['reward'] && $this->config->get('banking'))
		{
			$BTL = new Teller($this->database, User::get('id'));
			$BTL->hold(
				$fields['reward'],
				Lang::txt('COM_ANSWERS_HOLD_REWARD_FOR_BEST_ANSWER'),
				'answers',
				$row->get('id')
			);
		}

		// Add the tags
		$row->tag($tags);

		// Get users who need to be notified on every question
		$apu = $this->config->get('notify_users', '');
		$apu = explode(',', $apu);
		$apu = array_map('trim',$apu);

		$receivers = array();

		// Get tool contributors if question is about a tool
		if ($tags)
		{
			$tags = preg_split("/[,;]/", $tags);
			if (count($tags) > 0)
			{
				require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');
				require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');

				$TA = new \Components\Tools\Tables\Author($this->database);
				$objV = new \Components\Tools\Tables\Version($this->database);

				foreach ($tags as $tag)
				{
					if ($tag == '')
					{
						continue;
					}
					if (preg_match('/tool:/', $tag))
					{
						$toolname = preg_replace('/tool:/', '', $tag);
						if (trim($toolname))
						{
							$rev = $objV->getCurrentVersionProperty ($toolname, 'revision');
							$authors = $TA->getToolAuthors('', 0, $toolname, $rev);
							if (count($authors) > 0)
							{
								foreach ($authors as $author)
								{
									$receivers[] = $author->uidNumber;
								}
							}
						}
					}
				}
			}
		}

		if (!empty($apu))
		{
			foreach ($apu as $u)
			{
				$user = User::getInstance($u);
				if ($user)
				{
					$receivers[] = $user->get('id');
				}
			}
		}
		$receivers = array_unique($receivers);

		// Send the message
		if (!empty($receivers))
		{
			// Send a message about the new question to authorized users (specified admins or related content authors)
			$from = array(
				'email'     => Config::get('mailfrom'),
				'name'      => Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS'),
				'multipart' => md5(date('U'))
			);

			// Build the message subject
			$subject = Lang::txt('COM_ANSWERS_ANSWERS') . ', ' . Lang::txt('new question about content you author or manage');

			$message = array();

			// Plain text message
			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'question_plaintext'
			));
			$eview->option   = $this->_option;
			$eview->sitename = Config::get('sitename');
			$eview->question = $row;
			$eview->id       = $row->get('id', 0);
			$eview->boundary = $from['multipart'];

			$message['plaintext'] = $eview->loadTemplate(false);
			$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

			// HTML message
			$eview->setLayout('question_html');

			$message['multipart'] = $eview->loadTemplate();
			$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

			if (!Event::trigger('xmessage.onSendMessage', array('new_question_admin', $subject, $message, $from, $receivers, $this->_option)))
			{
				$this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		// Redirect to the question
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=question&id=' . $row->get('id')),
			Lang::txt('COM_ANSWERS_NOTICE_QUESTION_POSTED_THANKS')
		);
	}

	/**
	 * Delete a question
	 *
	 * @return     void
	 */
	public function deleteqTask()
	{
		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			$this->loginTask();
			return;
		}

		if (!User::authorise('core.delete', $this->_option)
		 && !User::authorise('core.manage', $this->_option))
		{
			throw new Exception(Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Incoming
		$id = Request::getInt('qid', 0);
		$ip = (!User::isGuest()) ? Request::ip() : '';

		$reward = 0;
		if ($this->config->get('banking'))
		{
			$BT = new Transaction($this->database);
			$reward = $BT->getAmount('answers', 'hold', $id);
		}
		$email = 0;

		$question = new Question($id);

		// Check if user is authorized to delete
		if ($question->get('created_by') != User::get('id'))
		{
			App::redirect(
				Route::url($question->link() . '&note=3')
			);
			return;
		}

		if ($question->get('state') == 1)
		{
			App::redirect(
				Route::url($question->link() . '&note=2')
			);
			return;
		}

		$question->set('state', 2);  // Deleted by user
		$question->set('reward', 0);

		// Store new content
		if (!$question->store(false))
		{
			throw new Exception($question->getError(), 500);
		}

		if ($reward && $this->config->get('banking'))
		{
			// Get all the answers for this question
			if ($question->comments('list', array('filterby' => 'all')))
			{
				$users = array();
				foreach ($responses as $r)
				{
					$users[] = $r->creator('id');
				}

				// Build the "from" info
				$from = array(
					'email'     => Config::get('mailfrom'),
					'name'      => Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS'),
					'multipart' => md5(date('U'))
				);

				// Build the message subject
				$subject = Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS') . ', ' . Lang::txt('COM_ANSWERS_QUESTION') . ' #' . $id . ' ' . Lang::txt('COM_ANSWERS_WAS_REMOVED');

				$message = array();

				// Plain text message
				$eview = new \Hubzero\Mail\View(array(
					'name'   => 'emails',
					'layout' => 'removed_plaintext'
				));
				$eview->option   = $this->_option;
				$eview->sitename = Config::get('sitename');
				$eview->question = $question;
				$eview->id       = $question->get('id');
				$eview->boundary = $from['multipart'];

				$message['plaintext'] = $eview->loadTemplate(false);
				$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

				// HTML message
				$eview->setLayout('removed_html');

				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				// Send the message
				if (!Event::trigger('xmessage.onSendMessage', array('answers_question_deleted', $subject, $message, $from, $users, $this->_option)))
				{
					$this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
				}
			}

			// Remove hold
			$BT->deleteRecords('answers', 'hold', $id);

			// Make credit adjustment
			$BTL_Q = new Teller($this->database, User::get('id'));
			$adjusted = $BTL_Q->credit_summary() - $reward;
			$BTL_Q->credit_adjustment($adjusted);
		}

		// Redirect to the question
		App::redirect(
			Route::url('index.php?option=' . $this->_option)
		);
	}

	/**
	 * Save an answer (reply to question)
	 *
	 * @return     void
	 */
	public function saveaTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			$this->loginTask();
			return;
		}

		// Incoming
		$response = Request::getVar('response', array(), 'post', 'none', 2);

		// clean input
		array_walk($response, function(&$field, $key)
		{
			$field = \Hubzero\Utility\Sanitize::clean($field);
		});

		// Initiate class and bind posted items to database fields
		$row = new Response($response['id']);
		if (!$row->bind($response))
		{
			throw new Exception($row->getError(), 500);
		}

		// Store new content
		if (!$row->store(true))
		{
			throw new Exception($row->getError(), 500);
		}

		// Load the question
		$question = new Question($row->get('question_id'));

		// ---

		// Build the "from" info
		$from = array(
			'email'     => Config::get('mailfrom'),
			'name'      => Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS'),
			'multipart' => md5(date('U'))
		);

		// Build the message subject
		$subject = Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS') . ', ' . Lang::txt('COM_ANSWERS_QUESTION') . ' #' . $question->get('id') . ' ' . Lang::txt('COM_ANSWERS_RESPONSE');

		$message = array();

		// Plain text message
		$eview = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'response_plaintext'
		));
		$eview->option   = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->question = $question;
		$eview->row      = $row;
		$eview->id       = $response['question_id'];
		$eview->boundary = $from['multipart'];

		$message['plaintext'] = $eview->loadTemplate(false);
		$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

		// HTML message
		$eview->setLayout('response_html');

		$message['multipart'] = $eview->loadTemplate();
		$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

		// ---

		$authorid = $question->creator('id');

		$apu = $this->config->get('notify_users', '');
		$apu = explode(',', $apu);
		$apu = array_map('trim', $apu);

		$receivers = array();

		if (!empty($apu))
		{
			foreach ($apu as $u)
			{
				$user = User::getInstance($u);
				if ($user)
				{
					$receivers[] = $user->get('id');
				}
			}
			$receivers = array_unique($receivers);
		}

		// Send the message
		if (!in_array($authorid, $receivers) && $question->get('email'))
		{
			// Flag to mask identity of anonymous question asker
			// MCRN Ticket #134
			if ($question->get('anonymous') == '1')
			{
				$messageType = 'answers_reply_submitted_anonymous';
			}
			else
			{
				$messageType = 'answers_reply_submitted';
			}

			if (!Event::trigger('xmessage.onSendMessage', array($messageType , $subject, $message, $from, array($authorid), $this->_option)))
			{
				$this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		// Send the answers admins message
		if (!empty($receivers))
		{
			if (!Event::trigger('xmessage.onSendMessage', array('new_answer_admin', $subject, $message, $from, $receivers, $this->_option)))
			{
				$this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		// Redirect to the question
		App::redirect(
			Route::url($question->link()),
			Lang::txt('COM_ANSWERS_NOTICE_POSTED_THANKS'),
			'success'
		);
	}

	/**
	 * Mark an answer as accepted
	 *
	 * @return  void
	 */
	public function acceptTask()
	{
		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			$this->loginTask();
			return;
		}

		// Incoming
		$id  = Request::getInt('id', 0);
		$rid = Request::getInt('rid', 0);

		$question = new Question($id);

		// verify the orignial poster is the only one accepting the answer
		if ($question->get('created_by') != User::get('id'))
		{
			App::redirect(
				Route::url($question->link()),
				Lang::txt('COM_ANSWERS_ERROR_MUST_BE_ASKER'),
				'error'
			);
		}

		// Check changes
		if (!$question->accept($rid))
		{
			$this->setError($question->getError());
		}

		// Call the plugin
		if (!Event::trigger('xmessage.onTakeAction', array('answers_reply_submitted', array(User::get('id')), $this->_option, $rid)))
		{
			$this->setError(Lang::txt('COM_ANSWERS_ACTION_FAILED'));
		}

		// Redirect to the question
		App::redirect(
			Route::url($question->link() . '&note=10'),
			Lang::txt('COM_ANSWERS_NOTICE_QUESTION_CLOSED'),
			'success'
		);
	}

	/**
	 * Vote for an item
	 *
	 * @return  void
	 */
	public function voteTask()
	{
		$no_html = Request::getInt('no_html', 0);
		$id      = Request::getInt('id', 0);
		$vote    = Request::getInt('vote', 0);

		// Login required
		if (User::isGuest())
		{
			if (!$no_html)
			{
				$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN_TO_VOTE'));
				$this->loginTask();
			}
			return;
		}

		// Load the question
		$row = new Question($id);

		// Record the vote
		if (!$row->vote($vote))
		{
			if ($no_html)
			{
				$response = new \stdClass;
				$response->success = false;
				$response->message = $row->getError();
				echo json_encode($response);
				return;
			}
			else
			{
				App::redirect(
					Route::url($row->link()),
					$row->getError(),
					'warning'
				);
				return;
			}
		}

		// Update display
		if ($no_html)
		{
			$this->qid = $id;

			$this->view->question = $row;
			$this->view->voted    = $vote;

			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->display();
		}
		else
		{
			App::redirect(
				Route::url($row->link())
			);
		}
	}

	/**
	 * Authorization check
	 *
	 * @param      string  $assetType Asset type to authorize
	 * @param      integer $assetId   ID of asset to authorize
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!User::isGuest())
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}

	/**
	 * Get a message
	 *
	 * @param      integer $type Note ID
	 * @param      array   $note Array to populate
	 * @return     array
	 */
	private function _note($type, $note=array('msg'=>'','class'=>'warning'))
	{
		switch ($type)
		{
			case '1' :  // question was removed
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_QUESTION_REMOVED');
				$note['class'] = 'info';
			break;
			case '2' : // can't delete a closed question
				$note['msg'] = Lang::txt('COM_ANSWERS_WARNING_CANT_DELETE_CLOSED');
			break;
			case '3' : // not authorized to delete question
				$note['msg'] = Lang::txt('COM_ANSWERS_WARNING_CANT_DELETE');
			break;
			case '4' : // answer posted
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_POSTED_THANKS');
				$note['class'] = 'passed';
			break;
			case '5' : // question posted
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_QUESTION_POSTED_THANKS');
				$note['class'] = 'passed';
			break;
			case '6' : // can't answer own question
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_CANT_ANSWER_OWN_QUESTION');
			break;
			case '7' : // can't delete question
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_CANNOT_DELETE');
			break;
			case '8' : // can't vote again
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_ALREADY_VOTED_FOR_QUESTION');
			break;
			case '9' : // can't vote for own question
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_RECOMMEND_OWN_QUESTION');
			break;
			case '10' : // answer accepted
				$note['msg'] = Lang::txt('COM_ANSWERS_NOTICE_QUESTION_CLOSED');
			break;
		}
		return $note;
	}

	/**
	 * Latest Questions Feed
	 *
	 * @return     string XML
	 */
	public function latestTask()
	{
		//instantiate database object
		$database = \App::get('db');

		//get the id of module so we get the right params
		$mid = Request::getInt('m', 0);

		//get module params
		$params = \Module::params($mid);

		//force mime type of document to be rss
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();

		//set rss feed attribs
		$doc->link        = Route::url('index.php?option=com_answers');
		$doc->title       = Lang::txt('COM_ANSWERS_LATEST_QUESTIONS_RSS_TITLE', Config::get('sitename'));
		$doc->description = Lang::txt('COM_ANSWERS_LATEST_QUESTIONS_RSS_DESCRIPTION', Config::get('sitename'));
		$doc->copyright   = Lang::txt('COM_ANSWERS_LATEST_QUESTIONS_RSS_COPYRIGHT', gmdate("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('COM_ANSWERS_LATEST_QUESTIONS_RSS_CATEGORY');

		//number of questions to get
		$limit = intval($params->get('limit', 5));

		//open, closed, or both
		$state = $params->get('state', 'both');
		switch ($state)
		{
			case 'open':   $st = "a.state=0"; break;
			case 'closed': $st = "a.state=1"; break;
			case 'both':   $st = "a.state<2"; break;
		}

		//get questions based on params
		$sql = "SELECT
					a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous,
					(SELECT COUNT(*) FROM `#__answers_responses` AS r WHERE r.question_id=a.id) AS rcount
				FROM `#__answers_questions` AS a
				WHERE {$st}
				ORDER BY a.created DESC
				LIMIT {$limit}";
		$database->setQuery($sql);
		$questions = $database->loadAssocList();

		//add each question to the feed
		foreach ($questions as $question)
		{
			//get the authors name
			$a = User::getInstance($question['created_by']);
			$author = ($a) ? $a->get("name") : "";
			$author = ($question['anonymous']) ? "Anonymous" : $author;

			$link = Route::url('index.php?option=com_answers&task=question&id=' . $question['id']);

			//set feed item attibs and add item to feed
			$item = new \Hubzero\Document\Type\Feed\Item();
			$item->title       = html_entity_decode(Sanitize::stripAll(stripslashes($question['subject'])));
			$item->link        = $link;
			$item->description = html_entity_decode(Sanitize::stripAll(stripslashes($question['question'])));
			$item->date        = date("r", strtotime($question['created']));
			$item->category    = 'Recent Question';
			$item->author      = $author;

			$doc->addItem($item);
		}
	}
}

