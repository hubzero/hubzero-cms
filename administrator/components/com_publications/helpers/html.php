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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ResourcesHtml'
 * 
 * Long description (if any) ...
 */
class PublicationsHtml
{

	/**
	 * Short description for 'error'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Short description for 'warning'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Short description for 'alert'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	/**
	 * Short description for 'statusKey'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function statusKey()
	{
		?>
			<p><?php echo JText::_('Default version status:'); ?></p>
			<ul class="key">
				<li class="draft"><span>draft</span> = <?php echo JText::_('Draft'); ?></li>
				<li class="ready"><span>ready</span> = <?php echo JText::_('Draft ready'); ?></li>
				<li class="new"><span>pending</span> = <?php echo JText::_('New, awaiting approval'); ?></li>
				<!--<li class="pending"><span>coming</span> = <?php echo JText::_('Published, but is coming'); ?></li>//-->
				<li class="published"><span>current</span> = <?php echo JText::_('Published and is current'); ?></li>
				<li class="archived"><span>archived</span> = <?php echo JText::_('Dark archive'); ?></li>
				<!--<li class="expired"><span>expired</span> = <?php echo JText::_('Published, but expired'); ?></li>//-->
				<li class="unpublished"><span>unpublished</span> = <?php echo JText::_('Unpublished'); ?></li>
				<!--<li class="deleted"><span>deleted</span> = <?php echo JText::_('Deleted'); ?></li>//-->
			</ul>
		<?php
	}

	/**
	 * Short description for 'shortenText'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $text Parameter description (if any) ...
	 * @param      integer $chars Parameter description (if any) ...
	 * @param      integer $p Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function shortenText($text, $chars=300, $p=1)
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) 
		{
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		if ($text == '') 
		{
			$text = '...';
		}
		if ($p) 
		{
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	/**
	 * Short description for 'parseTag'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) 
		{
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} 
		else 
		{
			$match = '';
		}
		return $match;
	}

	/**
	 * Short description for 'niceidformat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function niceidformat($someid)
	{
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	/**
	 * Short description for 'build_path'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $date Parameter description (if any) ...
	 * @param      unknown $pid Parameter description (if any) ...
	 * @param      unknown $vid Parameter description (if any) ...
	 * @param      string $base Parameter description (if any) ...
	 * @param      unknown $filedir Parameter description (if any) ...
	 * @param      unknown $root Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	
	public function buildPath( $pid = NULL, $vid = NULL, $base = '', $filedir = '', $root = 0 )
	{
		if ($vid === NULL or $pid === NULL ) {
			return false;
		}
		if (!$base) 
		{
			$pubconfig =& JComponentHelper::getParams( 'com_publications' );
			$base = $pubconfig->get('webpath');					
		}
		
		if (substr($base, 0, 1) != DS) 
		{
			$base = DS.$base;
		}
		if (substr($base, -1, 1) == DS) 
		{
			$base = substr($base, 0, (strlen($base) - 1));
		}
		
		$pub_dir =  Hubzero_View_Helper_Html::niceidformat( $pid );
		$version_dir =  Hubzero_View_Helper_Html::niceidformat( $vid );
		$path = $base.DS.$pub_dir.DS.$version_dir;
		$path = $filedir ? $path.DS.$filedir : $path;
		$path = $root ? JPATH_ROOT.$path : $path;
		
		return $path;		
	}

	/**
	 * Short description for 'writeRating'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rating Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function writeRating( $rating )
	{
		switch ($rating)
		{
			case 0.5: $class = ' half';      break;
			case 1:   $class = ' one';       break;
			case 1.5: $class = ' onehalf';   break;
			case 2:   $class = ' two';       break;
			case 2.5: $class = ' twohalf';   break;
			case 3:   $class = ' three';     break;
			case 3.5: $class = ' threehalf'; break;
			case 4:   $class = ' four';      break;
			case 4.5: $class = ' fourhalf';  break;
			case 5:   $class = ' five';      break;
			case 0:
			default:  $class = ' none';      break;
		}

		return '<p class="avgrating'.$class.'"><span>Rating: '.$rating.' out of 5 stars</span></p>';
	}

	//----------------------------------------------------------
	// Form <select> builders
	//----------------------------------------------------------

	/**
	 * Short description for 'selectAccess'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $as Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectAccess($as, $value)
	{
		$as = explode(',',$as);
		$html  = '<select name="access">'."\n";
		for ($i=0, $n=count( $as ); $i < $n; $i++)
		{
			$html .= "\t".'<option value="'.$i.'"';
			if ($value == $i) 
			{
				$html .= ' selected="selected"';
			}
			$html .= '>'.trim($as[$i]).'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $groups Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectGroup($groups, $value)
	{
		$html  = '<select name="group_owner"';
		if (!$groups) {
			$html .= ' disabled="disabled"';
		}
		$html .= ' style="max-width: 15em;">'."\n";
		$html .= ' <option value="">'.JText::_('Select group ...').'</option>'."\n";
		if ($groups) 
		{
			foreach ($groups as $group)
			{
				$html .= ' <option value="'.$group->cn.'"';
				if ($value == $group->cn) 
				{
					$html .= ' selected="selected"';
				}
				$html .= '>'.$group->description .'</option>'."\n";
			}
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectSection'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      integer $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectSection($name, $array, $value, $class='', $id)
	{
		$html  = '<select name="'.$name.'" id="'.$name.'" onchange="return listItemTask(\'cb'. $id .'\',\'regroup\')"';
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		$html .= ' <option value="0"';
		$html .= ($id == $value || $value == 0) ? ' selected="selected"' : '';
		$html .= '>'.JText::_('[ none ]').'</option>'."\n";
		foreach ($array as $anode)
		{
			$selected = ($anode->id == $value || $anode->name == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode->id.'"'.$selected.'>'.$anode->name.'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectCategory'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $arr Parameter description (if any) ...
	 * @param      string $name Parameter description (if any) ...
	 * @param      mixed $value Parameter description (if any) ...
	 * @param      string $shownone Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $js Parameter description (if any) ...
	 * @param      string $skip Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectCategory($arr, $name, $value='', $shownone='', $class='', $js='', $skip='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		if ($shownone != '') 
		{
			$html .= "\t".'<option value=""';
			$html .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
			$html .= '>'.$shownone.'</option>'."\n";
		}
		if ($skip) 
		{
			$skips = explode(',',$skip);
		} 
		else 
		{
			$skips = array();
		}
		foreach ($arr as $anode)
		{
			if (!in_array($anode->id, $skips)) 
			{
				$selected = ($value && ($anode->id == $value || $anode->name == $value))
					  ? ' selected="selected"'
					  : '';
				$html .= "\t".'<option value="'.$anode->id.'"'.$selected.'>'.$anode->name.'</option>'."\n";
			}
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectAuthorsNoEdit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $authnames Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectAuthorsNoEdit($authnames, $option)
	{
		$authIDs = array();
		$html = '';
		$i = 1;
		if ($authnames != NULL) 
		{
			$html = '<ul id="author-list">'."\n";
			foreach ($authnames as $authname)
			{
				$authIDs[] = $authname->id;
				$name = $authname->name;

				$org = ($authname->organization) ? htmlentities($authname->organization,ENT_COMPAT,'UTF-8') : '';
				$credit = ($authname->credit) ? htmlentities($authname->credit,ENT_COMPAT,'UTF-8') : '';
				$userid = $authname->user_id ? $authname->user_id : 'unregistered';

				$html .= "\t".'<li id="author_'.$authname->id.'">'. $i . '. ' . $name .' ('.$userid.')';			
				$html .= $org ? ' - <span class="org">'.$org.'</span>' : '';
				if ($credit) 
				{
					$html .= '<br />'.JText::_('Credit').': '.$credit;
				}
				$html .= '</li>'."\n";
				$i++;
			}
			$html.= '</ul>';
		}
		else 
		{
			$html.= '<p class="notice">'.JText::_('No authors listed').'</p>';
		}
		return $html;
	}
	
	/**
	 * Short description for 'selectContent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $primary Parameter description (if any) ...
	 * @param      array $secondary Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectContent($primary, $secondary, $option)
	{		
		$serveas = 'download';
			
		$html = '';
		$html.= '<h4>'.JText::_('Primary content').' ('.count($primary).')</h4>';
		if (count($primary) > 0) 
		{
			$primaryParams = new JParameter($primary[0]->params );
			$serveas = $primaryParams->get('serveas', 'download');
			
			$html .= '<ul class="content-list">'."\n";
			foreach ($primary as $att) 
			{
				$type = $att->type;
				if ($att->type == 'file') 
				{
					$ext = explode('.',$att->path);
					$type = strtoupper(end($ext));
				}
				$title = $att->title ? $att->title : $att->path;
				$html .= '<li>('.$type.') ';
				$html .= $att->title ? $att->title : $att->path;
				$html .= $att->title != $att->path ? '<br /><span class="ctitle">'.$att->path.'</span>' : '';
				$html .= '</li>'."\n";
			}
			$html .= '<li class="summary">'.JText::_('Primary content is served as').' <strong>'.$serveas.'</strong></li>'."\n";
			$html.= '</ul>';
		}
		else 
		{
			$html.= '<p class="notice">'.JText::_('No primary content').'</p>';
		}
		$html.= '<h4>'.JText::_('Supporting Documents').' ('.count($secondary).')</h4>';
		if (count($secondary) > 0) 
		{
			$html .= '<ul class="content-list">'."\n";
			foreach ($secondary as $att) 
			{
				$type = $att->type;
				if ($att->type == 'file') 
				{
					$ext = explode('.',$att->path);
					$type = strtoupper(end($ext));
				}
				$html .= '<li>('.$type.') ';
				$html .= $att->title ? $att->title : $att->path;
				$html .= $att->title != $att->path ? '<br /><span class="ctitle">'.$att->path.'</span>' : '';
				$html .= '</li>'."\n";
			}
			$html.= '</ul>';
		}
		else 
		{
			$html.= '<p>'.JText::_('No supporting documents').'</p>';
		}

		return $html;
	}

	/**
	 * Short description for 'dateToPath'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $date Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function dateToPath( $date )
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) 
		{
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		return $dir_year.DS.$dir_month;
	}

	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------

	/**
	 * Short description for 'dir_name'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $dir Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, '/'));
		if ($lastSlash == strlen($dir)-1) 
		{
			return substr($dir, 0, $lastSlash);
		} 
		else 
		{
			return dirname($dir);
		}
	}

	/**
	 * Short description for 'parse_size'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $size Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function parse_size($size)
	{
		if ($size < 1024) 
		{
			return $size.' bytes';
		} 
		elseif ($size >= 1024 && $size < 1024*1024) 
		{
			return sprintf('%01.2f',$size/1024.0).' <abbr title="kilobytes">Kb</abbr>';
		} 
		else 
		{
			return sprintf('%01.2f',$size/(1024.0*1024)).' <abbr title="megabytes">Mb</abbr>';
		}
	}

	/**
	 * Short description for 'num_files'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $dir Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function num_files($dir)
	{
		$total = 0;

		if (is_dir($dir)) 
		{
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry,0,1) != '.') 
				{
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}
	
	/**
	 * Short description for 'PublicationHelper::getPubStateProperty'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $row Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */	
	public function getPubStateProperty($row, $get = 'class') 
	{	
		$status 	= '';
		$class 		= '';
		$task       = '';
		$now 		= date( "Y-m-d H:i:s" );
		
		switch ($row->state) 
		{
			case 0: 
				$class  = 'unpublished';
				$status = JText::_('COM_PUBLICATIONS_VERSION_UNPUBLISHED');
				$task 	= 'publish';
				break;
				
			case 1: 
				$class   = 'published';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED');
				$task 	 = 'unpublish';
				
				if ($now <= $row->published_up) 
				{
					$class   = 'pending';
					$status  = JText::_('COM_PUBLICATIONS_VERSION_COMING');
				} 
				elseif ($now <= $row->published_down || $row->published_down == "0000-00-00 00:00:00") 
				{
					$class 	 = 'published';
					$status  = JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED');
				} 
				elseif ($row->published_down && $row->published_down != "0000-00-00 00:00:00" && $now > $row->published_down) 
				{
					$status  = JText::_('COM_PUBLICATIONS_VERSION_EXPIRED');
					$class   = 'expired';
				}
				break;
				
			case 2: 
				$class   = 'deleted';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_DELETED');
				break;
				
			case 3:
			default: 
				$class   = 'draft';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_DRAFT');
				break;
				
			case 4: 
				$class   = 'ready';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_READY');
				break;
				
			case 5: 
				$class  = 'new';
				$status = JText::_('COM_PUBLICATIONS_VERSION_PENDING');
				$task = 'publish';
				break;
					
			case 6: 
				$class  = 'archived';
				$status = JText::_('COM_PUBLICATIONS_VERSION_DARKARCHIVE');
				break;	
		}
		
		switch ($get) 
		{
			case 'class':
				return $class;
				break;			
			case 'status':
				return $status;
				break;
			case 'task':
				return $task;
				break;
		}
	}	
}

