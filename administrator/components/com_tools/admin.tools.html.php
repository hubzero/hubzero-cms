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

if (!defined("n")) {

/**
 * Description for '"t"'
 */
	define("t","\t");

/**
 * Description for '"n"'
 */
	define("n","\n");

/**
 * Description for '"br"'
 */
	define("br","<br />");

/**
 * Description for '"sp"'
 */
	define("sp","&#160;");

/**
 * Description for '"a"'
 */
	define("a","&amp;");
}

/**
 * Short description for 'ToolsHtml'
 * 
 * Long description (if any) ...
 */
class ToolsHtml
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
	 * Short description for 'hInput'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      string $value Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function hInput($name, $value='', $id='')
	{
		$html  = '<input type="hidden" name="'.$name.'" value="'.$value.'"';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= ' />'."\n";
		return $html;
	}

	/**
	 * Short description for 'sInput'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      string $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function sInput($name, $value='')
	{
		return '<input type="submit" name="'.$name.'" value="'.$value.'" />'."\n";
	}

	/**
	 * Short description for 'td'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $content Parameter description (if any) ...
	 * @param      string $attribs Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'admlink'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $vars Parameter description (if any) ...
	 * @param      string $text Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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
				$url .= ($option != $v) ? '&'.$k.'='.$v : '';
			}
			$html .= '<a href="'.$url.'" title="'.$text.'">'.$name.'</a>';
		}
		return $html;
	}

	//----------------------------------------------------------
	// ListEdit widget.
	//----------------------------------------------------------

	/**
	 * Short description for 'listedit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $list Parameter description (if any) ...
	 * @param      array $hidden Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'table'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $rows Parameter description (if any) ...
	 * @param      string $header Parameter description (if any) ...
	 * @param      string $middle Parameter description (if any) ...
	 * @param      string $trailer Parameter description (if any) ...
	 * @param      string $tail_row Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'updateform'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $table Parameter description (if any) ...
	 * @param      string $bit Parameter description (if any) ...
	 * @param      string $refs Parameter description (if any) ...
	 * @param      mixed &$row Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'tableHeader'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $headers Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'delete_button'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $table Parameter description (if any) ...
	 * @param      string $value Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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