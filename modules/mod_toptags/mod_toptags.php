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

//-------------------------------------------------------------

class modTopTags
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
		require_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		
		$database =& JFactory::getDBO();
		
		// Get some initial parameters
		$params =& $this->params;
		$numtags = $params->get( 'numtags', 25 );
		$message = $params->get( 'message' );
		$sortby  = $params->get( 'sortby' );
		$morelnk = $params->get( 'morelnk' );
		
		$obj = new TagsTag( $database );
		
		$tags = $obj->getTopTags( $numtags );

		$tl = array();
		if (count($tags) > 0) {
			$html  = "\t\t\t".'<ol class="tags">'."\n";
			foreach ($tags as $tag)
			{
				$tl[$tag->tag] = "\t\t\t".' <li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag->tag).'">'.$tag->raw_tag.'</a></li>'."\n";
			}
			if ($sortby == 'alphabeta') {
				ksort($tl);
			}
			$html .= implode('',$tl);
			$html .= "\t\t\t".'</ol>'."\n";
			if ($morelnk) {
				$html .= "\t\t\t".'<p class="more"><a href="'.JRoute::_('index.php?option=com_tags').'">'.JText::_('More &rsaquo;').'</a></p>'."\n";
			}
		} else {
			$html = '<p>'.$message.'</p>';
		}
	
		return $html;
	}
}

//-------------------------------------------------------------

$modtoptags = new modTopTags();
$modtoptags->params = $params;

require( JModuleHelper::getLayoutPath('mod_toptags') );
?>
