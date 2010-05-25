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
		<div id="fixwrap">
		<?php 
		$i = 0;
		$html = '';
		if (count($this->categories) > 0) {
			foreach ($this->categories as $row)
			{
				$i++;

				switch ($i) 
				{
					case 1: $cls = 'first';  break;
					case 2: $cls = 'second'; break;
					case 3: $cls = 'third';  break;
				}

				$html .= "\t\t".'<div class="three columns '.$cls.'">'."\n";
				$html .= "\t\t\t".'<p><a class="dir" href="'.JRoute::_('index.php?option='.$this->option.'&section='. $row->alias) .'">'. stripslashes($row->title) .'</a>';
				$html .= ' ('.$row->numitems.')';
				if ($row->description) {
					$html .= '<br />';
					$html .= Hubzero_View_Helper_Html::xhtml(Hubzero_View_Helper_Html::shortenText($row->description, 100, 0));
				}
				$html .= '</p>'."\n";
				$html .= "\t\t".'</div><!-- / .three columns '.$cls.' -->'."\n";
				$html .= ($i >= 3) ? '<div class="clear"></div>' : '';

				if ($i >= 3) { 
					$i = 0;
				}
			}
		}
		echo $html;
		?>
		</div><!-- / #fixwrap -->

		<div class="two columns first">
			<h3><?php echo JText::_('Most Popular Articles'); ?></h3>
			<?php
			if (count($this->articles['top']) > 0) {
				$html  = "\t".'<ul class="articles">'."\n";
				foreach ($this->articles['top'] as $row) 
				{
					if (!empty($row->alias)) {
						$link_on = JRoute::_('index.php?option='.$this->option.'&task=article&section='.$row->section.'&category='.$row->category.'&alias='.$row->alias);
					} else {
						$link_on = JRoute::_('index.php?option='.$this->option.'&task=article&section='.$row->section.'&category='.$row->category.'&id='.$row->id);
					}
					$html .= "\t\t".'<li><a href="'. $link_on .'" title="'.JText::_('READ_ARTICLE').'">'.stripslashes($row->title).'</a></li>'."\n";
				}
				$html .= "\t".'</ul>'."\n";
			} else {
				$html  = "\t".'<p>'.JText::_('No articles found.').'</p>'."\n";
			}
			echo $html;
			?>
		</div>
		
		<div class="two columns first">
			<h3><?php echo JText::_('Most Recent Articles'); ?></h3>
			<?php
			if (count($this->articles['new']) > 0) {
				$html  = "\t".'<ul class="articles">'."\n";
				foreach ($this->articles['new'] as $row) 
				{
					if (!empty($row->alias)) {
						$link_on = JRoute::_('index.php?option='.$this->option.'&task=article&section='.$row->section.'&category='.$row->category.'&alias='.$row->alias);
					} else {
						$link_on = JRoute::_('index.php?option='.$this->option.'&task=article&section='.$row->section.'&category='.$row->category.'&id='.$row->id);
					}
					$html .= "\t\t".'<li><a href="'. $link_on .'" title="'.JText::_('READ_ARTICLE').'">'.stripslashes($row->title).'</a></li>'."\n";
				}
				$html .= "\t".'</ul>'."\n";
			} else {
				$html  = "\t".'<p>'.JText::_('No articles found.').'</p>'."\n";
			}
			echo $html;
			?>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section withleft -->