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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class ToolsHtml
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function hInput($name, $value='', $id='')
	{
		$html  = '<input type="hidden" name="'.$name.'" value="'.$value.'"';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= ' />'."\n";
		return $html;
	}

	//-----------

	public function sInput($name, $value='')
	{
		return '<input type="submit" name="'.$name.'" value="'.$value.'" />'."\n";
	}

	//-----------

	public function td($content, $attribs='')
	{
		$html  = '   <td';
		$html .= ($attribs) ? ' '.$attribs : '';
		$html .= '>'.$content.'</td>'."\n";
		return $html;
	}
	
	//----------------------------------------------------------
	// An administrative active link.
	//----------------------------------------------------------

	public function admlink( $name, $vars, $text, $option ) 
	{
		$html = '';
		if (0) { // use POST
			$html .= '<a href="#"'."\n";
			$html .= '   onclick="document.adm.action=\'index.php\';'."\n";
			foreach ($vars as $k => $v) 
			{
				$html .= '            document.adm.'.$k.'.value=\''.$v.'\';'."\n";
			}
			$html .= '            document.adm.submit();"'."\n";
			$html .= '   title="'.$text.'">'.$name.'</a>';
		} else { // use GET
			$url = 'index.php?option='.$option;
			foreach ($vars as $k => $v) 
			{
				$url .= '&'.$k.'='.$v;
			}
			$html .= '<a href="'.$url.'" title="'.$text.'">'.$name.'</a>';
		}
		return $html;
	}
	
	//----------------------------------------------------------
	// ListEdit widget.
	//----------------------------------------------------------
	
	public function listedit( $list, $hidden ) 
	{
		$html = '<ul class="ntools">'."\n";
		foreach ($list as $key => $value) 
		{
			$html .= ' <li>';
			if ($value != '0') { 
				$html .= '<b>'; 
			}
			if (0) { // POST
				$html .= "<a href='#'\n";
				$html .= "onclick=\"document.adm.action='index.php';\n";
				$html .= "         document.adm.item.value='$key';\n";
				foreach ($hidden as $k => $v) 
				{
					$html .= "         document.adm.$k.value='$v';\n";
				}
				$html .= "         document.adm.submit();\"\n";
			} else { // GET
				$html .= '<a href="index.php';
				$prefix = '?';
				foreach ($hidden as $k => $v) 
				{
					if ($v != '') {
						$html .= $prefix.$k.'='.$v;
						$prefix = '&';
					}
				}
				$html .= $prefix.'item='.$key.'" ';
			}
			$html .= 'title="Toggle '.$key.' ('.$value.')">'.$key.'</a>';
			if ($value != '0') { 
				$html .= '</b>'; 
			}
			$html .= '</li>'."\n";
		}
		$html .= '</ul>'."\n";
		return $html;
	}

	//----------------------------------------------------------
	// Table widget.
	//----------------------------------------------------------

	public function table( $rows, $header, $middle, $trailer, $tail_row ) 
	{
		$html  = '<table>'."\n";
		$html .= '  <tr>'."\n"; 
		$html .= $header(); 
		$html .= '  </tr>'."\n";
		$html .= ' <tbody>'."\n"; 
		for($i=0; $i < count($rows); $i++) 
		{
			$html .= '  <tr>'."\n";
			$html .= $middle($rows[$i]); 
			$html .= '  </tr>'."\n";
		}
		if ($tail_row != '') {
			$html .= '  <tr>'."\n";
			$html .= $trailer($tail_row); 
			$html .= '  </tr>'."\n";
		}
		$html .= ' </tbody>'."\n"; 
		$html .= '</table>'."\n";
		return $html;
	}
	
	//-----------
	
	public function updateform($table, $bit, $refs, &$row, $option)
	{
		$html  = '<tr>'."\n";
		$html .= '<form name="update_'.$table.'" method="get" action="index.php">'."\n";
		$html .= t.ToolsHtml::hInput('option',$option);
		$html .= t.ToolsHtml::hInput('admin',1);
		$html .= t.ToolsHtml::hInput('table',$table);
		$html .= t.ToolsHtml::hInput('op','update');
		$html .= t.ToolsHtml::hInput('filter_'.$table,$row->name);
		$html .= '<td><input type="text" name="name" size="10" value="'.$row->name.'" />'."\n";
		$html .= '<td> '.$bit."\n";
		$html .= '<td><input type="text" name="description" size="20" value="'.$row->description.'" />'."\n";
		$html .= '<td> '.$refs."\n";
		$html .= '<td><input type="submit" name="update" value="Update" />'."\n";
		$html .= '<form>'."\n";
		$html .= '</tr>'."\n";
		
		return $html;
	}
	
	//-----------

	public function tableHeader($headers)
	{
		$html  = ' <thead>'."\n";
		$html .= '  <tr>'."\n";
		for ($i=0, $n=count( $headers ); $i < $n; $i++) 
		{
			$html .= '   <th>'.$headers[$i].'</th>'."\n";
		}
		$html .= '  </tr>'."\n";
		$html .= ' </thead>'."\n";
		return $html;
	}
	
	//-----------
	
	public function delete_button($name, $table, $value, $option)
	{
		$html  = '<form name="delete_'.$value.'" method="get" action="index.php">'."\n";
		$html .= t.ToolsHtml::hInput('option',$option);
		$html .= t.ToolsHtml::hInput('admin',1);
		$html .= t.ToolsHtml::hInput('table',$table);
		$html .= t.ToolsHtml::hInput('op','delete');
		$html .= t.ToolsHtml::hInput($name,$value);
		$html .= t.'<input type="submit" name="delete" value="Delete" />'."\n";
		$html .= '</form>'."\n";
		return $html;
	}
}
?>