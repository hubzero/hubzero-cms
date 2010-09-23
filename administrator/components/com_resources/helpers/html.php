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


class ResourcesHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}

	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------

	public function statusKey()
	{
		?>
			<p><?php echo JText::_('Published status: (click icon above to toggle state)'); ?></p>
			<ul class="key">
				<li class="draftinternal"><span>draft (internal)</span> = <?php echo JText::_('Draft (internal production)'); ?></li>
				<li class="draftexternal"><span>draft (external)</span> = <?php echo JText::_('Draft (user created)'); ?></li>
				<li class="new"><span>new</span> = <?php echo JText::_('New, awaiting approval'); ?></li>
				<li class="pending"><span>pending</span> = <?php echo JText::_('Published, but is Coming'); ?></li>
				<li class="published"><span>current</span> = <?php echo JText::_('Published and is Current'); ?></li>
				<li class="expired"><span>finished</span> = <?php echo JText::_('Published, but has Finished'); ?></li>
				<li class="unpublished"><span>unpublished</span> = <?php echo JText::_('Unpublished'); ?></li>
				<li class="deleted"><span>deleted</span> = <?php echo JText::_('Delete/Removed'); ?></li>
			</ul>
		<?php
	}
	
	//-----------
	
	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		if ($text == '') {
			$text = '...';
		}
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}
	
	//-----------

	public function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} else {
			$match = '';
		}
		return $match;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
	
	//-----------
	
	public function build_path( $date, $id, $base='' )
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		if ($date) {
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} else {
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = ResourcesHtml::niceidformat( $id );
		
		$path = $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
	
		//return $base.DS.$dir_id;
		return $path;
	}
	
	//-----------

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
	
	public function selectAccess($as, $value)
	{
		$as = explode(',',$as);
		$html  = '<select name="access">'."\n";
		for ($i=0, $n=count( $as ); $i < $n; $i++)
		{
			$html .= "\t".'<option value="'.$i.'"';
			if ($value == $i) {
				$html .= ' selected="selected"';
			}
			$html .= '>'.trim($as[$i]).'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}
	
	//-----------
	
	public function selectGroup($groups, $value)
	{
		$html  = '<select name="group_owner"';
		if (!$groups) {
			$html .= ' disabled="disabled"';
		}
		$html .= '>'."\n";
		$html .= ' <option value="">'.JText::_('Select group ...').'</option>'."\n";
		if ($groups) {
			foreach ($groups as $group)
			{
				$html .= ' <option value="'.$group->cn.'"';
				if ($value == $group->cn) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.$group->description .'</option>'."\n";
			}
		}
		$html .= '</select>'."\n";
		return $html;
	}

	//-----------

	public function selectSection($name, $array, $value, $class='', $id)
	{
		$html  = '<select name="'.$name.'" id="'.$name.'" onchange="return listItemTask(\'cb'. $id .'\',\'regroup\')"';
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		$html .= ' <option value="0"';
		$html .= ($id == $value || $value == 0) ? ' selected="selected"' : '';
		$html .= '>'.JText::_('[ none ]').'</option>'."\n";
		foreach ($array as $anode) 
		{
			$selected = ($anode->id == $value || $anode->type == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode->id.'"'.$selected.'>'.$anode->type.'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	//-----------

	public function selectType($arr, $name, $value='', $shownone='', $class='', $js='', $skip='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		if ($shownone != '') {
			$html .= "\t".'<option value=""';
			$html .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
			$html .= '>'.$shownone.'</option>'."\n";
		}
		if ($skip) {
			$skips = explode(',',$skip);
		} else {
			$skips = array();
		}
		foreach ($arr as $anode) 
		{
			if (!in_array($anode->id, $skips)) {
				$selected = ($value && ($anode->id == $value || $anode->type == $value))
					  ? ' selected="selected"'
					  : '';
				$html .= "\t".'<option value="'.$anode->id.'"'.$selected.'>'.$anode->type.'</option>'."\n";
			}
		}
		$html .= '</select>'."\n";
		return $html;
	}
	
	//-----------

	public function selectAuthors($rows, $authnames, $attribs, $option)
	{
		$authIDs = array();
		
		$document =& JFactory::getDocument();
		$document->addScript('components/'.$option.'/xsortables.js');
		$document->addScript('components/'.$option.'/resources.js');
		
		$html  = 'User ID: <input type="text" name="authid" id="authid" value="" /> ';
		$html .= "\t\t".'<select name="authrole" id="authrole">'."\n";
		$html .= "\t\t\t".'<option value="">Role...</option>'."\n";
		$html .= "\t\t\t".'<option value="submitter">submitter</option>'."\n";
		$html .= "\t\t\t".'<option value="editor">editor</option>'."\n";
		$html .= "\t\t".'</select>'."\n";
		$html .= '<input type="button" name="addel" id="addel" onclick="HUB.Resources.addAuthor();" value="'.JText::_('Add').'" />';
		$html .= '<ul id="author-list">'."\n";
		if ($authnames != NULL) {
			foreach ($authnames as $authname) 
			{
				if ($authname->name) {
					$name = $authname->name;
				} else {
					$name = $authname->givenName .' ';
					if ($authname->middleName != null) {
						$name .= $authname->middleName .' ';
					}
					$name .= $authname->surname;
				}
			
				$authIDs[] = $authname->id;
				
				$org = ($authname->organization) ? htmlentities($authname->organization,ENT_COMPAT,'UTF-8') : $attribs->get( $authname->id, '' );
				
				$html .= "\t".'<li id="author_'.$authname->id.'"><span class="handle">'.JText::_('DRAG HERE').'</span> '. $name .' ('.$authname->id.') [ <a href="#" onclick="HUB.Resources.removeAuthor(this);return false;">'.JText::_('remove').'</a> ]';
				//$html .= '<br />'.JText::_('Affiliation').': <input type="text" name="attrib['.$authname->id.']" value="'. $attribs->get( $authname->id, '' ) .'" />';
				$html .= '<br />'.JText::_('Affiliation').': <input type="text" name="'.$authname->id.'_organization" value="'. $org .'" />';
				$html .= "\t\t".'<select name="'.$authname->id.'_role">'."\n";
				$html .= "\t\t\t".'<option value="">Role...</option>'."\n";
				$html .= "\t\t\t".'<option value="submitter"';
				$html .= ($authname->role == 'submitter') ? ' selected="selected"' : '';
				$html .= '>submitter</option>'."\n";
				$html .= "\t\t\t".'<option value="editor"';
				$html .= ($authname->role == 'editor') ? ' selected="selected"' : '';
				$html .= '>editor</option>'."\n";
				$html .= "\t\t".'</select>'."\n";
				$html .= '<input type="hidden" name="'.$authname->id.'_name" value="'. htmlentities($name,ENT_COMPAT,'UTF-8') .'" />';
				//$html .= '<input type="hidden" name="'.$authname->id.'_organization" value="'. htmlentities($authname->organization,ENT_COMPAT,'UTF-8') .'" />';
				$html .= '</li>'."\n";
			}
		}
		$authIDs = implode(',',$authIDs);
		$html .= '</ul>'."\n";
		$html .= '<input type="hidden" name="old_authors" id="old_authors" value="'.$authIDs.'" />'."\n";
		$html .= '<input type="hidden" name="new_authors" id="new_authors" value="'.$authIDs.'" />'."\n";
		
		return $html;
	}

	//-----------
	
	public function dateToPath( $date ) 
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		return $dir_year.DS.$dir_month;
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, '/'));
		if ($lastSlash == strlen($dir)-1) {
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	//-----------

	public function parse_size($size)
	{
		if ($size < 1024) {
			return $size.' bytes';
		} else if ($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' <abbr title="kilobytes">Kb</abbr>';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' <abbr title="megabytes">Mb</abbr>';
		}
	}

	//-----------

	public function num_files($dir)
	{
		$total = 0;

		if (is_dir($dir)) {
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}
}
