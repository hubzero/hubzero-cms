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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class JobsHtml 
{
	//-----------

	public function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}
	
	//-----------
	
	public function confirmscreen($returnurl, $actionurl, $action='cancelsubscription')
	{
		$html =  '<div class="confirmwrap">'.n;
		$html.=  t.'<div class="confirmscreen">'.n;
		$html.=  t.'<p class="warning">'.JText::_('CONFIRM_ARE_YOU_SURE').' ';
		if($action=='cancelsubscription') {
			$html.= strtolower(JText::_('SUBSCRIPTION_CANCEL_THIS'));
		} 
		else if($action=='withdrawapp') {
			$html.=  JText::_('APPLICATION_WITHDRAW');
		} 
		else {
			$html.= JText::_('ACTION_PERFORM_THIS'); 
		}
		$yes = strtoupper(JText::_('YES'));
		$yes.= $action=='cancelsubscription' ? ', '.JText::_('ACTION_CANCEL_IT') : '';
		$yes.= $action=='withdrawapp' ? ', '.JText::_('ACTION_WITHDRAW') : '';
		
		$no = strtoupper(JText::_('NO'));
		$no.= $action=='cancelsubscription' ? ', '.JText::_('ACTION_DO_NOT_CANCEL') : '';
		$no.= $action=='withdrawapp' ? ', '.JText::_('ACTION_DO_NOT_WITHDRAW') : '';
		
		$html.= '?</p>'.n;
		$html.=  t.'<p><span class="yes"><a href="'.$actionurl.'">'.$yes.'</a></span> <span class="no"><a href="'.$returnurl.'">'.$no.'</a></span></p>';
		$html.=  t.'</div>'.n;
		$html.=  '</div>'.n;
		
		return $html;
	}
	
	//-----------

	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}
	//------------
	public function wikiHelp() 
	{	
		$out  = '<table class="wiki-reference" summary="Wiki Syntax Reference">'.n;
		$out .= '<caption>Wiki Syntax Reference</caption>'.n;
		$out .= '	<tbody>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td>\'\'\'bold\'\'\'</td>'.n;
		$out .= '			<td><b>bold</b></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td>\'\'italic\'\'</td>'.n;
		$out .= '			<td><i>italic</i></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td>__underline__</td>'.n;
		$out .= '			<td><span style="text-decoration:underline;">underline</span></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td>{{{monospace}}}</td>'.n;
		$out .= '			<td><code>monospace</code></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td>~~strike-through~~</td>'.n;
		$out .= '		<td><del>strike-through</del></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td>^superscript^</td>'.n;
		$out .= '			<td><sup>superscript</sup></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td>,,subscript,,</td>'.n;
		$out .= '			<td><sub>subscript</sub></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '	</tbody>'.n;
		$out .= '</table>'.n;
		
		return $out;
	}
}
?>