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

ximport('Hubzero_Plugin');
	
/**
 * Publications Plugin class for usage
 */
class plgPublicationsUsage extends JPlugin
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
		$this->_plugin = JPluginHelper::getPlugin( 'publications', 'usage' );
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
		if ($publication->_category->_params->get('plg_usage') && $extended) 
		{
			$areas = array(
				'usage' => JText::_('PLG_PUBLICATION_USAGE')
			);
		} 
		else 
		{
			$areas = array();
		}
		
		// Temp - do not show the actual panel yet 
		$areas = array();
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
				
		// Display panel only for tools
		if ($publication->base != 'apps') 
		{
			$rtrn == 'metadata';
		}

		// Check if we have a needed database table
		$database =& JFactory::getDBO();
		
		$tables = $database->getTableList();
		$table = $database->getPrefix() . 'publication_stats';

		if ($publication->alias) 
		{
			$url = JRoute::_('index.php?option=' . $option . '&alias=' . $publication->alias . '&active=usage');
		} 
		else 
		{
			$url = JRoute::_('index.php?option=' . $option . '&id=' . $publication->id . '&active=usage');
		}

		if (!in_array($table,$tables)) 
		{
			$arr['html'] 	 = '<p class="error">' . JText::_('PLG_PUBLICATION_USAGE_MISSING_TABLE') . '</p>';
			$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' 
							. JText::_('PLG_PUBLICATION_USAGE_DETAILED') . '</a></p>';
			return $arr;
		}
		
		// Get/set some variables
		$dthis  = JRequest::getVar('dthis', date('Y') . '-' . date('m'));
		$period = JRequest::getInt('period', $this->_params->get('period', 14));

		include_once( JPATH_ROOT . DS . 'administrator' .DS. 'components' . DS . $option . DS . 'tables' . DS . 'stats.php' );
		require_once( JPATH_ROOT . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'usage.php' );
		
		$stats = new PublicationStats( $database );
		$stats->loadStats( $publication->id, $period, $dthis );

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addComponentStylesheet('com_usage');
			
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'publications',
					'element'=>'usage',
					'name'=>'browse'
				)
			);
			
			// Get usage helper
			$view->helper = new PublicationUsage($database, $publication->id, $publication->base);

			// Pass the view some info
			$view->option 		= $option;
			$view->publication 	= $publication;
			$view->stats 		= $stats;
			$view->chart_path 	= $this->_params->get('chart_path','');
			$view->map_path 	= $this->_params->get('map_path','');
			$view->dthis 		= $dthis;
			$view->period 		= $period;
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();

		}

		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{			
			$stats->loadStats( $publication->id, $period );
			if ($stats->users) 
			{
				$action = $publication->base == 'files' ? '%s download(s)' : '%s view(s)';
				$arr['metadata'] = '<p class="usage">' . JText::sprintf('%s user(s)',$stats->users) 
					. ' | ' . JText::sprintf($action, $stats->downloads) . '</p>';
			}
		}

		return $arr;
	}
	
	/**
	 * Round time into nearest second/minutes/hours/days
	 * 
	 * @param      integer $time Time
	 * @return     string
	 */	
	public function timeUnits($time) 
	{
		if ($time < 60) 
		{
			$data = round($time,2). ' ' .JText::_('PLG_PUBLICATION_USAGE_SECONDS');
		} 
		else if ($time > 60 && $time < 3600) 
		{
			$data = round(($time/60), 2) . ' ' .JText::_('PLG_PUBLICATION_USAGE_MINUTES');
		} 
		else if ($time >= 3600 && $time < 86400) 
		{
			$data = round(($time/3600), 2). ' ' .JText::_('PLG_PUBLICATION_USAGE_HOURS');
		} 
		else if ($time >= 86400) 
		{
			$data = round(($time/86400),2). ' ' .JText::_('PLG_PUBLICATION_USAGE_DAYS');
		}

		return $data;
	}
}
