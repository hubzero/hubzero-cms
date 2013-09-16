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
		
		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'publications', 'questions' );
		$this->_params = new JParameter( $this->_plugin->params );

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
				$rtrn = 'metadata';
			}
		}
		
		// Only applicable to latest published version
		if (!$extended) 
		{
			return $arr;
		}

		$database =& JFactory::getDBO();

		// Get a needed library
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php' );

		// Get all the questions for this tool
		$a = new AnswersTableQuestion( $database );

		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 0 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$identifier 		 = $publication->alias ? $publication->alias : $publication->id;
		$filters['tag']      = $publication->cat_alias == 'tool' ?  'tool'.$identifier : 'publication'.$identifier;
		$filters['rawtag']   = $publication->cat_alias == 'tool' ?  'tool:'.$identifier : 'publication:'.$identifier;
		$filters['q']        = JRequest::getVar( 'q', '' );
		$filters['filterby'] = JRequest::getVar( 'filterby', '' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'withinplugin' );

		$count = $a->getCount( $filters );
		
		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');
			
			// Are we banking?
			$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
			$banking = $upconfig->get('bankAccounts');		

			// Info aboit points link
			$aconfig =& JComponentHelper::getParams( 'com_answers' );
			$infolink = $aconfig->get('infolink') ? $aconfig->get('infolink') : '/kb/points/';
			
			$limit = $this->_params->get('display_limit');
			$limit = $limit ? $limit : 10;
			
			// Get results
			$rows = $a->getResults( $filters );
			
			// Instantiate a view
			ximport('Hubzero_View_Helper_Html');
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'publications',
					'element'=>'questions',
					'name'=>'browse'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->publication = $publication;
			$view->rows = $rows;
			$view->count = $count;
			$view->infolink = $infolink;
			$view->banking = $banking;
			$view->limit = $limit;
			$view->filters = $filters;
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}
		
		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			$arr['metadata']  = '<p class="answer"><a href="'
				.JRoute::_('index.php?option='.$option.'&id='.$publication->id.'&active=questions&v=' . $publication->version_number).'">';
			if ($count == 1) 
			{
				$arr['metadata'] .= JText::sprintf('PLG_PUBLICATION_QUESTIONS_NUM_QUESTION',$count);
			} 
			else 
			{
				$arr['metadata'] .= JText::sprintf('PLG_PUBLICATION_QUESTIONS_NUM_QUESTIONS',$count);
			}
			$arr['metadata'] .= '</a> (<a href="/answers/question/new/?tag='
				.$filters['rawtag'].'">'.JText::_('PLG_PUBLICATION_QUESTIONS_ASK_A_QUESTION').'</a>)</p>';
		}
		
		// Return output
		return $arr;
	}
}
