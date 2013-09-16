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

jimport('joomla.plugin.plugin');

/**
 * Resources Plugin class for questions and answers
 */
class plgResourcesQuestions extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		if (isset($model->resource->toolpublished) || isset($model->resource->revision))
		{
			if (isset($model->resource->thistool) 
			 && $model->resource->thistool 
			 && ($model->resource->revision=='dev' or !$model->resource->toolpublished)) 
			{
				$model->type->params->set('plg_questions', 0);
			}
		}
		if ($model->type->params->get('plg_questions')) 
		{
			$areas = array(
				'questions' => JText::_('PLG_RESOURCES_QUESTIONS')
			);
		} 
		else 
		{
			$areas = array();
		}
		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model)))) 
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->type->params->get('plg_questions')) 
		{
			return $arr;
		}

		$this->database = JFactory::getDBO();
		$this->model    = $model;
		$this->option   = $option;
		$this->juser     = JFactory::getUser();

		// Get a needed library
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');

		// Get all the questions for this tool
		$this->a = new AnswersTableQuestion($this->database);

		$this->filters = array();
		$this->filters['limit']    = JRequest::getInt('limit', 0);
		$this->filters['start']    = JRequest::getInt('limitstart', 0);
		$this->filters['tag']      = $this->model->isTool() ? 'tool:' . $this->model->resource->alias : 'resource:' . $this->model->resource->id;
		$this->filters['q']        = JRequest::getVar('q', '');
		$this->filters['filterby'] = JRequest::getVar('filterby', '');
		$this->filters['sortby']   = JRequest::getVar('sortby', 'withinplugin');

		$this->count = $this->a->getCount($this->filters);

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');

			switch (strtolower(JRequest::getWord('action', 'browse')))
			{
				case 'save':
					$arr['html'] = $this->_save();
				break;

				case 'new':
					$arr['html'] = $this->_new();
				break;

				case 'browse':
				default:
					$arr['html'] = $this->_browse();
				break;
			}
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->resource = $this->model->resource;
			$view->count    = $this->count;
			$arr['metadata'] = $view->loadTemplate();
		}

		// Return output
		return $arr;
	}

	/**
	 * Show a list of questions attached to this resource
	 * 
	 * @return     string
	 */
	private function _browse()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('resources', $this->_name);

		// Instantiate a view
		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'resources',
				'element' => $this->_name,
				'name'    => 'browse'
			)
		);

		// Are we banking?
		$upconfig =& JComponentHelper::getParams('com_members');
		$view->banking = $upconfig->get('bankAccounts');

		// Info aboit points link
		$aconfig =& JComponentHelper::getParams('com_answers');
		$view->infolink = $aconfig->get('infolink', '/kb/points/');

		// Pass the view some info
		$view->option   = $this->option;
		$view->resource = $this->model->resource;

		// Get results
		$view->rows     = $this->a->getResults($this->filters);
		$view->count    = $this->count;
		$view->limit    = $this->params->get('display_limit', 10);
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a form for adding a question
	 * 
	 * @param      object $row AnswersTableQuestion
	 * @return     string
	 */
	private function _new($row=null)
	{
		// Login required
		if ($this->juser->get('guest')) 
		{
			return $this->_browse();
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_answers');

		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('resources', $this->_name);

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'resources',
				'element' => $this->_name,
				'name'    => 'question',
				'layout'  => 'new'
			)
		);
		$view->option   = $this->option;
		$view->resource = $this->model->resource;
		if (is_object($row))
		{
			$view->row  = $row;
		}
		else
		{
			$view->row  = $this->a;
		}
		$view->tag      = $this->filters['tag'];

		// Are we banking?
		$upconfig =& JComponentHelper::getParams('com_members');
		$view->banking = $upconfig->get('bankAccounts');

		$view->funds = 0;
		if ($view->banking) 
		{
			$juser = JFactory::getUser();

			$BTL = new Hubzero_Bank_Teller($this->database, $juser->get('id'));
			$funds = $BTL->summary() - $BTL->credit_summary();
			$view->funds = ($funds > 0) ? $funds : 0;
		}

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Save a question and redirect to the main listing when done
	 * 
	 * @return     void
	 */
	private function _save()
	{
		// Login required
		if ($this->juser->get('guest')) 
		{
			return $this->_browse();
		}

		// trim and addslashes all posted items
		//$_POST = array_map('trim', $_POST);

		// Incoming
		$tags   = JRequest::getVar('tags', '');
		$funds  = JRequest::getInt('funds', 0);
		$reward = JRequest::getInt('reward', 0);

		// If offering a reward, do some checks
		if ($reward) 
		{
			// Is it an actual number?
			if (!is_numeric($reward)) 
			{
				JError::raiseError(500, JText::_('COM_ANSWERS_REWARD_MUST_BE_NUMERIC'));
				return;
			}
			// Are they offering more than they can afford?
			if ($reward > $funds) 
			{
				JError::raiseError(500, JText::_('COM_ANSWERS_INSUFFICIENT_FUNDS'));
				return;
			}
		}

		// Initiate class and bind posted items to database fields
		$row = new AnswersTableQuestion($this->database);
		if (!$row->bind(JRequest::getVar('question', array(), 'post', 'none', 2))) 
		{
			$this->setError($row->getError());
			return $this->_new($row);
		}

		$row->subject    = Hubzero_Filter::cleanXss($row->subject);
		$row->question   = Hubzero_Filter::cleanXss($row->question);
		$row->question   = nl2br($row->question);
		$row->created    = date('Y-m-d H:i:s', time());
		$row->created_by = $this->juser->get('username');
		$row->state      = 0;
		$row->email      = 1; // force notification
		if ($reward && $this->banking) 
		{
			$row->reward = 1;
		}

		// Ensure the user added a tag
		if (!$tags) 
		{
			$this->setError(JText::_('COM_ANSWERS_QUESTION_MUST_HAVE_TAG'));
			return $this->_new($row);
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			$row->tags = $tags;
			return $this->_new($row);
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			$row->tags = $tags;
			return $this->_new($row);
		}

		// Checkin question
		$row->checkin();

		// Hold the reward for this question if we're banking
		if ($reward && $this->banking) 
		{
			$BTL = new Hubzero_Bank_Teller($this->database, $this->juser->get('id'));
			$BTL->hold($reward, JText::_('COM_ANSWERS_HOLD_REWARD_FOR_BEST_ANSWER'), 'answers', $row->id);
		}

		// Add the tags
		$tagging = new AnswersTags($this->database);
		$tagging->tag_object($this->juser->get('id'), $row->id, $tags, 1, 0);

		// Add the tag to link to the resource
		$tagging->safe_tag($this->juser->get('id'), $row->id, $this->filters['tag'], 1, '', ($this->model->isTool() ? 0 : 1));

		// Get users who need to be notified on every question
		$config = JComponentHelper::getParams('com_answers');
		$apu = ($config->get('notify_users')) ? $config->get('notify_users') : '';
		$apu = explode(',', $apu);
		$apu = array_map('trim', $apu);

		$receivers = array();

		// Get tool contributors if question is about a tool
		if ($tags) 
		{
			$tags = explode(',', $tags);
			if (count($tags) > 0) 
			{
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');

				$TA = new ToolAuthor($this->database);
				$objV = new ToolVersion($this->database);

				if ($this->model->isTool())
				{
					$toolname = $this->model->resource->alias;

					$rev = $objV->getCurrentVersionProperty($toolname, 'revision');
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

		if (!empty($apu)) 
		{
			foreach ($apu as $u)
			{
				$user =& JUser::getInstance($u);
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
			$jconfig =& JFactory::getConfig();
			$hub = array(
				'email' => $jconfig->getValue('config.mailfrom'),
				'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_ANSWERS_ANSWERS')
			);

			// Build the message subject
			$subject = JText::_('COM_ANSWERS_ANSWERS') . ', ' . JText::_('new question about content you author or manage');

			// Build the message	
			$eview = new JView(array(
				'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_answers',
				'name'      => 'emails',
				'layout'    => 'question'
			));
			$eview->option   = 'com_answers';
			$eview->sitename = $jconfig->getValue('config.sitename');
			$eview->juser    = $this->juser;
			$eview->row      = $row;
			$eview->id       = $row->id ? $row->id : 0;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('new_question_admin', $subject, $message, $hub, $receivers, $this->_option))) 
			{
				$this->setError(JText::_('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		// Redirect to the question
		JFactory::getApplication()->redirect(
			JRoute::_('index.php?option=' . $this->option . '&id=' . $this->model->resource->id . '&active=questions')
		);
	}
}

