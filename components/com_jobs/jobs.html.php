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

	public function info( $msg, $tag='p' )
	{
		return '<'.$tag.' class="info">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return '<script type="text/javascript"> alert(\''.$msg.'\'); window.history.go(-1); </script>'.n;
	}
	
	//-----------

	public function statusmsg ( $msg, $subclass, $tag='p' )
	{
		return '<'.$tag.' class="statusmsg '.$subclass.'">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------

	public function tableRow($h,$c='', $e, $class='')
	{
		$html  = t.'  <tr';
		if($class) {
		$html .= t.'  class="'.$class.'"';
		}
		$html .= '>'.n;
		$html .= t.'   <th>'.$h.'</th>'.n;
		$html .= t.'   <td>';
		//$html .= ($c) ? $c : '&nbsp;';
		$html .= $c;
		$html .= '</td>'.n;
		$html .= t.'   <td>';
		$html .= ($e) ? $e : '&nbsp;';
		$html .= '</td>'.n;
		$html .= t.'  </tr>'.n;
		
		return $html;
	}
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
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}
	
	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>'.n;
		$html .= $txt.n;
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}
	//-----------
	
	public function aside($txt, $id='')
	{
		return JobsHTML::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return JobsHtml ::div($txt, 'subject', $id);
	}
	
	//-----------
	
	public function confirmscreen($returnurl, $actionurl, $action='cancelsubscription')
	{
		$html =  '<div class="confirmwrap">'.n;
		$html.=  t.'<div class="confirmscreen">'.n;
		$html.=  t.'<p class="warning">'.JText::_('Are you sure you want to').' ';
		if($action=='cancelsubscription') {
			$html.=  JText::_('cancel this subscription');
		} 
		else if($action=='withdrawapp') {
			$html.=  JText::_('withdraw your application');
		} 
		else {
			$html.= JText::_('perform this action'); 
		}
		$yes = JText::_('YES');
		$yes.= $action=='cancelsubscription' ? ', '.JText::_('cancel it') : '';
		$yes.= $action=='withdrawapp' ? ', '.JText::_('withdraw') : '';
		
		$no = JText::_('NO');
		$no.= $action=='cancelsubscription' ? ', '.JText::_('do not cancel') : '';
		$no.= $action=='withdrawapp' ? ', '.JText::_('do not withdraw') : '';
		
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
	
	//-----------
	
	public function browseForm_Jobs($option, $filters, $admin, $totalnote)
	{
		$sortbys = array('category'=>JText::_('CATEGORY'),'opendate'=>JText::_('Posted Date'),'type'=>JText::_('TYPE'));
		$filterbys = array('all'=>JText::_('ALL'),'open'=>JText::_('ACTIVE'),'closed'=>JText::_('EXPIRED'));
		
		$html  = '<div class="jobs_controls">'.n;
		//$html .= t.'<form method="get" action="'.JRoute::_('index.php?option='.$option.a.'task=browse').'">'.n;
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.t.'<label> '.JText::_('Search by keywords').':<span class="questionmark tooltips" title="Keywords Search :: Use skill and action keywords separated by commas, e.g. XML, web, MBA etc."></span> '.n;
		$html .= t.t.t.'<input type="text" name="q" value="'.$filters['search'].'" />'.n;
		$html .= t.t.t.'</label> '.n;
		$html .= t.t.t.'&nbsp;&nbsp;<label>'.JText::_('SORTBY').':'.n;
		$html .= JobsHtml::formSelect('sortby', $sortbys, $filters['sortby'], '', '');
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.t.'<input type="hidden" name="limitstart" value="0" />'.n;
		$html .= t.t.t.'<input type="hidden" name="performsearch" value="1" />'.n;
		$html .= t.t.'</fieldset>'.n;
		$html .= t.t.t.'<div class="note_total">'.$totalnote.'</div>'.n;
		//$html .= t.'</form>'.n;		
		$html .= '</div>'.n;
		
		
		return $html;
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
			$text = $text.' &#8230;';
		}
		
		if ($text == '') {
			$text = '&#8230;';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}
	
	//------------
	public function wikiHelp() 
	{	
		$out  = '<table class="wiki-reference" summary="Wiki Syntax Reference">'.n;
		$out .= '<caption>Wiki Syntax Reference</caption>'.n;
		$out .= '	<tbody>'.n;
		/*$out .= '		<tr>'.n;
		$out .= '			<td> = heading =</td>'.n;
		$out .= '			<td><h1>heading</h1></td>'.n;
		$out .= '		</tr>'.n;
		$out .= '		<tr>'.n;
		$out .= '			<td> == subheading ==</td>'.n;
		$out .= '			<td><h2>subheading</h2></td>'.n;
		$out .= '		</tr>'.n;
		*/
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