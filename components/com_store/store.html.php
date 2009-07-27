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

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class StoreHtml 
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
	
	public function alert( $msg )
	{
		return '<script type="text/javascript"> alert(\''.$msg.'\'); window.history.go(-1); </script>'.n;
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
	
	public function formSelect($name, $idname, $array, $value, $class='', $jscall='')
	{
		$out  = '<select name="'.$name.'" id="'.$idname.'"';
		$out .= ($class) ? ' class="'.$class."" : ''."";
		$out .= ($jscall) ? ' onchange="'.$jscall.'">'."\n" : '>'."\n";
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' 			<option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}
	//-----------
	
	public function selectArray($name, $array, $value, $class='', $js='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $anode) 
		{
			$selected = ($anode == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode.'"'.$selected.'>'.stripslashes($anode).'</option>'.n;
		}
		$html .= '</select>'.n;
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
			$text = $text.' ...';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	//-----------
	
	public function menu($task, $option, $infolink)
	{
		$cls1 = '';
		$cls2 = '';
		$cls3 = '';
		$cls4 = '';
		switch($task) 
		{
			case 'cart':      	$cls2 = ' class="active"'; break;
			case 'start':      	$cls1 = ' class="active"'; break;
			default:            						 ; break;
		}
		
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		
		$html  = t. '	<ul>'.n;
		$html .= t. ' 		<li'.$cls1.'><a href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('STOREFRONT').'</a></li>'.n;
		$html .= t. ' 		<li'.$cls2.'><a href="'.JRoute::_('index.php?option='.$option.a.'task=cart').'">'.JText::_('CART').'</a></li>'.n;
		$html .= t. ' 		<li><a href="'.$infolink.'">'.JText::_('EARN').'</a></li>'.n;
		$html .= t. ' 		<li><a href="'.JRoute::_('index.php?option=com_feedback&task=report').'">'.JText::_('REPORT').'</a></li>'.n;
		$html .= t. ' 		<li><a href="'.JRoute::_('index.php?option=com_feedback').'">'.JText::_('FEEDBACK').'</a></li>'.n;
		$html .= t. '	</ul>'.n;
			
		return $html;
	}

	//-----------

	public function introduction($title, $items, $featured, $option, $filters, $infolink, $html='')
	{		
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		
		$html = StoreHtml::div( StoreHtml::hed( 2, $title ), '', 'content-header' );
		
		$html .= '<div id="content-header-extra">'.n;
		$html .= ' <ul id="useroptions">'.n;
		$html .= t.' <li class="last"><a href="'.JRoute::_('index.php?option=com_store'.a.'task=cart').'" class="shoppingcart">'.JText::_('CART').'</a></li>'.n;
		$html .= ' </ul>'.n;
		$html .= '</div>'.n;
		$html .= '<div class="main section">'.n;
		$html .= StoreHtml::title(3, JText::_('SPEND').' '.$hubShortName.' '.JText::_('MERCHANDISE').' '.JText::_('AND').' '.JText::_('PREMIUM_SERVICES'),'firstheader');
		$html .= $items;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}

	//-----------
	public function cart($title, $cartitems, $option, $funds, $cost, $msg='', $infolink, $juser)
	{
		
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$html = StoreHtml::div( StoreHtml::hed( 2, $title ), '', 'content-header' );
		$html .= '<div id="content-header-extra">'.n;
		$html .= ' <ul id="useroptions">'.n;
		$html .= t.' <li><a href="'.JRoute::_('index.php?option='.$option).'" class="storefront">'.JText::_('STOREFRONT').'</a></li>'.n;
		$html .= t.' <li class="last"><a href="'.DS.'members'.DS.$juser->get('id').DS.'points"  class="mypoints">'.JText::_('My Points').'</a></li>'.n;
		$html .= ' </ul>'.n;
		$html .= '</div>'.n;
		$html .= '<div class="main section">'.n;
		$html .= t.' <div id="cartcontent">'.n;
		if($msg) {
		$html .= t.'<p class="help">'.$msg.'</p>';
		}
		$html .= $cartitems;
		$html .= t.' </div>';
		$html .= StoreHtml:: funds_summary($funds, $cost, $hubShortName, $infolink);
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}

	
	//-----------

	public function title($level, $words, $class='') 
	{
		$html  = t.t.'<h'.$level;
		$html .= ($class) ? ' class="'.$class.'"' : '';
		$html .= '>'.$words.'</h'.$level.'>'.n;
		return $html;
	}


	//-----------

	public function htmlItemList(&$rows, $option, $infolink)
	{
	if ($rows) {
			$html   = t.'<p>';
			$html  .= (count($rows)==1) ? JText::_('THERE_IS').' ': JText::_('THERE_ARE').' ';
			$html  .= (count($rows)>0) ? count($rows) : JText::_('NO');
			$html  .= (count($rows)==1) ? ' '.JText::_('ITEM'): ' '.JText::_('ITEMS');
			$html  .= ' '.JText::_('AVAILABLE').'.</p>'.n;
			$html  .= t.'<ul class="storeitems">'.n;
			foreach ($rows as $row) 
			{
			$row->available = ($row->available) ?  1 : 0;
			$onemonth 		= date( 'Y-m-d H:i:s', time() - (30 * 24 * 60 * 60) );
			$new = ($row->created > $onemonth) ? 1 : 0;

			$avail = ($row->available) ?  '<span class="yes">'.JText::_('INSTOCK').'</span>' : '<span class="no">'.JText::_('SOLDOUT').'</span>';	
				$html .= t.' 	<li ';
				if($row->featured) {
				$html .= 'class="featured" ';
				} else if($new) {
				$html .= 'class="new" ';
				}
				$html .= '>'.n;
				$html .= t.'		<div class="imageholder">'.n;
				$html .= StoreHtml::productimage( $row->id, $row->root, $row->webpath, $row->title, $row->category );
				$html .= t.'		</div>'.n;
				$html .= t.'		<div class="infoholder">'.n;
				$html .= t.' 			<h4>'.$row->title.'</h4>'.n;
				$html .= t.'			<p>'.StoreHtml::shortenText($row->description, $chars=200, $p=0).'</p>'.n;
				$html .= t.'			<p>';
				if($row->category ) {
				$html .= t.'<span class="sizes">'.JText::_('CATEGORY').': '.$row->category.'</span>';
				}
				if($row->size && $row->available) {
				$html .= t.'<span class="sizes">'.JText::_('SIZES').': '.$row->size.'</span>';
				}
							
				if($row->category != 'service') {
				$html .= t.$avail;
				}
				$html .= '</p'.n;
				$html .= t.'		</div>'.n;
				$html .= t.'		<div class="purchase">'.n;
				$html .= t.'			<span class="price"><a href="'.$infolink.'" title="'.JText::_('WHAT_ARE_POINTS').'?">&nbsp;</a>'.$row->price.'</span>'.n;
				if($row->available) {
				$html .= t.'			<a href="'.JRoute::_('index.php?option='.$option.a.'task=cart').'?action=add'.a.'item='.$row->id.'" class="button buy"></a>'.n;
				}
				else {
				$html .= t.'			<span class="button buy_disabled">&nbsp;</span>'.n;
				}
				$html .= t.'		</div>'.n;
				$html .= t.' </li>'.n;
			}
			$html .= t.'</ul>'.n;
		} else {
			$html  = t.'<p>'.JText::_('NO_PRODUCTS').'.</p>'.n;
		}
		
		
		return $html;				
		
	}

	//-----------
	public function htmlCartList(&$rows, $option, $funds, $cost, $infolink)
	{
	if ($rows) {
			if(count($rows) > 1) {
			$html   = t.'<p>'.JText::_('THERE_ARE').' '.count($rows).' '.JText::_('ITEMS').' '.JText::_('IN_CART').'.';
			}
			else {
			$html   = t.'<p>'.JText::_('THERE_IS').' 1 '.JText::_('ITEM').' '.JText::_('IN_CART').'.';
			}
			$html  .=  t.'<a href="'.JRoute::_('index.php?option='.$option).'" >'.JText::_('CONTINUE').'</a>.</p>'.n;
			$html  .= t.' <form id="myCart" method="post" action="index.php?option='.$option.'">'.n;
			$html .= t.'	 <fieldset>'.n;
			$html .= t.'	 <input type="hidden"  name="action" value="" />'.n;
			$html .= t.'	 <input type="hidden"  name="task" value="checkout" />'.n;
			$html .= t.'	 <input type="hidden" id="funds" value="'.$funds.'" />'.n;
			$html .= t.'	 <input type="hidden" id="cost" value="'.$cost.'" />'.n;
			$html .= t.'	 </fieldset>'.n;
			$html  .= t.'	<table id="tktlist">'.n;
			$html  .= t.' 		<thead>'.n;
			$html  .= t.' 			<tr>'.n;
			$html  .= t.' 			 <th>'.ucfirst(JText::_('ITEM')).'</th>'.n;
			$html  .= t.' 			 <th>'.JText::_('AVAILABILITY').'</th>'.n;
			$html  .= t.' 			 <th>'.JText::_('QUANTITY').'*</th>'.n;
			$html  .= t.' 			 <th>'.JText::_('SIZE').'</th>'.n;
			$html  .= t.' 			 <th><a href="'.$infolink.'" title="'.JText::_('WHAT_ARE_POINTS').'?" class="coin">&nbsp;</a></th>'.n;
			$html  .= t.' 			</tr>'.n;
			$html  .= t.'		</thead>'.n;
			$html  .= t.'		<tbody>'.n;
			$total = 0;	
			foreach ($rows as $row) 
			{
			$avail = ($row->available) ?  '<span class="yes">'.JText::_('INSTOCK').'</span>' : '<span class="no">'.JText::_('SOLDOUT').'</span>';
			$price = $row->price*$row->quantity;
			if($row->available) { // do not add if not available
			$total = $total+$price;
			}
			$sizes = array(); // build size options
			if($row->sizes && count($row->sizes) > 0) {
				foreach($row->sizes as $rs) {
					if(trim($rs) != '') {
					$sizes[$rs]=$rs;
					}
				}
				$selectedsize = ($row->selectedsize) ? $row->selectedsize : $row->sizes[0];
		
			}
			$html .= t.' 			<tr> '.n;
			$html .= t.' 			 <td>'.$row->title.'</td>'.n;
			$html .= t.' 			 <td>';
			if($row->category!='service') {
			$html .= $avail;
			}
			$html .='</td>'.n;
			$html .= t.' 			 <td class="quantityspecs">';
			if($row->category!='service') {
			$html .= t.'<input type="text" name="num'.$row->itemid.'" id="num'.$row->itemid.'" value="'.$row->quantity.'" size="1" maxlength = "1" class="quantity" />'.n;
			}
			else {
			$html .= '1 ';
			}
			$html .= t.'<span class="removeitem"><a href="'.JRoute::_('index.php?option='.$option.a.'task=cart'.a.'action=remove'.a.'item='.$row->itemid).'" title="'.JText::_('REMOVE_FROM_CART').'">&nbsp;</a></span>';
			
			$html .= t.'</td>'.n;
			$html .= t.' 			 <td>'.n;
			if(count($sizes)>0) {
			$html .= t. 				StoreHtml :: selectArray(strtolower(JText::_('SIZE')).$row->itemid.'', $sizes, $selectedsize, $class='', $jscall='');
			}
			else {
			$html .= 'N/A';
			}
			$html .= '</td>'.n;
			$html .= t.' 			 <td>'.$price.'</td>'.n;
			$html .= t.'			</tr>'.n;
			}
			$html .= t.' 			<tr class="totals">'.n;
			$html .= t.' 			 <td>';
			$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=cart'.a.'action=empty').'" class="actionlink" title="'.JText::_('EMPTY_CART').'">'.JText::_('EMPTY_CART').'</a></td>'.n;
			$html .= t.' 			 <td></td>'.n;
			$html .= t.' 			 <td>';
			$html .= '<a href="javascript:void(0);" class="actionlink" id="updatecart" title="'.JText::_('TITLE_UPDATE').'">'.JText::_('UPDATE').'</a></td>'.n;
			$html .= t.' 			 <td>'.JText::_('TOTAL').':</td>'.n;
			$html .= t.' 			 <td>'.$total.'</td>'.n;
			$html .= t.' 			</tr>'.n;
			$html .= t.' 		</tbody>'.n;				
			$html .= t.'	</table>'.n;			
			$html .= t.'	<p class="process">';
			if($funds >= $total && intval($total) > 0) {
			$html .= t.'<input type="submit" class="button checkout" value="checkout" /></p>';
			$html .= t.'<span class="reassure">('.JText::_('NOTE_NOCHARGE').')</span>';
			}
			else {
			$html .= t.'<span class="button checkout_disabled">&nbsp;</span>';
			}
			//$html .= t.'</p>'.n;
			$html .= t.' </form>'.n;
			$html .= t.' <div class="footernotes">'.n;
			$html .= t.'	<p>* '.JText::_('CART_NOTES').'.</p>'.n;
			$html  .= t.'</div>'.n;		
		} else {
			$html  = t.'<p>'.JText::_('CART_IS_EMPTY').'. <a href="'.JRoute::_('index.php?option='.$option).'" >'.JText::_('START_SHOPPING').'</a>.</p>'.n;
		}
		
		
		return $html;				
		
	}
	//-----------
	public function order_completed($option, $orderid, $infolink, $title) {
	
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		
		$html = StoreHtml::div( StoreHtml::hed( 2, $title ), '', 'content-header' );
		$html .= '<div class="main section">'.n;	
		//$html .= t.StoreHtml::title(3, ''.JText::_('THANKYOU').'!','firstheader').n;
		$html .= t.t.'<p class="final_summary">'.JText::_('ORDER_NUMBER').': <strong>'.$orderid.'</strong><br />'.JText::_('THANKYOU_MSG').n;
		$html .= t.t.'<br /><a href="'.JRoute::_('index.php?option='.$option).'" >'.JText::_('CONTINUE').'</a>.</p>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-----------
	public function finalize ($title, $items, $option, $funds, $cost, $xuser, $posted, $infolink) {
	
		$name 			= (count($posted)>0 && isset($posted['name'])) ? $posted['name'] : $xuser->get('name');
		$address 		= (count($posted)>0 && isset($posted['address'])) ? $posted['address'] : '';
		$address1 		= (count($posted)>0 && isset($posted['address1'])) ? $posted['address1'] : '';
		$address2 		= (count($posted)>0 && isset($posted['address2'])) ? $posted['address2'] : '';
		$city 			= (count($posted)>0 && isset($posted['city'])) ? $posted['city'] : '';
		$state 			= (count($posted)>0 && isset($posted['state'])) ? $posted['state'] : '';
		$mycountry 		= (count($posted)>0 && isset($posted['country'])) ? htmlentities(($posted['country'])) : htmlentities(getcountry($xuser->get('countryresident')));
		//$mycountry		= 'United States';
		$postal 		= (count($posted)>0 && isset($posted['postal'])) ? $posted['postal'] : '';
		$phone 			= (count($posted)>0 && isset($posted['phone'])) ? $posted['phone'] : htmlentities($xuser->get('phone'));
		$email 			= (count($posted)>0 && isset($posted['email'])) ? $posted['email'] : $xuser->get('email');
		$comments 		= (count($posted)>0 && isset($posted['comments'])) ? $posted['comments'] : '';
		$addressline    = '        <p>'.$name.'<br />'.$address1.' '.$address2.'<br />'.$city;
		$addressline   .= ($state) ? ', '.$state : '';
		$addressline   .= ', '.$postal.'<br />'.$mycountry.'</p>'.n;
		
		
		$html = StoreHtml::div( StoreHtml::hed( 2, $title ), '', 'content-header' );
		$html .= '<div class="main section">'.n;				
		$html .= ' 	<div id="cartcontent">'.n;
		//$html .= StoreHtml::title(3, JText::_('VERIFY_ORDER'),'firstheader');
		$html  .= t.' <form id="hubForm" method="post" action="index.php?option='.$option.'">'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <input type="hidden"  name="task" value="finalize" />'.n;
		$html .= t.'	 <input type="hidden"  name="action" value="" />'.n;
		$html .= t.'	 <input type="hidden"  name="name" value="'.$name.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="address" value="'.$address.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="address1" value="'.$address1.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="address2" value="'.$address2.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="city" value="'.$city.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="state" value="'.$state.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="country" value="'.$mycountry.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="postal" value="'.$postal.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="phone" value="'.$phone.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="email" value="'.$email.'" />'.n;
		$html .= t.'	 <input type="hidden"  name="comments" value="'.$comments.'" />'.n;
		$html .= t.'	 <h3>'.JText::_('ORDER_WILL_SHIP').':</h3>'.n;
		$html .= t.'	 <div class="group"></div>'.n;
		//$html .= t.$addressline;
		$html .= t.'<pre>'.$name.n;
		$html .= $address.n;
		$html .= $mycountry.'</pre>'.n;
		$html .= t.' <p><a class="actionlink" href="javascript:void(0);" id="change_address">'.JText::_('CHANGE_ADDRESS').'</a></p>'.n;
		$html .= t.'	 </fieldset>'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <h3>'.JText::_('CONTACT_INFO').':</h3>'.n;
		$html .= t.'	 <div class="group"></div>'.n;
		$html .= t.'	 <p>';
		if($phone) {
		$html .= 'Phone: '.$phone.'<br />';
		}
		$html .= 'Email: '.$email;
		$html .= '</p>';
		$html .= t.'	 </fieldset>'.n;
		if($comments) {
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <h3>'.JText::_('ADDITIONAL_COMMENTS').':</h3>'.n;
		$html .= t.'	 <div class="group"></div>'.n;
		$html .= t.'	 <p>'.$comments.'</p>'.n;
		$html .= t.'	 </fieldset>'.n;
		}
		$html .= t.t.'<p><a href="'.JRoute::_('index.php?option=com_store'.a.'task=cart').'?action=empty" class="actionlink">'.JText::_('CANCEL ORDER').'</a></p>'.n;
	
		$html .= t.'<div class="clear"></div>'.n;
		$html .= t.t.'<p class="process"><input type="submit" class="button finalize_order" value="finalize" /></p>'.n;
		$html .= t.'  </form>'.n;
		$html .= t.'</div>';
		$html .= StoreHtml:: order_summary($items, $cost, 1, $option);
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		return $html;
	}
	//-----------
	public function checkout($title, $items, $option, $funds, $cost, $xuser, $posted = array(), $error, $infolink) {
	
		$name 			= (count($posted)>0 && isset($posted['name'])) ? $posted['name'] : $xuser->get('name');
		$address 		= (count($posted)>0 && isset($posted['address'])) ? $posted['address'] : '';
		$address1 		= (count($posted)>0 && isset($posted['address1'])) ? $posted['address1'] : '';
		$address2 		= (count($posted)>0 && isset($posted['address2'])) ? $posted['address2'] : '';
		$city 			= (count($posted)>0 && isset($posted['city'])) ? $posted['city'] : '';
		$state 			= (count($posted)>0 && isset($posted['state'])) ? $posted['state'] : '';
		$mycountry 		= (count($posted)>0 && isset($posted['country'])) ? htmlentities(($posted['country'])) : htmlentities(getcountry($xuser->get('countryresident')));
		//$mycountry		= 'United States';
		$postal 		= (count($posted)>0 && isset($posted['postal'])) ? $posted['postal'] : '';
		$phone 			= (count($posted)>0 && isset($posted['phone'])) ? $posted['phone'] : htmlentities($xuser->get('phone'));
		$email 			= (count($posted)>0 && isset($posted['email'])) ? $posted['email'] : $xuser->get('email');
		$comments 		= (count($posted)>0 && isset($posted['comments'])) ? $posted['comments'] : '';
	
	
		$html = StoreHtml::div( StoreHtml::hed( 2, $title ), '', 'content-header' );
		$html .= '<div class="main section">'.n;		
		$html .= ' 	<div id="cartcontent">'.n;
		if($error) {$html .= StoreHtml::warning( $error).n; }
		$html  .= t.' <form id="hubForm" method="post" action="index.php?option='.$option.'">'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <input type="hidden"  name="task" value="process" />'.n;
		$html .= t.'	 <h3>'.JText::_('SHIPPING_ADDRESS').'</h3>'.n;
		$html .= t.'	 <div class="group"></div>'.n;
		$html .= t.'	 	<label>'.JText::_('RECEIVER_NAME').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.'	 	<input name="name" id="name" type="text" value="'.$name.'" /></label>'.n;
		$html .= t.'	 	<label>'.JText::_('COMPLETE_ADDRESS').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.'	 	<textarea name="address" rows="10" cols="50">'.$address.'</textarea></label>'.n;
		$html .= t.'	 	<p class="hint">'.JText::_('ADDRESS_MSG').'</p>'.n;
		/*
		$html .= t.'	 	<input name="address1" id="address1" type="text" value="'.$address1.'" /></label>'.n;
		$html .= t.'	 	<input name="address2" id="address2" type="text" value="'.$address2.'" /></label>'.n;
		$html .= t.'	 	<p class="hint">NOTE: We do not ship to PO boxes. See notes to the right of this form for information on our shipping policies.</p>'.n;
		$html .= t.'	 	<label>City: <span class="required">required</span>'.n;
		$html .= t.'	 	<input name="city" id="city" type="text" value="'.$city.'" /></label>'.n;
		$html .= t.'	 	<label>State/Province:'.n;
		$html .= t.'	 	<input name="state" id="state" type="text" value="'.$state.'" /></label>'.n;
		$html .= t.'	 	<label>Zip / Postal Code: <span class="required">required</span>'.n;
		$html .= t.'	 	<input name="postal" id="postal" type="text" value="'.$postal.'" /></label>'.n;
		*/
		$html .= t.'	 	<label>Country: <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= "\t\t\t\t".'<select name="country" id="country">'."\n";
		//$html .= '<option value="United States" selected="selected">USA</option>';
		$html .= "\t\t\t\t".' <option value="">(select from list)</option>'."\n";
		$countries = getcountries();
		foreach($countries as $country) {
				$html .= "\t\t\t\t".' <option value="' . htmlentities($country['name']) . '"';
				if($country['name'] == $mycountry) {
					$html .= ' selected="selected"';
				}
				$html .= '>' . htmlentities($country['name']) . '</option>'."\n";
		}
		$html .= "\t\t\t\t".'</select></label>'."\n";
		$html .= t.'	 </fieldset>'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <h3>'.JText::_('CONTACT_INFO').'</h3>'.n;
		$html .= t.'	 <div class="group"></div>'.n;
		$html .= t.'	 	<label>'.JText::_('CONTACT_PHONE').':'.n;
		$html .= t.'	 	<input name="phone" id="phone" type="text" value="'.$phone.'" /></label>'.n;
		$html .= t.'	 	<label>'.JText::_('CONTACT_EMAIL').':'.n;
		$html .= t.'	 	<input name="email" id="email" type="text" value="'.$email.'" /></label>'.n;
		$html .= t.'	 	<p class="hint">'.JText::_('CONTACT_MSG').'</p>'.n;
		$html .= t.'	 </fieldset>'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <h3>'.JText::_('ADDITIONAL_COMMENTS').'</h3>'.n;
		$html .= t.'	 <div class="group"></div>'.n;
		$html .= t.'	 	<label>'.JText::_('DETAILS').n;
		$html .= t.'	 	<textarea name="comments" rows="10" cols="50">'.$comments.'</textarea></label>'.n;
		$html .= t.'	 </fieldset>'.n;
		$html .= t.'    		<p class="process"><input type="submit" class="button process_order" value="process" /></p>'.n;
		$html .= t.'    		<span class="confirm">('.JText::_('NOTE_NOCHARGE').')</span>';
		$html .= t.'  </form>'.n;
		$html .= t.'</div>';
		$html .= StoreHtml:: order_summary($items, $cost, '', $option);
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	
	}	
	//-----------
	public function funds_summary($funds, $cost, $hubShortName, $infolink)
	{
		$html  = '<div id="balanceupdate">'.n;
		$html .= t.'<p class="point-balance"><small>'.JText::_('YOU_HAVE').' </small> '.$funds.'<small> '.JText::_('POINTS').' '.JText::_('TO_SPEND').'</small></p>'.n;
		//$html .= t.'<p class="sidenotes">Go to <a href="'.JRoute::_('index.php?option=com_myaccount&task=points').'">My Account</a> page to check balance and transaction history.</p>'.n;
		if($funds < $cost && $cost!=0) {
		$html .= t.'<p class="error">'.JText::_('MSG_NO_FUNDS').' '.JText::_('LEARN_HOW').' <a href="'.$infolink.'">'.strtolower(JText::_('EARN')).'</a> '.JText::_('ON').' '.$hubShortName.'.</p>'.n;
		}
		$html .= '</div>'.n;
		
		return $html;
	}
	//-----------
	public function order_summary($items, $cost, $final='', $option)
	{
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$html  = t.'<div id="balanceupdate">'.n;
		$html .= t.'<div class="order_summary">'.n;
		$html .= t.' <span class="coin">&nbsp;</span><h4>'.JText::_('ORDER_SUMMARY').'</h4>'.n;
		foreach($items as $item) {
		$html .= t.' <p>'.StoreHtml::shortenText($item->title, $chars=28, $p=0);
		if($item->selectedsize) {
		$html .= ',</p>'.n.t.'<p>'.JText::_('SIZE').' '.$item->selectedsize.' (x '.$item->quantity.')';
		}
		else if($item->category != 'service') {
		$html .= ' (x '.$item->quantity.')';
		}
		$html .= t.'<span>'.($item->price*$item->quantity).'</span>'.n;
		$html .= t.' </p>'.n;
		}
		$html .= t.' <p>'.JText::_('SHIPPING').':<span>0</span></p>'.n;
		$html .= t.' <p class="totals">'.JText::_('TOTAL').' '.strtolower(JText::_('POINTS')).':<span>'.$cost.'</span></p>'.n;
		$html .= t.' <p><a class="actionlink" href="'.JRoute::_('index.php?option='.$option.a.'task=cart').'">'.JText::_('CHANGE_ORDER').'</a></p>'.n;
		$html .= t.'</div>'.n;
		if(!$final) {
		$html .= t.' <p class="sidenotes">'.JText::_('MSG_CHANCE_TO_REVIEW').'</p>'.n;
		$html .= t.' <p class="sidenotes"><span class="sidetitle">'.JText::_('SHIPPING').'</span>'.JText::_('MSG_SHIPPING').'</p>'.n;
		$html .= t.' <p class="sidenotes"><span class="sidetitle">'.JText::_('NO_RETURNS').'</span>'.JText::_('MSG_NO_RETURNS').' '.JText::_('MSG_CONTACT_SUPPORT').' <a href="/support">'.JText::_('SUPPORT').'</a> '.JText::_('AREA').'.</p>'.n;
		}
		$html .= t.' <p class="sidenotes">'.JText::_('CONSULT').' <a href="/legal/terms">'.$hubShortName.' '.JText::_('TERMS').'</a></p>'.n;

		$html .= t.'</div>'.n;
		
		return $html;
	}

	
	
	//-----------
	public function productimage( $item, $root, $wpath, $alt, $category )
	{
		if ($wpath) {
			// Strip any trailing slash
			if (substr($wpath, -1) == DS) { 
				$wpath = substr($wpath, 0, strlen($wpath) - 1);
			}
			// Ensure a starting slash
			if (substr($wpath, 0, 1) != DS) { 
				$wpath = DS.$wpath;
			}
			$wpath = $wpath.DS;
		}
		
		$d = @dir($root.$wpath.$item);

		$images = array();
		$html = '';
			
		if ($d) {
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 
				if (is_file($root.$wpath.$item.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png|swf", $img_file )) {
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		}
		else {
			if($category=='service') {
			$html = '<img src="../components/com_store/images/premiumservice.gif" alt="'.JText::_('PREMIUM_SERVICE').'"/>';
			}
			else {
			$html = '<img src="../components/com_store/images/nophoto.gif" alt="'.JText::_('MSG_NO_PHOTO').'"/>';
			}
		}
		
		sort($images);
		$els = '';
		$k = 0;
		$g = 0;
		
		for ($i=0, $n=count( $images ); $i < $n; $i++) 
		{
			$tn = StoreHtml::thumbnail($images[$i]);
			$type = explode('.',$images[$i]);
			
			if (is_file($root.$wpath.$item.'/'.$tn)) {
				
					$k++;
					$els .= '<a rel="lightbox" href="'.$wpath.$item.'/'.$images[$i].'" title="'.$alt.'">';
					$els .= '<img src="'.$wpath.$item.'/'.$tn.'" alt="'.$alt.'" /></a>'.n;
			}
		}
		
		if ($els) {
			//$html .= '<ul class="screenshots">'.n;
			$html .= $els;
			//$html .= '</ul>'.n;
		}
		return $html;
	}
	
	//-----------
	
	public function thumbnail($pic)
	{
		$pic = explode('.',$pic);
		$n = count($pic);
		$pic[$n-2] .= '-tn';
		$end = array_pop($pic);
		$pic[] = 'gif';
		$tn = implode('.',$pic);
		return $tn;
	}
}
?>
