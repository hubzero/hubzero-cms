<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_questions' );
	
//-----------

class plgResourcesQuestions extends JPlugin
{
	public function plgResourcesQuestions(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'questions' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesAreas( $resource ) 
	{
		if ($resource->_type->_params->get('plg_questions')) {
			$areas = array(
				'questions' => JText::_('PLG_RESOURCES_QUESTIONS')
			);
		} else {
			$areas = array();
		}
		return $areas;
	}

	//-----------

	public function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Display only for tools
		if ($resource->type != 7) {
			return $arr;
		}

		$database =& JFactory::getDBO();

		// Get a needed library
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'question.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'response.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'log.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'questionslog.php' );

		// Get all the questions for this tool
		$a = new AnswersQuestion( $database );

		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 0 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$filters['tag']      = $resource->type== 7 ?  'tool'.$resource->alias : 'resource'.$resource->id;
		$filters['q']        = JRequest::getVar( 'q', '' );
		$filters['filterby'] = JRequest::getVar( 'filterby', '' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'withinplugin' );

		$count = $a->getCount( $filters );
		
		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') {
			include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'reportabuse.php');
			
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
					'folder'=>'resources',
					'element'=>'questions',
					'name'=>'browse'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->resource = $resource;
			$view->rows = $rows;
			$view->count = $count;
			$view->infolink = $infolink;
			$view->banking = $banking;
			$view->limit = $limit;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}
		
		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			$arr['metadata']  = '<p class="answer"><a href="'.JRoute::_('index.php?option='.$option.'&alias='.$resource->alias.'&active=questions').'">';
			if ($count == 1) {
				$arr['metadata'] .= JText::sprintf('PLG_RESOURCES_QUESTIONS_NUM_QUESTION',$count);
			} else {
				$arr['metadata'] .= JText::sprintf('PLG_RESOURCES_QUESTIONS_NUM_QUESTIONS',$count);
			}
			$arr['metadata'] .= '</a> (<a href="/answers/question/new/?tag=tool:'.$resource->alias.'">'.JText::_('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION').'</a>)</p>';
		}
		
		// Return output
		return $arr;
	}
}
