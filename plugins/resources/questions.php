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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_questions' );

/**
 * Short description for 'plgResourcesQuestions'
 * 
 * Long description (if any) ...
 */
class plgResourcesQuestions extends JPlugin
{

	/**
	 * Short description for 'plgResourcesQuestions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgResourcesQuestions(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'questions' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onResourcesAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $resource Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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

	/**
	 * Short description for 'onResources'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $resource Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @param      string $rtrn Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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

			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('resources', 'questions');

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
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'questions',
					'name'=>'metadata'
				)
			);
			$view->resource = $resource;
			$view->count = $count;
			$arr['metadata'] = $view->loadTemplate();
		}

		// Return output
		return $arr;
	}
}

