<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class CitationsFormatter
{
	public function cleanUrl($url)
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);

		return $url;
	}

	public function keyExistsOrIsNotEmpty($key,$row)
	{
		if (isset($row->$key)) {
			if ($row->$key != '' && $row->$key != '0') {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function grammarCheck($html, $punct=',')
	{
		if (substr($html,-1) == '"') {
			$html = substr($html,0,strlen($html)-1).$punct.'"';
		} else {
			$html .= $punct;
		}
		return $html;
	}

	public function formatReference(&$row, $link='none', $highlight='')
	{
		ximport('Hubzero_View_Helper_Html');

		$html = "\t".'<p>';
		if (CitationsFormatter::keyExistsOrIsNotEmpty('author',$row)) {
			$xprofile =& Hubzero_Factory::getProfile();
			$app   =& JFactory::getApplication();
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
						if (is_object($xprofile) && in_array(strtolower($app->getCfg('sitename')), $xprofile->get('admin'))) {
							$a[] = '<a href="index.php?option=com_whois&query=uidNumber%3D'.$aid.'">'.trim($auth).'</a>';
						} else {
							$a[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$aid).'">'.trim($auth).'</a>';
						}
					} else {
						$a[] = trim($auth);
					}
				} else {
					$a[] = trim($auth);
				}
			}
			$row->author = implode('; ', $a);

			$html .= stripslashes($row->author);
		} elseif (CitationsFormatter::keyExistsOrIsNotEmpty('editor',$row)) {
			$html .= stripslashes($row->editor);
		}

		if (CitationsFormatter::keyExistsOrIsNotEmpty('year',$row)) {
			$html .= ' ('.$row->year.')';
		}

		if (CitationsFormatter::keyExistsOrIsNotEmpty('title',$row)) {
			if (!$row->url) {
				$html .= ', "'.stripslashes($row->title);
			} else {
				$html .= ', "<a href="'.CitationsFormatter::cleanUrl($row->url).'">'.Hubzero_View_Helper_Html::str_highlight(stripslashes($row->title),array($highlight)).'</a>';
			}
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('journal',$row)
		|| CitationsFormatter::keyExistsOrIsNotEmpty('edition',$row)
		|| CitationsFormatter::keyExistsOrIsNotEmpty('booktitle',$row)) {
			$html .= ',';
		}
		$html .= '"';
		if (CitationsFormatter::keyExistsOrIsNotEmpty('journal',$row)) {
			$html .= ' <i>'.Hubzero_View_Helper_Html::str_highlight(stripslashes($row->journal),array($highlight)).'</i>';
		} elseif (CitationsFormatter::keyExistsOrIsNotEmpty('booktitle',$row)) {
			$html .= ' <i>'.stripslashes($row->booktitle).'</i>';
		}
		if ($row->type) {
			switch ($row->type)
			{
				case 'phdthesis': $html .= ' ('.JText::_('PhD Thesis').')'; break;
				case 'mastersthesis': $html .= ' ('.JText::_('Masters Thesis').')'; break;
				default: break;
			}
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('edition',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.$row->edition;
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('chapter',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->chapter);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('series',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->series);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('publisher',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->publisher);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('address',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->address);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('volume',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' <b>'.$row->volume.'</b>';
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('number',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' <b>'.$row->number.'</b>';
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('pages',$row)) {
			$html .= ': pg. '.$row->pages;
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('organization',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->organization);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('institution',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->institution);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('school',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->school);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('location',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->location);
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('month',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, ',' );
			$html .= ' '.$row->month;
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('isbn',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, '.' );
			$html .= ' '.$row->isbn;
		}
		if (CitationsFormatter::keyExistsOrIsNotEmpty('doi',$row)) {
			$html  = CitationsFormatter::grammarCheck( $html, '.' );
			$html .= ' ('.JText::_('DOI').': '.$row->doi.')';
		}
		$html  = CitationsFormatter::grammarCheck( $html, '.' );
		$html .= '</p>'."\n";

		return $html;
	}
}
?>