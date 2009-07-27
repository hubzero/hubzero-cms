<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class ContribtoolHtml
{
	//----------------------------------------------------------
	// Misc. 
	//----------------------------------------------------------
	
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}
	
	//-----------
	
	public function summary($error, $option, $config, $update=0, $html = '')
	{
		if($error) {
		$html .= ContribtoolHtml::error($error);
		}
		else {
		$html .= '<p>This component is fully setup. There is currently no back-end functionality</p>';
		}
		$html .= '<p><a href="index.php?option='.$option.'&amp;task=setup">Re-run setup</a> | <a href="index.php?option='.$option.'&amp;task=setup&amp;update=1">Reset component parameters</a></p>';
		echo $html;
	}
	
	//-----------
	
	public function setup($option, $setup, $html = '')
	{
		$html = '<p>This component requires ';
		if($setup==1) {
			$html .= 'full database setup';
		}
		else {
			$html .= 'partial database setup';
		}
		$html .= '</p>';
		$html .= '<p><a href="index.php?option='.$option.'&amp;task=setup">Run setup</a> | <a href="index.php?option='.$option.'&amp;task=setup&amp;update=1">Reset component parameters</a></p>';		
		echo $html;
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
}
?>