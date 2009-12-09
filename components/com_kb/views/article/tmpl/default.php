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

				$html .= "\t".'<li'.$cls.'><a href="'.JRoute::_('index.php?option='.$this->option.'&section='.$row->alias).'">'.KbHtml::xhtml($row->title).'</a></li>'."\n";
			}
		}
		$html .= '</ul>'."\n";
		
		echo $html;
		?>
	</div><!-- / .aside -->
	<div class="subject">
		<?php 
		$html = '';
		if (is_object($this->category) && $this->category->id != '') {
			$html .= '<h3 class="firstheader"><a href="'.JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias).'">'.stripslashes($this->category->title).'</a></h3>'."\n";
			$html .= '<h4>'.stripslashes($this->article->title).'</h4>'."\n";
		} else {
			$html .= '<h3 class="firstheader">'.stripslashes($this->article->title).'</h3>'."\n";
		}
		if ($this->article->introtext) {
			$html .= '<p>'.JText::_('Detailed Question:').'</p>'."\n";
			$html .= '<blockquote>'. stripslashes($this->article->introtext) .'</blockquote>'."\n";
		}
		if ($this->article->fulltext) {
			$html .= stripslashes( $this->article->fulltext );
		}
		
		$total = $this->article->helpful + $this->article->nothelpful;
		
		$html .= "\t".'<div class="faq">'."\n";
		$html .= "\t\t".'<p class="helpful">'."\n";
		$html .= "\t\t\t".'<span>'.JText::sprintf('FOUND_THIS_HELPFUL', $this->article->helpful, $total).'</span> '."\n";
		if (!$this->juser->get('guest')) {
			if ($this->helpful) {
				$html .= "\t\t\t".'<span>'.JText::_('YOU_FOUND_THIS_ARTICLE').' <strong>'.$this->helpful.'</strong></span>'."\n";
			} else {
				$html .= "\t\t\t".'<span>'.JText::_('WAS_THIS_HELPFUL').'</span> '."\n";
				$html .= "\t\t\t".'<a class="yesbutton" href="'.JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'&helpful=yes').'">'.JText::_('YES').'</a> '."\n";
				$html .= "\t\t\t".'<a class="nobutton" href="'.JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'&helpful=no').'">'.JText::_('NO').'</a>'."\n";
			}
		}
		$html .= "\t\t".'</p>'."\n";
		$html .= "\t".'</div><!-- / .faq -->'."\n";
		
		echo $html;
		?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section withleft -->