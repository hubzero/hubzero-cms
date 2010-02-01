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

class ServicesHtml 
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
	


	//----------------------------------------------------------
	// Browse Views
	//----------------------------------------------------------
	
	public function services( $rows, $option, $pageNav, $filters) 
	{
	?>
	
	 <h3><?php echo JText::_('Services'); ?></h3>
		<form action="index.php" method="post" name="adminForm">
			
			<table class="adminlist" summary="<?php echo JText::_('A list of paid/subscription-based HUB services'); ?>">
				<thead>
					<tr>
						<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th width="5%" nowrap="nowrap"><?php echo JText::_('ID'); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'title', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
                        <th><?php echo JHTML::_('grid.sort', JText::_('Category'), 'category', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
                        <th><?php echo JHTML::_('grid.sort', JText::_('Status'), 'status', @$filters['sort_Dir'], @$filters['sort'] ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="5"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		$i = 0;
		foreach ($rows as $row) 
		{
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
						<td><?php echo $row->id; ?></td>
                        <td><?php echo $row->title; ?></td>
						<td><?php echo $row->category; ?></td>
                        <td class="<?php echo $row->status==1 ? JText::_('active') : JText::_('inactive') ; ?>"><?php echo $row->status==1 ? JText::_('active') : JText::_('inactive') ; ?></td>
					</tr>
<?php
			$k = 1 - $k;
			$i++;
		}
?>
				</tbody>
			</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="services" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $filters['sort']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sort_Dir']; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	//-----------

	public function service( $database, $service, $option ) 
	{
		
	}

	public function subscriptions( $database, $rows, $total, $pageNav, $option, $filters ) 
	{
		$now = date( 'Y-m-d H:i:s', time() );
		
		?>
		
 		<h3><?php echo JText::_('Subscriptions'); ?></h3>
   		<form action="index2.php" method="post" name="adminForm">
		
		<fieldset id="filter">
            <?php echo $total; ?> <?php echo JText::_('total subscriptions'); ?>.
			<label><?php echo JText::_('Filter by'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
			 <option value="pending"<? if($filters['filterby'] == 'pending') { echo ' selected="selected"'; } ?>><?php echo JText::_('Pending'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
			 <option value="active"<? if($filters['filterby'] == 'processed') { echo ' selected="selected"'; } ?>><?php echo JText::_('Active'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
             <option value="cancelled"<? if($filters['filterby'] == 'cancelled') { echo ' selected="selected"'; } ?>><?php echo JText::_('Cancelled'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
			 <option value="all"<? if($filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALL'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
			</select></label> 
			
			<label><?php echo JText::_('Sort by'); ?>:
			<select name="sortby" onchange="document.adminForm.submit( );">
             <option value="date"<? if($filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo JText::_('Date Added'); ?></option>
             <option value="date_updated"<? if($filters['sortby'] == 'date_updated') { echo ' selected="selected"'; } ?>><?php echo JText::_('Last Updated'); ?></option>
             <option value="date_expires"<? if($filters['sortby'] == 'date_expires') { echo ' selected="selected"'; } ?>><?php echo JText::_('Soon to Expire'); ?></option>
			 <option value="pending"<? if($filters['sortby'] == 'pending') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Pending Admin Action')); ?></option>	
              <option value="status"<? if($filters['sortby'] == 'status') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Status')); ?></option>					
			</select></label> 
		</fieldset>
		
		<table class="adminlist">
		 <thead>
		  <tr>
		   <th><?php echo JText::_('ID -- Code'); ?></th>
		   <th><?php echo JText::_('Status'); ?></th>
		   <th><?php echo JText::_('Service'); ?></th>
           <th><?php echo JText::_('Pending Payment / Units'); ?></th>
           <th><?php echo JText::_('User'); ?></th>
           <th><?php echo JText::_('Added'); ?></th>
           <th><?php echo JText::_('Last Updated'); ?></th>
           <th><?php echo JText::_('Expires'); ?></th>
           <th></th>
		  </tr>
		 </thead>
		 <tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
				
			$name = JText::_('UNKNOWN');
			$login = JText::_('UNKNOWN');
			$ruser =& JUser::getInstance($row->uid);
			if (is_object($ruser)) {
				$name = $ruser->get('name');
				$login = $ruser->get('username');
			}
			
			$status='';
			$pending = $row->currency.' '.$row->pendingpayment.' - '.JText::_('for').' '.$row->pendingunits.' '.JText::_('units(s)');
			
			$expires = (intval( $row->expires) <> 0) ? JHTML::_('date', $row->expires, '%d %b, %Y') : 'N/A';
				
			switch($row->status) 
			{
				case '1':
					$status = ($row->expires > $now) ? '<span style="color:#197f11;">'.strtolower(JText::_('Active')).'</span>' : '<span style="color:#ef721e;">'.strtolower(JText::_('Expired')).'</span>';
					break;
				case '0':
					$status = '<span style="color:#ff0000;">'.strtolower(JText::_('Pending')).'</span>';
					break;
				case '2':
					$status = '<span style="color:#999;">'.strtolower(JText::_('Cancelled')).'</span>';
					$pending .= $row->pendingpayment ? ' ('.JText::_('refund').')' : '';
					break;
			}
?>
		  <tr class="<?php echo "row$k"; ?>">
		   <td><a href="index2.php?option=<?php echo $option ?>&amp;task=subscription&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('View Subscription Details'); ?>"><?php echo $row->id,' -- '.$row->code; ?></a></td>
		   <td><?php echo $status;  ?></td>
           <td><?php echo $row->category.' -- '.$row->title; ?></td>
           <td><?php echo $row->pendingpayment &&  ($row->pendingpayment > 0 or $row->pendingunits > 0)  ? '<span style="color:#ff0000;">'.$pending.'</span>' : $pending;  ?></td>
           <td><?php echo $name.' ('.$login.')';  ?></td>
		   <td><?php echo JHTML::_('date', $row->added, '%d %b, %Y'); ?></td>	   
           <td><?php echo JHTML::_('date', $row->updated, '%d %b, %Y'); ?></td>
           <td><?php echo $expires; ?></td>	   	   
           <td><a href="index2.php?option=<?php echo $option ?>&amp;task=subscription&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('View Subscription Details'); ?>"><?php echo JText::_('DETAILS'); ?></a></td>
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

	public function subscription( $database, $row, $option, $funds, $customer ) 
	{
	
	$added = (intval( $row->added ) <> 0) ? JHTML::_('date', $row->added, '%d %b, %Y') : NULL ;
	$updated = (intval( $row->updated ) <> 0) ? JHTML::_('date', $row->updated, '%d %b, %Y') : 'N/A';
	$expires = (intval( $row->expires) <> 0) ? JHTML::_('date', $row->expires, '%d %b, %Y') : 'N/A';
	
	$status='';
	$pending = $row->currency.' '.$row->pendingpayment;
	$now = date( 'Y-m-d H:i:s', time() );
	
	$onhold_msg = $row->status==2 ? JText::_('No action / send message to user') : JText::_('Subscription on hold (pending payment or verification)');
		
	switch($row->status) 
	{
		case '1':
			$status = ($row->expires > $now) ? '<span style="color:#197f11;">'.strtolower(JText::_('Active')).'</span>' : '<span style="color:#ef721e;">'.strtolower(JText::_('Expired')).'</span>';
			break;
		case '0':
			$status = '<span style="color:#ff0000;">'.strtolower(JText::_('Pending')).'</span>';
			break;
		case '2':
			$status = '<span style="color:#999;">'.strtolower(JText::_('Cancelled')).'</span>';
			$pending .= $row->pendingpayment ? ' ('.JText::_('refund').')' : '';
			break;
	}
	
			$priceline = $row->currency.' '.$row->unitprice.'  </strong>'.JText::_( 'per' ).' '.$row->unitmeasure;
			$priceline.=  $row->pointsprice > 0 ? ' or '.$row->pointsprice.' '.JText::_('points') : '';
			
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

		<form action="index.php" method="post" name="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
		
            <?php  if(isset($row->id)) { ?>
			<legend><?php echo JText::_('Subscription').' #'.$row->id.' ('.$row->code.') '; ?></legend>
				<table class="admintable">
				 <tbody>
				  <tr>
				    <td class="key"><label><?php echo JText::_('Service'); ?>:</label></td>
				   <td>
                   <?php echo $row->title.' - <strong>'.$priceline.'</strong>'; ?>
                   </td>
				  </tr>
				  <tr>
				   <td class="key"><label><?php echo JText::_('Profile Info'); ?>:</label></td>
				   <td><?php echo JText::_('Login'); ?>: 		<?php echo $customer->get('username') ?> <br />
	 				   <?php echo JText::_('Name'); ?>:  		<?php echo $customer->get('name') ?> <br />
    				   <?php echo JText::_('Email'); ?>: 		<?php echo $customer->get('email') ?> <br />
                       <?php echo JText::_('Tel.'); ?>: 		<?php echo $customer->get('phone') ?>
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('Employer Info'); ?>:</label></td>
				   <td><?php echo JText::_('Company Name'); ?>: 	<?php echo $row->companyName; ?> <br />
	 				   <?php echo JText::_('Company Location'); ?>: <?php echo $row->companyLocation; ?> <br />
    				   <?php echo JText::_('Company URL'); ?>: 		<?php echo $row->companyWebsite; ?>
                   </td>
				  </tr>
                  <tr>
				    <td class="key"><label><?php echo JText::_('Administrator Notes'); ?>:</label></td>
				   <td><textarea name="notes" id="notes"  cols="50" rows="10"><?php echo (stripslashes($row->notes)); ?></textarea></td>
				  </tr>
				 </tbody>
				</table>
			</fieldset>
			
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('Process Subscription'); ?></legend>
				<table class="admintable">
				 <tbody>
				 <tr>
				   <td class="key"><label><?php echo JText::_('Status'); ?>:</label></td>
				   <td><?php echo $status ?></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('Added'); ?>:</label></td>
				   <td><?php echo $added ?></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('Expires'); ?>:</label></td>
				   <td><?php echo $expires ?></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('Last Updated'); ?>:</label></td>
				   <td><?php echo $updated ?></td>
				  </tr>
                  
                   <tr>
				   <td class="key"><label><?php echo JText::_('Total paid'); ?>:</label></td>
				   <td><?php echo $row->totalpaid; ?><?php if($row->usepoints) { echo JText::_('POINTS'); } else { echo $row->currency; } ?></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('Pending payment'); ?>:</label></td>
				   <td><?php echo $row->pendingpayment; ?><?php if($row->usepoints) { echo JText::_('POINTS'); } else { echo $row->currency; } ?></td>
				  </tr>
                   <tr>
				   <td class="key"><label><?php echo JText::_('Active units'); ?>:</label></td>
				   <td><?php echo $row->units; ?></td>
				  </tr>
                   <tr>
				   <td class="key"><label><?php echo JText::_('Pending units'); ?>:</label></td>
				   <td><?php echo $row->pendingunits; ?></td>
				  </tr>
                  
                   <tr>
				   	<td colspan="2"><h3><?php echo JText::_('Manage Subscription'); ?>:</h3></td>
				   </tr>                   
                    <tr>
				   	<td colspan="2"><input type="radio" name="action" value="message"  /> <?php echo $onhold_msg; ?></td>
				  </tr>
                    
                    <?php
					if($row->status == 2) {
					?>
                    <?php if ($row->pendingpayment > 0) { ?> 
                    <tr>
                    <td colspan="2"><input type="radio" name="action" value="refund" /> <?php echo JText::_('Process refund / remove pending items'); ?></td>
                    
				   </tr>
                    <tr>
				   <td class="key"><label><?php echo JText::_('Pending Refund').' <br />for '.$row->pendingunits.' '.JText::_('unit(s)'); ?>:</label></td>
				   <td> <?php echo $row->pendingpayment; ?><?php if($row->usepoints) { echo JText::_('POINTS'); } else { echo $row->currency; } ?></td>
				  </tr>
                 
                    <tr>
				   <td class="key"><label><?php echo JText::_('Refund posted');  ?>:</label></td>
				   <td><input type="text" name="received_refund" value="<?php echo $row->pendingpayment ?>"  /> <?php if($row->usepoints) { echo JText::_('POINTS'); } else { echo $row->currency; } ?></td>
				  </tr>
                   <?php } ?>
                    
                   <?php
					} else { 
					?>                            
                  <tr>
				   	<td colspan="2"><input type="radio" name="action" value="activate" /> <?php echo JText::_('Activate/ Extend this subscription (new payment or verification received) '); ?></td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('New payment received'); ?>:</label></td>
				   <td> <?php if  ($row->pendingpayment > 0 ) { ?> <input type="text" name="received_payment" value="<?php echo $row->pendingpayment ?>"  />  <?php } else { echo $row->pendingpayment;  } ?><?php if($row->usepoints) { echo JText::_('POINTS'); } else { echo $row->currency; } ?></td>
				  </tr>
                  
                   <tr>
				   <td class="key"><label><?php echo JText::_('Activate units'); ?>:</label></td>
				   <td> <?php if  ($row->pendingunits > 0 ) { ?> <input type="text" name="newunits" value="<?php echo $row->pendingunits ?>"  /> <?php } else { echo $row->pendingunits;  } ?> </td>
				  </tr>
                   <tr>
				   	<td colspan="2"><input type="radio" name="action" value="cancelsub" /> <?php echo JText::_('Cancel this subscription'); ?></td>
				  </tr>
                 <?php
					}
					?>
                     <tr >
                     	<td colspan="2" style="border-top:3px solid #ccc;padding-top:2em;"></td>
                     </tr>
                    
                    <tr>
				   	 <td class="key"><?php echo JText::_('Send user a message').'<br />'.JText::_('(optional)'); ?>:</td>
				    <td><textarea name="message" id="message"  cols="30" rows="5"></textarea></td>
				  </tr>
                  
                       
				 </tbody>
				</table>
			</fieldset>
			</div>
			<div class="clr"></div>
            <input type="hidden" name="usepoints" value="<?php echo $row->usepoints; ?>" />				 
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="saveorder" />
            <?php  } // end if id exists ?>
		</form>
		<?php
		
	}


}
?>
