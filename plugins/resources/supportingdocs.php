<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
JPlugin::loadLanguage( 'plg_resources_supportingdocs' );
	
//-----------

class plgResourcesSupportingDocs extends JPlugin
{
	public function plgResourcesSupportingDocs(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'supportingdocs' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesAreas( $resource, $archive = 0 ) 
	{
		
		if ($archive) {
			$areas = array();			
		} else if ($resource->type !=8) {
			$areas = array(
				'supportingdocs' => JText::_('Supporting Documents')
			);
		} else {
			$areas = array();			
		}
		
		return $areas;
	}

	//-----------

	public function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				// do nothing
			}
		}
		
		$database =& JFactory::getDBO();
		
		// Initiate a resource helper class
		$helper = new ResourcesHelper( $resource->id, $database );
		$config =& JComponentHelper::getParams( $option );
		
		$helper->getChildren( $resource->id, 0, 'all', 1 );
		$children = $helper->children;
		$dls = '';
		
		$xhub =& XFactory::getHub();
		$live_site = $xhub->getCfg('hubLongURL');
		
		switch ($resource->type)
		{
			case 7:
				$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $helper->children, '', '', '', $resource->id, $fsize=0 );									
			break;
				
			case 4:
				$dls = '';

				$database->setQuery( "SELECT r.path, r.type, r.title, r.access, r.id, r.standalone, a.* FROM #__resources AS r, #__resource_assoc AS a WHERE a.parent_id=".$resource->id." AND r.id=a.child_id AND r.access=1 ORDER BY a.ordering" );
				if ($database->query()) {
					$downloads = $database->loadObjectList();
				}
				$base = $config->get('uploadpath');
				if ($downloads) {
					$dls .= '<ul>'."\n";
					foreach ($downloads as $download)
					{
						$ftype = '';
						$liclass = '';
						$file_name_arr = explode('.',$download->path);
						$ftype = end($file_name_arr);
						$ftype = (strlen($ftype) > 3) ? substr($ftype, 0, 3): $ftype;

						if ($download->type == 12) {
							$liclass = ' class="html"';
						} else {
							$liclass = ' class="'.$ftype.'"';
						}

						$url = ResourcesHtml::processPath($option, $download, $resource->id);

						$dls .= "\t".'<li'.$liclass.'><a href="'.$url.'">'.$download->title.'</a> ';
						$dls .= ResourcesHtml::getFileAttribs( $download->path, $base, 0 );
						$dls .= '</li>'."\n";
					}
					$dls .= '</ul>'."\n";
				} else {
					$dls .= '<p>[ none ]</p>';
				}
			break;
				
			case 8:
				// show no docs
			break;
			
			case 6:
			case 31:
			case 2:					
				$helper->getChildren( $resource->id, 0, 'no' );
				$children = $helper->children;
				$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $children, $live_site, '', '', $resource->id, $fsize=0 );
			break;
				
			default:
				$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $helper->children, '', '', '', $resource->id, $fsize=0 );
			break;
		}
		
		$html  = '<div class="supportingdocs">'."\n";
		$html .= '<h3>'.JText::_('SUPPORTING_DOCUMENTS').'</h3>'."\n";
		$html .= $dls;
		$html .= '</div><!-- / .supportingdocs -->'."\n";

		$arr = array(
			'html'=>$html,
			'metadata'=>''
		);

		return $arr;
	}
}
