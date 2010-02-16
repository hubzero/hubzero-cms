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

class StoreHTML 
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
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}
	public function shortenText($text, $chars=300) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
		}

		return $text;
	}
	
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
	
	public function orders( &$rows, &$pageNav, $option, $filters ) 
	{
		?>
		<script type="text/javascript">
		public function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>
<p class="extranav"><?php echo JText::_('VIEW'); ?>: <strong><?php echo JText::_('ORDERS'); ?></strong> | <a href="index2.php?option=<?php echo $option; ?>&amp;task=storeitems"><?php echo JText::_('STORE'); ?> <?php echo JText::_('ITEMS'); ?></a></p>
 
   		<form action="index2.php" method="post" name="adminForm">
		
		<fieldset id="filter">
            <?php echo count($rows); ?> <?php echo JText::_('ORDERS_DISPLAYED'); ?>.
			<label><?php echo JText::_('FILTERBY'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
			 <option value="new"<? if($filters['filterby'] == 'new') { echo ' selected="selected"'; } ?>><?php echo JText::_('NEW'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
			 <option value="processed"<? if($filters['filterby'] == 'processed') { echo ' selected="selected"'; } ?>><?php echo JText::_('COMPLETED'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
             <option value="cancelled"<? if($filters['filterby'] == 'cancelled') { echo ' selected="selected"'; } ?>><?php echo JText::_('CANCELLED'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
			 <option value="all"<? if($filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALL'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
			</select></label> 
			
			<label><?php echo JText::_('SORTBY'); ?>:
			<select name="sortby" onchange="document.adminForm.submit( );">
             <option value="m.ordered"<? if($filters['sortby'] == 'm.ordered') { echo ' selected="selected"'; } ?>><?php echo JText::_('ORDER_DATE'); ?></option>
			 <option value="m.status_changed"<? if($filters['sortby'] == 'm.status_changed') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_STATUS_CHANGE'); ?></option>
			 <option value="m.id DESC"<? if($filters['sortby'] == 'm.id DESC') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('ORDER')).' '.strtoupper(JText::_('ID')); ?></option>			
			</select></label> 
		</fieldset>
		
		<table class="adminlist">
		 <thead>
		  <tr>
		   <th><?php echo strtoupper(JText::_('ID')); ?></th>
		   <th><?php echo JText::_('STATUS'); ?></th>
		   <th><?php echo JText::_('ORDERED_ITEMS'); ?></th>
           <th><?php echo JText::_('TOTAL'); ?> (<?php echo JText::_('POINTS'); ?>)</th>
		   <th><?php echo JText::_('BY'); ?></th>
           <th><?php echo JText::_('DATE'); ?></th>
           <th></th>
		  </tr>
		 </thead>
		 <tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
			$status='';
			switch($row->status) 
			{
				case '1':
					$status = strtolower(JText::_('COMPLETED'));
					break;
				case '0':
					$status = '<span class="yes">'.strtolower(JText::_('NEW')).'</span>';
					break;
				case '2':
					$status = '<span style="color:#999;">'.strtolower(JText::_('CANCELLED')).'</span>';
					break;
			}
?>
		  <tr class="<?php echo "row$k"; ?>">
		   <td><a href="index2.php?option=<?php echo $option ?>&amp;task=order&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ORDER'); ?>"><?php echo $row->id; ?></a></td>
		   <td><?php echo $status;  ?></td>
           <td><?php echo $row->itemtitles; ?></td>
           <td><?php echo $row->total; ?></td>
           <td><?php echo $row->author;  ?></td>
		   <td><?php echo JHTML::_('date', $row->ordered, '%d %b, %Y'); ?></td>	   
           <td><a href="index2.php?option=<?php echo $option ?>&amp;task=order&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ORDER'); ?>"><?php echo JText::_('DETAILS'); ?></a><?php if($row->status!=2) { echo '&nbsp;&nbsp;|&nbsp;&nbsp; <a href="index2.php?option='.$option.a.'task=receipt'.a.'id='.$row->id.'">'.JText::_('Receipt').'</a>'; } ?></td>
		  </tr>
<?php
			$k = 1 - $k;
		}
?>
		 </tbody>
		</table>
		
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>


		<?php
	}

//-----------
	
	public function storeitems (&$rows, &$pageNav, $option, $filters ) 
	{
		?>
		<script type="text/javascript">
		public function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>
		<p class="extranav"><?php echo JText::_('VIEW'); ?>: <a href="index2.php?option=<?php echo $option; ?>&amp;task=orders"><?php echo JText::_('ORDERS'); ?></a> | <strong><?php echo JText::_('STORE'); ?> <?php echo JText::_('ITEMS'); ?></strong></p>
   
	
   		<form action="index2.php" method="post" name="adminForm">
		
		<fieldset id="filter">
        <?php echo count($rows); ?> <?php echo JText::_('ITEMS_DISPLAYED'); ?>.
			<label><?php echo JText::_('FILTERBY'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
			 <option value="available"<? if($filters['filterby'] == 'available') { echo ' selected="selected"'; } ?>><?php echo JText::_('INSTORE_ITEMS'); ?></option>
             <option value="published"<? if($filters['filterby'] == 'published') { echo ' selected="selected"'; } ?>><?php echo JText::_('PUBLISHED'); ?></option>
			 <option value="all"<? if($filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALL_ITEMS'); ?></option>
			</select></label> 
			
			<label><?php echo JText::_('SORTBY'); ?>:
			<select name="sortby" onchange="document.adminForm.submit( );">
             <option value="pricelow"<? if($filters['sortby'] == 'pricelow') { echo ' selected="selected"'; } ?>><?php echo JText::_('Lowest price'); ?></option>
             <option value="pricehigh"<? if($filters['sortby'] == 'pricehigh') { echo ' selected="selected"'; } ?>><?php echo JText::_('Highlest price'); ?></option>
			 <option value="date"<? if($filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Date added')); ?></option>
             <option value="category"<? if($filters['sortby'] == 'category') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Category')); ?></option>			
			</select></label> 
		</fieldset>
		
		<table class="adminlist">
		 <thead>
		  <tr>
		   <th><?php echo strtoupper(JText::_('ID')); ?></th>
           <th><?php echo JText::_('CATEGORY'); ?></th>
		   <th><?php echo JText::_('TITLE'); ?></th>
		   <th><?php echo JText::_('DESCRIPTION'); ?></th>
		   <th><?php echo JText::_('PRICE'); ?></th>
           <th><?php echo JText::_('TIMES_ORDERED'); ?></th>
           <th><?php echo JText::_('INSTOCK'); ?></th>
           <th><?php echo JText::_('PUBLISHED'); ?></th>
		  </tr>
		 </thead>
		 <tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
			$status='';
			switch($row->available) 
			{
				case '1':
					$a_class = 'published';
					$a_task = 'unavail';
					$a_alt = JText::_('TIP_MARK_UNAVAIL');
					break;
				case '0':
					$a_class = 'unpublished';
					$a_task = 'avail';
					$a_alt = JText::_('TIP_MARK_AVAIL');
					break;
			}
			switch($row->published) 
			{
				case '1':
					$p_class = 'published';
					$p_task = 'unpublish';
					$p_alt = JText::_('TIP_REMOVE_ITEM');
					break;
				case '0':
					$p_class = 'unpublished';
					$p_task = 'publish';
					$p_alt = JText::_('TIP_ADD_ITEM');
					break;
			}
?>
		  <tr class="<?php echo "row$k"; ?>">
		   <td><a href="index2.php?option=<?php echo $option ?>&amp;task=storeitem&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ITEM_DETAILS'); ?>"><?php echo $row->id; ?></a></td>
		   <td><?php echo $row->category;  ?></td>
           <td><a href="index2.php?option=<?php echo $option ?>&amp;task=storeitem&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ITEM_DETAILS'); ?>"><?php echo stripslashes($row->title); ?></a></td>
           <td><?php echo StoreHTML:: shortenText($row->description, $chars=300)  ?></td>
		   <td><?php echo $row->price ?></td>
           <td><?php echo ($row->allorders) ? $row->allorders : '0';  ?></td>	 	   
           <td><a class="<?php echo $a_class;?>" href="index2.php?option=<?php echo $option ?>&amp;task=<?php echo $a_task;?>&amp;id=<?php echo $row->id; ?>" title="<?php echo $a_alt;?>"><span><?php echo $a_alt; ?></span></a></td>
           <td><a class="<?php echo $p_class;?>" href="index2.php?option=<?php echo $option ?>&amp;task=<?php echo $p_task;?>&amp;id=<?php echo $row->id; ?>" title="<?php echo $p_alt;?>"><span><?php echo $p_alt; ?></span></a></td>
		  </tr>
<?php
			$k = 1 - $k;
		}
?>
		 </tbody>
		</table>
		
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="storeitems" />
		</form>


		<?php
	}

	//-----------

	public function viewOrder( &$row, &$orderitems, $customer, $funds, $option) 
	{
		$order_date = (intval( $row->ordered ) <> 0) ? JHTML::_('date', $row->ordered, '%d %b, %Y') : NULL ;
		$status_changed = (intval( $row->status_changed ) <> 0) ? JHTML::_('date', $row->status_changed, '%d %b, %Y') : NULL;
		
		switch($row->status)
		{
			case 0: 
				$status = "<span class='yes'>".strtolower(JText::_('NEW'))."</span>";
				break;
			case 1: 
				$status = "completed";
				break;
			case 2:
			default: 
				$status = "cancelled";
				break;
		}
		
		
		?>

		<script type="text/javascript">
		public function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			submitform( pressbutton );

		}
		</script>

		<p class="extranav" style="margin-left:1.5em;"><?php echo JText::_('VIEW'); ?>: <a href="index2.php?option=<?php echo $option; ?>"><?php echo JText::_('STORE').' '. JText::_('ORDERS'); ?></a></p>
 
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
		
            <?php  if(isset($row->id)) { ?>
			<legend><?php echo JText::_('ORDER').' #'.$row->id.' '.JText::_('DETAILS'); ?></legend>
				<table class="admintable">
				 <tbody>
				  <tr>
				    <td class="key"><label><?php echo JText::_('ITEMS'); ?>:</label></td>
				   <td><p>
				   <?php 
				    $k=1;
				    foreach($orderitems as $o) {
					$avail = ($o->available) ?  'available' : 'unavailable';
					$html  = $k.') ';
				   	$html .= $o->title. ' (x'.$o->quantity.')';
					$html .= ($o->selectedsize) ? '- size '.$o->selectedsize : '';
					$html .= '<br /><span style="color:#999;">'.JText::_('ITEM').' '.JText::_('STORE').' '.JText::_('ID').' #'.$o->itemid.'. '.JText::_('STATUS').': '.$avail;
					if(!$o->sizeavail) {
					$html .= JText::_('WARNING_NOT_IN_STOCK');
					}
					$html .= '. '.JText::_('CURRENT_PRICE').': '.$o->price.'</span><br />';
					$k++;
					echo $html;				   
				   	}
				    ?></p>
                   </td>
				  </tr>
				  <tr>
				   <td class="key"><label><?php echo JText::_('SHIPPING'); ?>:</label></td>
				   <td><pre><?php echo $row->details ?></pre></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('PROFILE_INFO'); ?>:</label></td>
				   <td><?php echo JText::_('LOGIN'); ?>: 		<?php echo $customer->get('username') ?> <br />
	 				   <?php echo JText::_('NAME'); ?>:  		<?php echo $customer->get('name') ?> <br />
    				   <?php echo JText::_('EMAIL'); ?>: 		<?php echo $customer->get('email') ?>
                   </td>
				  </tr>
                  <tr>
				    <td class="key"><label><?php echo JText::_('ADMIN_NOTES'); ?>:</label></td>
				   <td><textarea name="notes" id="notes"  cols="50" rows="10"><?php echo (stripslashes($row->notes)); ?></textarea></td>
				  </tr>
				 </tbody>
				</table>
			</fieldset>
			
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('PROCESS_ORDER'); ?></legend>
				<table class="admintable">
				 <tbody>
				 <tr>
				   <td class="key"><label><?php echo JText::_('STATUS'); ?>:</label></td>
				   <td><?php echo $status ?></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('ORDER_PLACED'); ?>:</label></td>
				   <td><?php echo $order_date ?></td>
				  </tr>
                  <?php
					if($row->status != 0) {
					?>
				  <tr>
				   <td class="key"><label><?php echo JText::_('ORDER').' '.$status; ?>:</label></td>
				    <td><?php echo $status_changed ?></td>
				  </tr>
                  <?php
					}
					?>
                  <tr>
				   <td class="key"><label><?php echo JText::_('ORDER').' '.JText::_('TOTAL'); ?>:</label></td>
                   <td>
                    <?php
					if($row->status == 0) {
					?>
				   <input type="text" name="total" value="<?php echo $row->total ?>"  /> <?php echo JText::_('POINTS'); ?>
                   <?php
					} else {
					?>
                    <?php echo $row->total ?> <?php echo JText::_('POINTS'); ?>
                    <input type="hidden" name="total" value="<?php echo $row->total ?>"  />
                    <?php
					} 
					?>
                    </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('CURRENT_BALANCE'); ?>:</label></td>
				   <td><strong><?php echo $funds ?></strong> points</td>
				  </tr> 
                  <?php
					if($row->status == 0) {
					?>
				  <tr>
				   	<td class="key"><label><?php echo JText::_('MANAGE_ORDER'); ?>:</label></td>
				    <td><input type="radio" name="action" value="message" /><?php echo JText::_('ORDER_ON_HOLD'); ?></td>
				  </tr>
                  <tr>
				   	<th></th>
				    <td><input type="radio" name="action" value="complete_order" /> <?php echo JText::_('PROCESS_TRANSACTION'); ?></td>
				  </tr>
                    <tr>
				   	<th></th>
				    <td><input type="radio" name="action" value="cancel_order" /> <?php echo JText::_('RELEASE_FUNDS'); ?></td>
				  </tr>
                   <tr>
				   	<th></th>
				    <td><?php echo JText::_('SEND_A_MSG'); ?>: <br /><textarea name="message" id="message"  cols="30" rows="5"></textarea></td>
				  </tr>
                  <?php
					}
					?>                
				 </tbody>
				</table>
			</fieldset>
			</div>
			<div class="clr"></div>				 
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="saveorder" />
            <?php  } // end if id exists ?>
		</form>
		<?php
	}

	//-----------
	public function viewItem( &$row, $option) 
	{
		$created = NULL;
		if (intval( $row->created ) <> 0) {
			$created = JHTML::_('date', $row->created, '%d %b, %Y');
		}
		
		?>

		<script type="text/javascript">
		public function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			submitform( pressbutton );

		}
		</script>
	<p class="extranav" style="margin-left:1.5em;"><?php echo JText::_('VIEW'); ?>: <a href="index2.php?option=<?php echo $option; ?>&amp;task=storeitems"><?php echo JText::_('STORE').' '. JText::_('ITEMS'); ?></a></p>
 
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
		
			
            <?php  if(isset($row->id)) { ?>

			<legend><?php echo JText::_('STORE').' '. JText::_('ITEM').' #'.$row->id.' '.JText::_('DETAILS'); ?></legend>
				<table class="admintable">
				 <tbody>
                 <tr>
				  <td class="key"><label><?php echo JText::_('CATEGORY'); ?>:</label></td>
				   <td><select name="category">
                   		<option value="service"<? if($row->category == 'service') { echo ' selected="selected"'; } ?>>Service</option>
			 			<option value="wear"<? if($row->category == 'wear') { echo ' selected="selected"'; } ?>>Wear</option>
             			<option value="office"<? if($row->category == 'office') { echo ' selected="selected"'; } ?>>Office</option>
                        <option value="fun"<? if($row->category == 'fun') { echo ' selected="selected"'; } ?>>Fun</option>
					   </select>
            		</td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('PRICE'); ?>:</label></td>
				   <td><input type="text" name="price" id="price"  size="5" value="<?php echo $row->price; ?>" /></td>
				  </tr>
				  <tr>
				   <td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
				   <td><input type="text" name="title" id="title"  maxlength="100" style="width:100%" value="<?php echo stripslashes($row->title); ?>" /></td>
				  </tr>
                  <tr>
				  <td class="key"><label><?php echo JText::_('DESCRIPTION'); ?>:</label></td>
				   <td><textarea name="description" id="description"  cols="50" rows="10"><?php echo stripslashes($row->description); ?></textarea>
                <br /><?php echo JText::_('WARNING_DESCR'); ?></td>
				  </tr>
				 </tbody>
				</table>
			</fieldset>
			
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('OPTIONS'); ?></legend>
				<table class="admintable">
				 <tbody>
				 <tr>
				  <td class="key"><label><?php echo JText::_('PUBLISHED'); ?>:</label></td>
				   <td><input type="checkbox" name="published" value="1" <?php echo ($row->published) ? 'checked="checked"' : ''; ?> /></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo ucfirst(JText::_('INSTOCK')); ?>:</label></td>
				   <td><input type="checkbox" name="available" value="1" <?php echo ($row->available) ? 'checked="checked"' : ''; ?> /></td>
				  </tr> 
                  <tr>
				   <td class="key"><label><?php echo JText::_('FEATURED'); ?>:</label></td>
				   <td><input type="checkbox" name="featured" id="featured" value="1" <?php echo ($row->featured) ? 'checked="checked"' : ''; ?> /></td>
				  </tr> 
                  <tr>
				   <td class="key"><label><?php echo JText::_('AV_SIZES'); ?>:</label></td>
				   <td><input type="text" name="sizes" size="15" value="<?php echo (isset($row->size)) ? $row->size : '' ; ?>" /><br /><?php echo JText::_('SAMPLE_SIZES'); ?>:</td>
				  </tr>           
				 </tbody>
				</table>
			</fieldset>
			</div>
			<div class="clr"></div>			 
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="saveorder" />
            <?php  } // end if id exists ?>
		</form>
		<?php
	}
	
	//-----------

	public function autop($pee, $br = 1) 
	{
		// converts paragraphs of text into xhtml
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		$pee = preg_replace('!(<(?:table|ul|ol|li|pre|form|blockquote|h[1-6])[^>]*>)!', "\n$1", $pee); // Space things out a little
		$pee = preg_replace('!(</(?:table|ul|ol|li|pre|form|blockquote|h[1-6])>)!', "$1\n", $pee); // Space things out a little
		$pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // cross-platform newlines 
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "\t<p>$1</p>\n", $pee); // make paragraphs, including one at the end 
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace 
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $pee); 
		if ($br) $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|th|pre|td|ul|ol)>)!', '$1', $pee);
		$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);
		
		return $pee; 
	}

	//-----------
	
	public function unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', '', $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee; 
	}
}
?>
