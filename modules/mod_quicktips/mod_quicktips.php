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

class modQuickTips
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------

	public function display()
	{
		global $mainframe;
		
		$database =& JFactory::getDBO();
		$params =& $this->params;

		$catid = trim( $params->get( 'catid' ) );
		$secid = trim( $params->get( 'secid' ) );
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		$method = trim( $params->get( 'method' ) );

		$now = date( 'Y-m-d H:i:s', time() );

		if ($method == 'random') {
			$order = "RAND()";
		} elseif($method == 'ordering') {
			$order = "a.ordering ASC";
		} else {
			$order = "a.publish_up DESC";
		}

		$query = "SELECT a.id, a.title, a.introtext, a.created"
				. "\n FROM #__content AS a"
				. "\n WHERE ( a.state = '1' AND a.checked_out = '0' AND a.sectionid > '0' )"
				. "\n AND ( a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '$now' )"
				. "\n AND ( a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '$now' )"
				. ($catid ? "\n AND ( a.catid IN (". $catid .") )" : '')
				. ($secid ? "\n AND ( a.sectionid IN (". $secid .") )" : '')
				. "\n ORDER BY $order LIMIT 1";
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		$html = '';
		if ($rows) {
			$html .= '<div class="'.$moduleclass_sfx.'">'."\n";
			$html .= '<h3>'.$module->title.'</h3>'."\n";
			foreach ($rows as $row)
			{
				$Itemid = $mainframe->getItemid( $row->id, 0, 0, '','','' );
				if ($Itemid == NULL) {
					$Itemid = '';
				} else {
					$Itemid = '&Itemid='.$Itemid;
				}
		
				$html .= '<p>'.$row->introtext.'</p>'."\n";
				$html .= '<p class="more"><a href="' .JRoute::_( 'index.php?option=com_content&task=view&id='. $row->id . $Itemid ) .'">'.JText::_('Learn more &rsaquo;').'</a></p>'."\n";
			}
			$html .= '</div>'."\n";
		}
		return $html;
	}
}

//-------------------------------------------------------------

$modquicktips = new modQuickTips();
$modquicktips->params = $params;

require( JModuleHelper::getLayoutPath('mod_quicktips') );
?>
