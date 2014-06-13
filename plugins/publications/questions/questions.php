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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Publications Plugin class for questions
 */
class plgPublicationsQuestions extends JPlugin
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
	 * @param      object $publication 	Current publication
	 * @param      string $version 		Version name
	 * @param      boolean $extended 	Whether or not to show panel
	 * @return     array
	 */	
	public function &onPublicationAreas( $publication, $version = 'default', $extended = true ) 
	{
		if ($publication->_category->_params->get('plg_questions') && $extended) {
			$areas = array(
				'questions' => JText::_('PLG_PUBLICATION_QUESTIONS')
			);
		} else {
			$areas = array();
		}
		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  	$publication 	Current publication
	 * @param      string  	$option    		Name of the component
	 * @param      array   	$areas     		Active area(s)
	 * @param      string  	$rtrn      		Data to be returned
	 * @param      string 	$version 		Version name
	 * @param      boolean 	$extended 		Whether or not to show panel
	 * @return     array
	 */	
	public function onPublication( $publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) 
		{
			if (!array_intersect( $areas, $this->onPublicationAreas( $publication ) ) 
			&& !array_intersect( $areas, array_keys( $this->onPublicationAreas( $publication ) ) )) 
			{
				if ($publication->_category->_params->get('plg_questions')) 
				{
					$rtrn == 'metadata';
				}
				else
				{
					return $arr;
				}
			}
		}
		
		// Only applicable to latest published version
		if (!$extended) 
		{
			return $arr;
		}

		$this->database 		= JFactory::getDBO();
		$this->publication    	= $publication;
		$this->option   		= $option;
		$this->juser     		= JFactory::getUser();

		// Get a needed library
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');

		// Get all the questions for this publication
		$this->a = new AnswersTableQuestion( $this->database );

		$this->filters = array();
		$this->filters['limit']    	= JRequest::getInt( 'limit', 0 );
		$this->filters['start']    	= JRequest::getInt( 'limitstart', 0 );
		$identifier 		 		= $this->publication->alias ? $this->publication->alias : $this->publication->id;
		$this->filters['tag']      	= $this->publication->cat_alias == 'tool' 
									?  'tool:' . $identifier : 'publication:' . $identifier;
		$this->filters['rawtag']   	= $this->publication->cat_alias == 'tool' 
									?  'tool:' . $identifier : 'publication:' . $identifier;
		$this->filters['q']        	= JRequest::getVar( 'q', '' );
		$this->filters['filterby'] 	= JRequest::getVar( 'filterby', '' );
		$this->filters['sortby']   	= JRequest::getVar( 'sortby', 'withinplugin' );

		$this->count = $this->a->getCount($this->filters);
		
		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
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
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'publications',
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->publication 	= $this->publication;
			$view->count    	= $this->count;
			$arr['metadata'] 	= $view->loadTemplate();
		}

		// Return output
		return $arr;
	}
	
	/**
	 * Show a list of questions attached to this publication
	 * 
	 * @return     string
	 */
	private function _browse()
	{
		\Hubzero\Document\Assets::addPluginStylesheet('publications', $this->_name);

		// Instantiate a view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'publications',
				'element' => $this->_name,
				'name'    => 'browse'
			)
		);

		// Are we banking?
		$upconfig = JComponentHelper::getParams('com_members');
		$view->banking = $upconfig->get('bankAccounts');

		// Info aboit points link
		$aconfig = JComponentHelper::getParams('com_answers');
		$view->infolink = $aconfig->get('infolink', '/kb/points/');

		// Pass the view some info
		$view->option   	= $this->option;
		$view->publication 	= $this->publication;

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
			$app = JFactory::getApplication();
			$app->redirect(
				'/login?return=' . base64_encode($_SERVER['REQUEST_URI']),
				JText::_('PLG_PUBLICATIONS_QUESTIONS_LOGIN_TO_ASK_QUESTION'),
				'warning'
			);
			return;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_answers');

		\Hubzero\Document\Assets::addPluginStylesheet('publications', $this->_name);

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'publications',
				'element' => $this->_name,
				'name'    => 'question',
				'layout'  => 'new'
			)
		);
		$view->option   	= $this->option;
		$view->publication 	= $this->publication;
		$view->juser    	= $this->juser;
		if (is_object($row))
		{
			$view->row  = $row;
		}
		else
		{
			$view->row  = new AnswersModelQuestion(0);
		}
		$view->tag      = $this->filters['tag'];

		// Are we banking?
		$upconfig = JComponentHelper::getParams('com_members');
		$view->banking = $upconfig->get('bankAccounts');

		$view->funds = 0;
		if ($view->banking) 
		{
			$juser = JFactory::getUser();

			$BTL = new \Hubzero\Bank\Teller($this->database, $juser->get('id'));
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

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

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
		$fields = JRequest::getVar('question', array(), 'post', 'none', 2);

		$row = new AnswersModelQuestion($fields['id']);
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			return $this->_new($row);
		}

		if ($reward && $this->banking) 
		{
			$row->set('reward', 1);
		}

		// Ensure the user added a tag
		if (!$tags) 
		{
			$this->setError(JText::_('COM_ANSWERS_QUESTION_MUST_HAVE_TAG'));
			return $this->_new($row);
		}

		// Store new content
		if (!$row->store(true)) 
		{
			$row->set('tags', $tags);

			$this->setError($row->getError());
			return $this->_new($row);
		}

		// Hold the reward for this question if we're banking
		if ($reward && $this->banking) 
		{
			$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
			$BTL->hold($reward, JText::_('COM_ANSWERS_HOLD_REWARD_FOR_BEST_ANSWER'), 'answers', $row->get('id'));
		}

		// Add the tags
		$row->tag($tags);

		// Add the tag to link to the publication
		$identifier = $this->publication->alias ? $this->publication->alias : $this->publication->id;
		$tag      	= $this->publication->cat_alias == 'tool' ?  'tool' . $identifier : 'publication' . $identifier;
									
		$row->addTag($tag, $this->juser->get('id'), ($this->publication->cat_alias == 'tool' ? 0 : 1));

		// Redirect to the question
		JFactory::getApplication()->redirect(
			JRoute::_('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=questions')
		);
	}
}
