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


class FootNoteMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'Add a footnote, or explicitly display collected footnotes when no args (footnote text) are given.';
		$txt['html'] = '<p>Add a footnote, or explicitly display collected footnotes when no args (footnote text) are given.</p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$note = $this->args;

		$wm =& WikiMacro::getInstance();

		$fn = $wm->footnotes;
		$notes = $wm->footnotes_notes;
		//$nc = $wm->footnotes_count;
		if ($note) {
			// Build and return the link
			if (!isset($notes)) {
				$notes = array();
			}
			if (!isset($fn)) {
				$fn = array();
			}
			
			$p = new WikiParser( 'Footnotes', $this->option, $this->scope, $this->pagename, $this->pageid, $this->filepath, $this->domain );
			
			$note = $p->parse(trim($note));
			
			$wm->footnotes_count++;
			
			if (in_array($note,$notes)) {
				$i = array_search($note, $notes) + 1;
				$k = $wm->footnotes_count;
				//return '<sup><a name="fndef-'.$k.'"></a>['.JRoute::_('index.php?option='.$this->option.a.'scope='.$this->scope.a.'pagename='.$this->pagename).'#fnref-'.$i.' &#91;'.$i.'&#93;]</sup>';
				return '<sup><a name="fndef-'.$k.'"></a><a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$this->pagename).'#fnref-'.$i.'">&#91;'.$i.'&#93;</a></sup>';
			}
			
			//$i = $wm->footnotes_count;
			$i = count($fn) + 1;
			$notes[] = $note;
			$fn[] = '<li><a name="fnref-'.$i.'"></a>'.$note.'</li>';
			//$fn[] = ' # <a name="fnref-'.$i.'"></a>'.$note;
			
			$wm->footnotes_notes = $notes;
			$wm->footnotes = $fn;
			
			//return '<sup><a name="fndef-'.$i.'"></a>['.JRoute::_('index.php?option='.$this->option.a.'scope='.$this->scope.a.'pagename='.$this->pagename).'#fnref-'.$i.' &#91;'.$i.'&#93;]</sup>';
			//return '^[[Anchor(fndef-'.$i.'")]]['.JRoute::_('index.php?option='.$this->option.a.'scope='.$this->scope.a.'pagename='.$this->pagename).'#fnref-'.$i.' '.$i.']^';
			return '<sup><a name="fndef-'.$i.'"></a><a href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->scope.'&pagename='.$this->pagename).'#fnref-'.$i.'">&#91;'.$i.'&#93;</a></sup>';
		} else {
			$html  = '<ol class="footnotes">';
			$html .= implode("\n",$wm->footnotes);
			$html .= '</ol>';
			
			return $html;
		}
	}
}
