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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>
<div class="main section withleft">
	<div class="aside">
		<?php
		$html = '<ul>'."\n";
		if ($this->catid == 0) {
			$cls = ' class="active"';
		} else {
			$cls = '';
		}
		$html .= "\t".'<li'.$cls.'><a href="'.JRoute::_('index.php?option='.$this->option).'">'.JText::_('Knowledge Base').'</li>'."\n";
		if (count($this->categories) > 0) {
			foreach ($this->categories as $row) 
			{
				if ($this->catid == $row->id) {
					$cls = ' class="active"';
				} else {
					$cls = '';
				}

				$html .= "\t".'<li'.$cls.'><a href="'.JRoute::_('index.php?option='.$this->option.'&section='.$row->alias).'">'.Hubzero_View_Helper_Html::xhtml($row->title).'</a></li>'."\n";
			}
		}
		$html .= '</ul>'."\n";
		
		echo $html;
		?>
	</div><!-- / .aside -->
	<div class="subject">
		<h3 class="firstheader"><?php echo stripslashes($this->category->title); ?></h3>
		<?php 
		$html = '';
		if ($this->category->description) {
			if (substr($this->category->description, 0, 2) != '<p') {
				$html .= "\t".'<p>';
			}
			$html .= stripslashes($this->category->description);
			if (substr($this->category->description, 0, 2) != '<p') {
				$html .= '</p>'."\n";
			}
		}
		
		if (count($this->subcategories) > 0) {
			$html .= '<h4>'.JText::_('SUBCATEGORIES').'</h4>'."\n";
			$html .= "\t".'<ul class="categories">'."\n";
			foreach ($this->subcategories as $cat) 
			{
				$html .= "\t\t".'<li><a href="'. JRoute::_('index.php?option='.$this->option.'&section='.$this->category->alias.'&category='. $cat->alias) .'">'. stripslashes($cat->title) .'</a> ('.$cat->numitems.')</li>'."\n";
			}
			$html .= "\t".'</ul>'."\n";
			
			if ($this->articles) {
				$html .= '<h4>'.JText::_('ARTICLES').'</h4>'."\n";
			}
		}

		if (count($this->articles) > 0) {
			$html .= "\t".'<ul class="articles">'."\n";
			foreach ($this->articles as $row) 
			{
				if (is_object($this->section) && $this->section->id) {
					$link = 'index.php?option='.$this->option.'&section='.$this->section->alias;
					$link .= ($row->calias) ? '&category='.$row->calias : '';
				} else {
					$link = 'index.php?option='.$this->option.'&section='.$this->category->alias;
				}
				$link .= ($row->alias) ? '&alias='. $row->alias : '&alias='. $row->id;
				
				$html .= "\t\t".'<li><a href="'. JRoute::_($link) .'">'. stripslashes($row->title) .'</a></li>'."\n";
			}
			$html .= "\t".'</ul>'."\n";
		} else {
			$html .= '<p class="warning">'.JText::_( JText::_('NO_ARTICLES_FOR_CATEGORY') ).'</p>'."\n";
		}
		echo $html;
		?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section withleft -->