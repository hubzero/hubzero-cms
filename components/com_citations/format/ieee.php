<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'format'.DS.'abstract.php');

/**
 * Short description for 'CitationsFormatIEEE'
 * 
 * Long description (if any) ...
 */
class CitationsFormatIEEE extends CitationsFormatAbstract
{

	/**
	 * Short description for 'format'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $row Parameter description (if any) ...
	 * @param      string $link Parameter description (if any) ...
	 * @param      string $highlight Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function format($row, $link='none', $highlight='')
	{
		$html = "\t".'<p>';
		if ($this->keyExistsOrIsNotEmpty('author',$row)) {

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
							$a[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$aid).'">'.trim($auth).'</a>';
					} else {
						$a[] = trim($auth);
					}
				} else {
					$a[] = trim($auth);
				}
			}
			$row->author = implode('; ', $a);

			$html .= Hubzero_View_Helper_Html::str_highlight(stripslashes($row->author), array($highlight));
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
				$html .= ', "<a href="'.$this->cleanUrl($row->url).'">'.Hubzero_View_Helper_Html::str_highlight(stripslashes($row->title), array($highlight)).'</a>';
			}
		}
		if ($this->keyExistsOrIsNotEmpty('journal',$row)
		 || $this->keyExistsOrIsNotEmpty('edition',$row)
		 || $this->keyExistsOrIsNotEmpty('booktitle',$row)) {
			$html .= ',';
		}
		$html .= '"';
		if ($this->keyExistsOrIsNotEmpty('journal',$row)) {
			$html .= ' <i>'.Hubzero_View_Helper_Html::str_highlight(stripslashes($row->journal), array($highlight)).'</i>';
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
		$html .= '</p>'."\n";

		return $html;
	}
}

