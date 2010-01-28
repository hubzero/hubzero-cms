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
JPlugin::loadLanguage( 'plg_resources_versions' );
	
//-----------

class plgResourcesVersions extends JPlugin
{
	function plgResourcesVersions(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'versions' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onResourcesAreas( $resource ) 
	{
		if ($resource->type != 7) {
			$areas = array();
		} else {
			$areas = array(
				'versions' => JText::_('VERSIONS')
			);
		}
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Display only for tools
		if ($resource->type != 7) {
			return array('html'=>'','metadata'=>'');
		}

		$database =& JFactory::getDBO();

		$html = '';
		if ($rtrn == 'all' || $rtrn == 'html') {
			$tv = new ToolVersion( $database );
			$rows = $tv->getVersions( $resource->alias );

			// Did we get results back?
			if ($rows) {
				//$xhub =& XFactory::getHub();
				//$hubDOIpath = $xhub->getCfg('hubDOIpath');
				$config =& JComponentHelper::getParams( $option );
				$hubDOIpath = $config->get('doi');

				$cls = 'even';

				// Loop through the results and build the HTML
				$sbjt  = '<table class="resource-versions" summary="'.JText::_('VERSIONS_TBL_SUMMARY').'">'.n;
				$sbjt .= t.'<thead>'.n;
				$sbjt .= t.t.'<tr>'.n;
				$sbjt .= t.t.t.'<th>'.JText::_('VERSION').'</th>'.n;
				$sbjt .= t.t.t.'<th>'.JText::_('RELEASED').'</th>'.n;
				$sbjt .= t.t.t.'<th>'.JText::_('DOI_HANDLE').'</th>'.n;
				$sbjt .= t.t.t.'<th>'.JText::_('PUBLISHED').'</th>'.n;
				$sbjt .= t.t.'</tr>'.n;
				$sbjt .= t.'</thead>'.n;
				$sbjt .= t.'<tbody>'.n;
				foreach ($rows as $v) 
				{
					$handle = ($v->doi) ? $hubDOIpath.'r'.$resource->id.'.'.$v->doi : '' ;

					$cls = (($cls == 'even') ? 'odd' : 'even');

					$sbjt .= t.t.'<tr class="'.$cls.'">'.n;
					$sbjt .= t.t.t.'<td>';
					$sbjt .= ($v->version) ? '<a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id).'?rev='.$v->revision.'">'.$v->version.'</a>' : 'N/A';
					$sbjt .= '</td>'.n;
					$sbjt .= t.t.t.'<td>';
					$sbjt .= ($v->released && $v->released!='0000-00-00 00:00:00') ? JHTML::_('date',$v->released, '%d %b %Y') : 'N/A';
					$sbjt .= '</td>'.n;
					$sbjt .= t.t.t.'<td>';
					$sbjt .= ($handle) ? '<a href="http://hdl.handle.net/'.$handle.'">'.$handle.'</a>' : 'N/A';
					$sbjt .= '</td>'.n;
					$sbjt .= t.t.t.'<td><span class="';
					$sbjt .= ($v->state=='1') ? 'toolpublished' : 'toolunpublished';
					$sbjt .= '">';
					$sbjt .= ($v->state=='1') ? JText::_('YES') : JText::_('NO');
					$sbjt .= '</span></td>'.n;
					$sbjt .= t.t.'</tr>'.n;
				}
				$sbjt .= t.'</tbody>'.n;
				$sbjt .= '</table>'.n;
			} else {
				$sbjt  = t.t.'<p>'.JText::_('NO_VERIONS_FOUND').'</p>'.n;
			}


			//$html .= ResourcesHtml::aside('<p>'.JText::_('VERSIONS_EXPLANATION').'</p>');
			$html  = ResourcesHtml::hed(3,'<a name="versions"></a>'.JText::_('VERSIONS')).n;
			$html .= $sbjt;
		}

		$metadata = '';
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			$metadata = '';
		}
		
		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
}