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
	public function &onResourcesAreas($resource)
	{
		if (isset($resource->toolpublished) || isset($resource->revision))
		{
			if (isset($resource->thistool) && $resource->thistool && ($resource->revision=='dev' or !$resource->toolpublished)) 
			{
				$resource->_type->_params->set('plg_questions', 0);
			}
		}
		if ($resource->_type->_params->get('plg_questions')) 
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
	public function onResources($resource, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area' => 'questions',
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($resource))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($resource)))) 
			{
				$rtrn = 'metadata';
			}
		}
		if (!$resource->_type->_params->get('plg_questions')) 
		{
			return $arr;
		}

		$database =& JFactory::getDBO();

		// Get a needed library
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');

		// Get all the questions for this tool
		$a = new AnswersQuestion($database);

		$filters = array();
		$filters['limit']    = JRequest::getInt('limit', 0);
		$filters['start']    = JRequest::getInt('limitstart', 0);
		$filters['tag']      = $resource->type== 7 ? 'tool:' . $resource->alias : 'resource:' . $resource->id;
		$filters['q']        = JRequest::getVar('q', '');
		$filters['filterby'] = JRequest::getVar('filterby', '');
		$filters['sortby']   = JRequest::getVar('sortby', 'withinplugin');

		$count = $a->getCount($filters);

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');

			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('resources', 'questions');

			// Are we banking?
			$upconfig =& JComponentHelper::getParams('com_members');
			$banking = $upconfig->get('bankAccounts');

			// Info aboit points link
			$aconfig =& JComponentHelper::getParams('com_answers');
			$infolink = $aconfig->get('infolink') ? $aconfig->get('infolink') : '/kb/points/';

			$limit = $this->params->get('display_limit');
			$limit = $limit ? $limit : 10;

			// Get results
			$rows = $a->getResults($filters);

			// Instantiate a view
			ximport('Hubzero_View_Helper_Html');
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'questions',
					'name'    => 'browse'
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
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'questions',
					'name'    => 'metadata'
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

