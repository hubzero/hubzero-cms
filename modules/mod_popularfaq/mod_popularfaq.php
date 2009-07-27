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

class modPopularFaq
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
	    $juser    =& JFactory::getUser();
		$database =& JFactory::getDBO();
		$params   =& $this->params;
		
		$limit = intval( $params->get( 'limit' ) );
		$moduleid = $params->get( 'moduleid' );
		
		/*$database->setQuery( "SELECT a.id, a.alias, a.title, a.state, a.access, a.created, a.modified, a.hits, b.alias AS section "
			."\n FROM #__faq AS a LEFT JOIN #__faq_categories AS b ON b.id=a.section"
			."\n WHERE a.state = 1"
			."\n AND a.access <= ". $juser->get('aid') .""
			."\n ORDER BY a.hits DESC"
			."\n LIMIT ".$limit
			);
		$rows = $database->loadObjectList();*/
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_kb'.DS.'kb.class.php' );
		$a = new KbArticle( $database );
		$rows = $a->getArticles($limit, 'a.hits DESC');
		
		$html  = "\t".'<div id="'.$moduleid.'">'."\n";
		if ($rows) {
			$html .= "\t\t".'<ul class="articles">'."\n";
			foreach ($rows as $row) 
			{
				if ($row->access <= $juser->get('aid')) {
					//$link_on = JRoute::_('index.php?option=com_kb&section='.$row->section.'&article='. $row->alias, 1);
					$link = 'index.php?option=com_kb&amp;section='.$row->section;
					$link .= ($row->category) ? '&amp;category='.$row->category : '';
					$link .= ($row->alias) ? '&amp;alias='. $row->alias : '&amp;alias='. $row->id;
					
					$link_on = JRoute::_($link);
				} else {
					$link_on = JRoute::_('index.php?option=com_hub&task=register');
				}
				$html .= "\t\t".' <li><a href="'. $link_on .'">'.stripslashes($row->title).'</a></li>'."\n";
			}
			$html .= "\t\t".'</ul>'."\n";
		} else {
			$html .= "\t\t".'<p>'.JText::_('NO_ARTICLES_FOUND').'</p>'."\n";
		}
		$html .= "\t".'</div>'."\n";

		echo $html;
	}
}

//-------------------------------------------------------------

$modpopularfaq = new modPopularFaq();
$modpopularfaq->params = $params;

require( JModuleHelper::getLayoutPath('mod_popularfaq') );
?>
