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

include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'format'.DS.'abstract.php');

class CitationsFormatIEEE extends CitationsFormatAbstract
{
	public function format($row, $link='none')
	{
		$html = t.'<p>';
		if ($this->keyExistsOrIsNotEmpty('author',$row)) {
			//$xuser =& XFactory::getUser();
			
			$auths = explode(';',$row->author);
			$a = array();
			foreach ($auths as $auth) 
			{
				preg_match('/{{(.*?)}}/s',$auth, $matches);
				if (isset($matches[0]) && $matches[0]!='') {
					$matches[0] = preg_replace( '/{{(.*?)}}/s', '\\1', $matches[0] );
					$aid = 0;
					if (is_numeric($matches[0])) {
						$aid = $matches[0];
					} else {
						$zuser =& JUser::getInstance( trim($matches[0]) );
						if (is_object($zuser)) {
							$aid = $zuser->get('id');
						}
					}
					$auth = preg_replace( '/{{(.*?)}}/s', '', $auth );
					if ($aid) {
						$app =& JFactory::getApplication();
						/*if (is_object($xuser) && in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
							$a[] = '<a href="/whois?query=uidNumber%3D'.$aid.'">'.trim($auth).'</a>';
						} else {*/
							$a[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$aid).'">'.trim($auth).'</a>';
						//}
					} else {
						$a[] = trim($auth);
					}
				} else {
					$a[] = trim($auth);
				}
			}
			$row->author = implode('; ', $a);
	
			$html .= stripslashes($row->author);
		} elseif ($this->keyExistsOrIsNotEmpty('editor',$row)) {
			$html .= stripslashes($row->editor);
		}

		if ($this->keyExistsOrIsNotEmpty('year',$row)) {
			$html .= ' ('.$row->year.')';
		}
		
		if ($this->keyExistsOrIsNotEmpty('title',$row)) {
			if (!$row->url) {
				$html .= ', "'.stripslashes($row->title);
			} else {
				$html .= ', "<a href="'.$this->cleanUrl($row->url).'">'.stripslashes($row->title).'</a>';
			}
		}
		if ($this->keyExistsOrIsNotEmpty('journal',$row) 
		 || $this->keyExistsOrIsNotEmpty('edition',$row)
		 || $this->keyExistsOrIsNotEmpty('booktitle',$row)) {
			$html .= ',';
		}
		$html .= '"';
		if ($this->keyExistsOrIsNotEmpty('journal',$row)) {
			$html .= ' <i>'.stripslashes($row->journal).'</i>';
		} elseif ($this->keyExistsOrIsNotEmpty('booktitle',$row)) {
			$html .= ' <i>'.stripslashes($row->booktitle).'</i>';
		}
		if ($row->type) {
			switch ($row->type) 
			{
				case 'phdthesis': $html .= ' (PhD Thesis)'; break;
				case 'mastersthesis': $html .= ' (Masters Thesis)'; break;
				default: break;
			}
		}
		if ($this->keyExistsOrIsNotEmpty('edition',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.$row->edition;
		}
		if ($this->keyExistsOrIsNotEmpty('chapter',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->chapter);
		}
		if ($this->keyExistsOrIsNotEmpty('series',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->series);
		}
		if ($this->keyExistsOrIsNotEmpty('publisher',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->publisher);
		}
		if ($this->keyExistsOrIsNotEmpty('address',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->address);
		}
		if ($this->keyExistsOrIsNotEmpty('volume',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' <b>'.$row->volume.'</b>';
		}
		if ($this->keyExistsOrIsNotEmpty('number',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' <b>'.$row->number.'</b>';
		}
		if ($this->keyExistsOrIsNotEmpty('pages',$row)) {
			$html .= ': pg. '.$row->pages;
		}
		if ($this->keyExistsOrIsNotEmpty('organization',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->organization);
		}
		if ($this->keyExistsOrIsNotEmpty('institution',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->institution);
		}
		if ($this->keyExistsOrIsNotEmpty('school',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->school);
		}
		if ($this->keyExistsOrIsNotEmpty('location',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->location);
		}
		if ($this->keyExistsOrIsNotEmpty('month',$row)) {
			$html  = $this->grammarCheck( $html, ',' );
			$html .= ' '.$row->month;
		}
		if ($this->keyExistsOrIsNotEmpty('isbn',$row)) {
			$html  = $this->grammarCheck( $html, '.' );
			$html .= ' '.$row->isbn;
		}
		if ($this->keyExistsOrIsNotEmpty('doi',$row)) {
			$html  = $this->grammarCheck( $html, '.' );
			$html .= ' (DOI: '.$row->doi.')';
		}
		$html  = $this->grammarCheck( $html, '.' );
		$html .= '</p>'.n;

		return $html;
	}
}
