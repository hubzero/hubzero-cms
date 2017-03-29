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
use Components\Answers\Models\Tags;
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
use App;

require_once(\Component::path('com_members') . DS . 'models' . DS . 'member.php');

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
			return $this->loginTask();
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
			App::abort(404, Lang::txt('COM_ANSWERS_ERROR_QUESTION_ID_NOT_FOUND'));
		}

		$row = Comment::oneOrNew($comment['id'])->set($comment);

		// Perform some text cleaning, etc.
		$row->set('anonymous', ($row->get('anonymous') ? 1 : 0));
		$row->set('created', Date::toSql());
		$row->set('state', 0);
		$row->set('created_by', User::get('id'));

		// Save the data
		if (!$row->save())
		{
			App::abort(500, $row->getError());
		}

		// For email
		// Load question
		$question = Question::oneOrFail($questionID);

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

		$authorid = $question->get('created_by');

		$receivers = $this->recipients();

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

		// Log activity
		$recipients = array($question->get('created_by'));
		$recipients[] = $row->get('created_by');
		if ($row->get('parent'))
		{
			$recipients[] = $row->parent()->get('created_by');
		}
		else
		{
			$response = Response::oneOrFail($row->get('item_id'));
			$recipients[] = $response->get('created_by');
		}
		$recipients = $this->recipients($recipients);

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($comment['id'] ? 'updated' : 'created'),
				'scope'       => 'question.answer.comment',
				'scope_id'    => $row->get('id'),
				'anonymous'   => $row->get('anonymous', 0),
				'description' => Lang::txt('COM_ANSWERS_ACTIVITY_COMMENT_' . ($comment['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($question->link()) . '">' . $question->get('subject') . '</a>'),
				'details'     => array(
					'title' => $question->get('title'),
					'url'   => $question->link()
				)
			],
			'recipients' => $recipients
		]);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=question&id=' . $questionID)
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
			return $this->loginTask();
		}

		$this->questionTask();
	}

	/**
	 * Vote for an item
	 *
	 * @return  void
	 */
	public function voteTask()
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
		$id   = Request::getInt('id', 0);
		$type = Request::getVar('category', '');
		$vote = Request::getVar('vote', '');
		$ip   = Request::ip();

		// Check for reference ID
		if (!$id)
		{
			// cannot proceed
			if (!$no_html)
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option),
					Lang::txt('COM_ANSWERS_ERROR_ID_NOT_FOUND'),
					'error'
				);
			}
			return;
		}

		if ($type == 'question')
		{
			$row = Question::oneOrFail($id);
			$scope = 'question';
		}
		elseif ($type == 'response')
		{
			$row = Response::oneOrFail($id);
			$scope = 'question.answer';
		}
		elseif ($type == 'comment')
		{
			$row = Comment::oneOrFail($id);
			$scope = 'question.answer.comment';
		}

		// Can't vote for your own comment
		if ($row->get('created_by') == User::get('id'))
		{
			if (!$no_html)
			{
				App::redirect(
					Route::url($row->link()),
					Lang::txt('COM_ANSWERS_ERROR_CANNOT_VOTE_FOR_OWN'),
					'warning'
				);
			}
			return;
		}

		if (!$vote)
		{
			if (!$no_html)
			{
				App::redirect(
					Route::url($row->link()),
					Lang::txt('COM_ANSWERS_ERROR_VOTE_NOT_FOUND'),
					'warning'
				);
			}
			return;
		}

		if (!$row->vote($vote, User::get('id'), $ip))
		{
			$this->setError($row->getError());
		}

		if (!$this->getError())
		{
			// Log activity
			$recipients = array(User::get('id'));

			if ($row instanceof Question)
			{
				$question = $row;
				$txt = $row->get('subject');
			}
			if ($row instanceof Response)
			{
				$question = Question::oneOrFail($row->get('question_id'));
				$txt = Lang::txt('COM_ANSWERS_ACTIVITY_ANSWER_ON', $row->get('id'), $question->get('subject'));
			}
			if ($row instanceof Comment)
			{
				$question = Question::oneOrFail($row->get('question_id'));
				$txt = Lang::txt('COM_ANSWERS_ACTIVITY_COMMENT_ON', $row->get('id'), $question->get('subject'));
			}

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'voted',
					'scope'       => $scope,
					'scope_id'    => $id,
					'description' => Lang::txt('COM_ANSWERS_ACTIVITY_VOTED_' . ($vote == 'yes' ? 'UP' : 'DOWN'), '<a href="' . Route::url($row->link()) . '">' . $txt . '</a>'),
					'details'     => array(
						'title'       => $question->get('title'),
						'question_id' => $question->get('id'),
						'url'         => $question->link()
					)
				],
				'recipients' => $recipients
			]);
		}

		// update display
		if ($no_html)
		{
			$row->set('vote', $vote);

			$this->view
				->setError($this->getErrors())
				->set('item', $row)
				->set('vote', $row->ballot())
				->setLayout('_vote')
				->display();
		}
		else
		{
			App::redirect(
				Route::url($row->link())
			);
		}
	}

	/**
	 * Search entries
	 *
	 * @return  void
	 */
	public function searchTask()
	{
		// Incoming
		$filters = array(
			'limit'    => Request::getInt('limit', Config::get('list_limit')),
			'start'    => Request::getInt('limitstart', 0),
			'tag'      => Request::getVar('tags', ''),
			'search'   => Request::getVar('q', ''),
			'filterby' => Request::getWord('filterby', ''),
			'sortby'   => Request::getWord('sortby', 'date'),
			'sort_Dir' => strtolower(Request::getWord('sortdir', 'desc')),
			'area'     => Request::getVar('area', '')
		);

		// Validate inputs
		$filters['tag'] = ($filters['tag'] ? $filters['tag'] : Request::getVar('tag', ''));

		if ($filters['filterby']
		 && !in_array($filters['filterby'], array('open', 'closed')))
		{
			$filters['filterby'] = '';
		}

		if (!in_array($filters['sortby'], array('date', 'votes', 'rewards')))
		{
			$filters['sortby'] = 'date';
		}

		if (!in_array($filters['sort_Dir'], array('desc', 'asc')))
		{
			$filters['sort_Dir'] = 'desc';
		}

		if ($filters['area']
		 && !in_array($filters['area'], array('mine', 'assigned', 'interest')))
		{
			$filters['area'] = '';
		}

		// Get questions of interest
		// @TODO: Remove reference to members. Add getTags() to user?
		if ($filters['area'] == 'interest')
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'tags.php');

			// Get tags of interest
			$mt = new \Components\Members\Models\Tags(User::get('id'));

			$filters['tag'] = $mt->render('string');
		}

		// Get assigned questions
		// @TODO: Remove reference to tools. Turn into an event call?
		if ($filters['area'] == 'assigned')
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');

			// What tools did this user contribute?
			$db = App::get('db');

			$TA = new \Components\Tools\Tables\Author($db);
			$tools = $TA->getToolContributions(User::get('id'));
			$mytooltags = array();
			if ($tools)
			{
				foreach ($tools as $tool)
				{
					$mytooltags[] = 'tool' . $tool->toolname;
				}
			}

			$filters['tag'] = implode(',', $mytooltags);
		}

		$records = Question::all()
			->including(['responses', function ($response)
			{
				$response
					->select('id')
					->select('question_id')
					->where('state', '!=', Question::STATE_DELETED);
			}]);

		if ($filters['tag'] || $filters['area'] == 'interest' || $filters['area'] == 'assigned')
		{
			$cloud = new Tags();
			$tags = $cloud->parse($filters['tag']);

			$records
				->select('#__answers_questions.*')
				->join('#__tags_object', '#__tags_object.objectid', '#__answers_questions.id')
				->join('#__tags', '#__tags.id', '#__tags_object.tagid')
				->whereEquals('#__tags_object.tbl', 'answers')
				->whereIn('#__tags.tag', $tags);
		}

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$records->whereLike('subject', $filters['search'], 1)
					->orWhereLike('question', $filters['search'], 1)
					->resetDepth();
		}

		if ($filters['filterby'] == 'open')
		{
			$records->whereEquals('state', 0);
		}
		if ($filters['filterby'] == 'closed')
		{
			$records->whereEquals('state', 1);
		}
		if (!$filters['filterby'] || $filters['filterby'] == 'both')
		{
			$records->where('state', '<', Question::STATE_DELETED);
		}

		if ($filters['area'] == 'mine')
		{
			$records->whereEquals('created_by', User::get('id'));
		}

		switch ($filters['sortby'])
		{
			case 'rewards':
				$order = 'reward';
				break;
			case 'votes':
				$order = 'helpful';
				break;
			case 'date':
			default:
				$order = 'created';
				break;
		}

		$results = $records
			->order($order, $filters['sort_Dir'])
			->paginated()
			->rows();

		// Output HTML
		$this->view
			->setError($this->getErrors())
			->set('results', $results)
			->set('filters', $filters)
			->set('config', $this->config)
			->setLayout('search')
			->display();
	}

	/**
	 * Display a question
	 *
	 * @return  void
	 */
	public function questionTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_ANSWERS_ERROR_QUESTION_ID_NOT_FOUND'));
		}

		$question = Question::oneOrFail($id);

		// Check session if this is a newly submitted entry. Trigger a proper event if so.
		if (Session::get('newsubmission.question')) {
			// Unset the new submission session flag
			Session::set('newsubmission.question');
			Event::trigger('content.onAfterContentSubmission', array('Question'));
		}

		$this->view
			->set('question', $question)
			->set('config', $this->config)
			->set('responding', 0)
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
		$this->questionTask();
	}

	/**
	 * Show a confirmation form for deleting a question
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
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
			return $this->loginTask();
		}

		if (!User::authorise('core.create', $this->_option)
		 && !User::authorise('core.manage', $this->_option))
		{
			App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		}

		// Instantiate if doesn't exist
		if (!is_object($question))
		{
			$question = new Question();
		}

		// Is banking turned on?
		$funds = 0;

		if ($this->config->get('banking'))
		{
			$BTL = new Teller(User::get('id'));

			$funds = $BTL->summary() - $BTL->credit_summary();
			$funds = ($funds > 0) ? $funds : 0;
		}

		// Render view
		$this->view
			->setError($this->getErrors())
			->set('question', $question)
			->set('config', $this->config)
			->set('funds', $funds)
			->set('tag', Request::getVar('tag', ''))
			->setLayout('new')
			->display();
	}

	/**
	 * Save a question
	 *
	 * @return  void
	 */
	public function saveqTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			return $this->loginTask();
		}

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.manage', $this->_option))
		{
			App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
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
				App::abort(500, Lang::txt('COM_ANSWERS_REWARD_MUST_BE_NUMERIC'));
			}
			// Are they offering more than they can afford?
			if ($fields['reward'] > $fields['funds'])
			{
				App::abort(500, Lang::txt('COM_ANSWERS_INSUFFICIENT_FUNDS'));
			}
		}
		unset($fields['funds']);

		// clean input
		array_walk($fields, function(&$field, $key)
		{
			$field = \Hubzero\Utility\Sanitize::clean($field);
		});

		// Initiate class and bind posted items to database fields
		$row = Question::oneOrNew($fields['id'])->set($fields);

		if ($fields['reward'] && $this->config->get('banking'))
		{
			$row->set('reward', 1);
		}

		// Store new content
		if (!Request::checkHoneypot())
		{
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_INVALID_CONTENT'));
			return $this->newTask($row);
		}

		// Ensure the user added a tag
		if (!$tags)
		{
			Notify::error(Lang::txt('COM_ANSWERS_QUESTION_MUST_HAVE_TAG'));
			return $this->newTask($row);
		}

		// Store new content
		if (!$row->save())
		{
			Request::setVar('tag', $tags);

			Notify::error($row->getError());
			return $this->newTask($row);
		}

		// Hold the reward for this question if we're banking
		if ($fields['reward'] && $this->config->get('banking'))
		{
			$BTL = new Teller(User::get('id'));
			$BTL->hold(
				$fields['reward'],
				Lang::txt('COM_ANSWERS_HOLD_REWARD_FOR_BEST_ANSWER'),
				'answers',
				$row->get('id')
			);
		}

		// Add the tags
		$row->tag($tags);

		$recipients = array($row->get('created_by'));
		$recipients = $this->recipients($recipients);

		// Get tool contributors if question is about a tool
		if ($tags)
		{
			$tags = preg_split("/[,;]/", $tags);
			if (count($tags) > 0)
			{
				require_once Component::path('com_tools') . DS . 'tables' . DS . 'author.php';
				require_once Component::path('com_tools') . DS . 'tables' . DS . 'version.php';

				$db = \App::get('db');
				$TA = new \Components\Tools\Tables\Author($db);
				$objV = new \Components\Tools\Tables\Version($db);

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
							$rev = $objV->getCurrentVersionProperty($toolname, 'revision');
							$authors = $TA->getToolAuthors('', 0, $toolname, $rev);
							if (count($authors) > 0)
							{
								foreach ($authors as $author)
								{
									$recipients[] = $author->uidNumber;
								}
							}
						}
					}
				}
			}
		}

		// Send the message
		if (!empty($recipients))
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

			if (!Event::trigger('xmessage.onSendMessage', array('new_question_admin', $subject, $message, $from, $recipients, $this->_option)))
			{
				Notify::error(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'question',
				'scope_id'    => $row->get('id'),
				'anonymous'   => $row->get('anonymous', 0),
				'description' => Lang::txt('COM_ANSWERS_ACTIVITY_QUESTION_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($row->link()) . '">' . $row->get('subject') . '</a>'),
				'details'     => array(
					'title' => $row->get('title'),
					'url'   => $row->link()
				)
			],
			'recipients' => $recipients
		]);

		// Set the session flag indicating the new submission
		Session::set('newsubmission.question', true);

		// Redirect to the question
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=question&id=' . $row->get('id')),
			Lang::txt('COM_ANSWERS_NOTICE_QUESTION_POSTED_THANKS')
		);
	}

	/**
	 * Delete a question
	 *
	 * @return  void
	 */
	public function deleteqTask()
	{
		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			return $this->loginTask();
		}

		if (!User::authorise('core.delete', $this->_option)
		 && !User::authorise('core.manage', $this->_option))
		{
			App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		}

		// Incoming
		$id = Request::getInt('qid', 0);
		$ip = (!User::isGuest()) ? Request::ip() : '';

		$reward = 0;
		if ($this->config->get('banking'))
		{
			$reward = Transaction::getAmount('answers', 'hold', $id);
		}

		$question = Question::oneOrFail($id);
		$question->set('state', Question::STATE_DELETED);
		$question->set('reward', 0);

		// Store new content
		if (!$question->save())
		{
			App::abort(500, $question->getError());
		}

		if ($reward && $this->config->get('banking'))
		{
			/*
			// Get all the answers for this question
			$responses = $question->responses()->rows();

			if ($responses->count())
			{
				$users = array();
				foreach ($responses as $r)
				{
					$users[] = $r->get('created_by');
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
			*/

			// Remove hold
			$transaction->deleteRecords('answers', 'hold', $id);

			// Make credit adjustment
			$teller = new Teller(User::get('id'));
			$adjusted = $teller->credit_summary() - $reward;
			$teller->credit_adjustment($adjusted);
		}

		// Log activity
		$recipients = array($question->get('created_by'));
		$recipients = $this->recipients($recipients);

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'question',
				'scope_id'    => $question->get('id'),
				'description' => Lang::txt('COM_ANSWERS_ACTIVITY_QUESTION_DELETED', '<a href="' . Route::url($question->link()) . '">' . $question->get('subject') . '</a>'),
				'details'     => array(
					'title' => $question->get('title'),
					'url'   => $question->link()
				)
			],
			'recipients' => $recipients
		]);

		// Redirect to the question
		App::redirect(
			Route::url('index.php?option=' . $this->_option)
		);
	}

	/**
	 * Save an answer (reply to question)
	 *
	 * @return  void
	 */
	public function saveaTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Login required
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_ANSWERS_PLEASE_LOGIN'));
			return $this->loginTask();
		}

		// Incoming
		$response = Request::getVar('response', array(), 'post', 'none', 2);

		// clean input
		array_walk($response, function(&$field, $key)
		{
			$field = \Hubzero\Utility\Sanitize::clean($field);
		});

		// Initiate class and bind posted items to database fields
		$row = Response::oneOrNew($response['id'])->set($response);

		// Store new content
		if (!$row->save())
		{
			App::abort(500, $row->getError());
		}

		// Load the question
		$question = Question::oneOrFail($row->get('question_id'));

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

		$authorid = $question->get('created_by');

		$receivers = $this->recipients();

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

		// Log activity
		$recipients = array($row->get('created_by'));
		if ($row->get('created_by') != $question->get('created_by'))
		{
			$recipients[] = $question->get('created_by');
		}
		$recipients = $this->recipients($recipients);

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($response['id'] ? 'updated' : 'created'),
				'scope'       => 'question.answer',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_ANSWERS_ACTIVITY_ANSWER_SUBMITTED', '<a href="' . Route::url($question->link() . '#a' . $row->get('id')) . '">' . $question->get('subject') . '</a>'),
				'details'     => array(
					'title'       => $question->get('title'),
					'question_id' => $question->get('id'),
					'url'         => $question->link()
				)
			],
			'recipients' => $recipients
		]);

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
			return $this->loginTask();
		}

		// Incoming
		$id  = Request::getInt('id', 0);
		$rid = Request::getInt('rid', 0);

		$question = Question::oneOrFail($id);

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

		// Log the activity
		if (!$this->getError())
		{
			$answer = Response::oneOrFail($rid);

			$recipients = array($answer->get('created_by'));
			if ($answer->get('created_by') != $question->get('created_by'))
			{
				$recipients[] = $question->get('created_by');
			}

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'accepted',
					'scope'       => 'question.answer',
					'scope_id'    => $rid,
					'description' => Lang::txt('COM_ANSWERS_ACTIVITY_ANSWER_ACCEPTED', $rid, '<a href="' . Route::url($question->link() . '#a' . $rid) . '">' . $question->get('subject') . '</a>'),
					'details'     => array(
						'title'       => $question->get('title'),
						'question_id' => $question->get('id'),
						'url'         => $question->link()
					)
				],
				'recipients' => $recipients
			]);
		}

		// Redirect to the question
		App::redirect(
			Route::url($question->link()),
			($this->getError() ? $this->getError() : Lang::txt('COM_ANSWERS_NOTICE_QUESTION_CLOSED')),
			($this->getError() ? 'error' : 'success')
		);
	}

	/**
	 * Latest Questions Feed
	 *
	 * @return  void
	 */
	public function latestTask()
	{
		//get the id of module so we get the right params
		$mid = Request::getInt('m', 0);

		//get module params
		$params = \Module::params($mid);

		//number of questions to get
		$limit = intval($params->get('limit', 5));

		//open, closed, or both
		$state = $params->get('state', 'both');

		$records = Question::all();

		if ($state == 'open')
		{
			$records->whereEquals('state', 0);
		}
		if ($state == 'closed')
		{
			$records->whereEquals('state', 1);
		}
		if (!$state || $state == 'both')
		{
			$records->where('state', '<', Question::STATE_DELETED);
		}

		$questions = $records
			->ordered()
			->limit($limit)
			->start(0)
			->paginated()
			->rows();

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

		//add each question to the feed
		foreach ($questions as $question)
		{
			//set feed item attibs and add item to feed
			$item = new \Hubzero\Document\Type\Feed\Item();
			$item->title       = html_entity_decode(Sanitize::stripAll(stripslashes($question->subject)));
			$item->link        = Route::url($question->link());
			$item->description = html_entity_decode(Sanitize::stripAll(stripslashes($question->question)));
			$item->date        = date("r", strtotime($question->get('created')));
			$item->category    = Lang::txt('COM_ANSWERS_LATEST_QUESTIONS_RSS_CATEGORY_ITEM');
			$item->author      = $question->creator()->get('name', Lang::txt('COM_ANSWERS_ANONYMOUS'));

			$doc->addItem($item);
		}
	}

	/**
	 * Get a list of people to be notified of updates
	 *
	 * @param   array  $recipients
	 * @return  array
	 */
	protected function recipients($recipients = array())
	{
		$apu = explode(',', $this->config->get('notify_users', ''));
		$apu = array_map('trim', $apu);

		foreach ($apu as $u)
		{
			$user = User::getInstance($u);

			if ($user)
			{
				$recipients[] = $user->get('id');
			}
		}

		$recipients = array_unique($recipients);

		return $recipients;
	}
}
