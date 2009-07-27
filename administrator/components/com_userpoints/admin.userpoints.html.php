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

//----------------------------------------------------------

class UserpointsHTML 
{
	function users( &$rows, $option, $stats) 
	{
	 			UserpointsHTML::menutop();
		?>
       
			<ul id="submenu">
			 	<li><a href="index2.php?option=<?php echo $option; ?>" class="active">Summary</a></li> 
                <li><a href="index2.php?option=<?php echo $option; ?>&amp;task=edit">Look up User Balance</a></li>
			 	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=config">Configuration</a></li> 
             	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=batch">Batch Transaction</a></li>
			</ul>
       <?php   UserpointsHTML::menubottom(); ?>
            
            <h3>Top Earners</h3>
<?php if($rows) { ?>
			<table class="adminlist" summary="A list of Top 15 earners and their points">
			 <thead>
			  <tr>
			   <th>Name</th>
			   <th>UID</th>
               <th>Lifetime Earnings</th>
			   <th>Current Balance</th>
               <th>Transaction History</th>
			  </tr>
			 </thead>
			 <tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			$wuser =& XUser::getInstance( $row->uid );
			if (is_object($wuser)) {
				$name = $wuser->get('name');
			} else {
				$name = JText::_('UNKNOWN'); 
			}	
			
?>
			  <tr class="<?php echo "row$k"; ?>">
			   <th><?php echo $name; ?></th>
			   <th><?php echo $row->uid; ?></th>
               <th><?php echo $row->earnings; ?></th>
			   <th><?php echo $row->balance; ?></th>
               <th><a href="index2.php?option=<?php echo $option; ?>&amp;task=edit&amp;uid=<?php echo $row->uid; ?>">view</a></th>
			  </tr>
<?php
			$k = 1 - $k;
		}
?>
			 </tbody>
			</table>
	<?php } else { ?>
    <p>No user information found.</p>
    <?php } ?>
    <h3>Economy Activity Stats as of <?php echo JHTML::_('date', date( "Y-m-d H:i:s" ), '%d %b, %Y'); ?></h3>
 <?php if(count($stats) > 0) { ?>
    	<table class="adminlist" summary="Summary of user point activity">
			 <thead>
			  <tr>
			   <th rowspan="2">Activity</th>
			   <th colspan="3" style="background-color:#d0d0d0;">All time</th>
			   <th colspan="2" style="background-color:#e5d2c4;">Current month</th>
               <th colspan="2">Previous month</th>
			  </tr>
              <tr style="font-size:x-small">
			   <th style="background-color:#dcdddc;">Points</th>
			   <th style="background-color:#dcdddc;">Transactions</th>
              
               <th style="background-color:#dcdddc;">Avg Pnt/Trans</th>
               <th style="background-color:#f2ede9;">Points</th>
               <th style="background-color:#f2ede9;">Transactions</th>
               <th>Points</th>
			   <th>Transactions</th>
               
			  </tr>
			 </thead>
			 <tbody>
 <?php

		foreach($stats as $stat) {
		
		if(isset($stat['class'])) {
			switch ( $stat['class'] ) 
			{
			case 'spendtotal':     
				$class = ' style="color:red; background-color:#f2ede9;border-top:2px solid #ccc;"';   	
				break;						
			case 'earntotal':
			 	$class = ' style="color:green; background-color:#ecf9e9;"';
				break;
			case 'royaltytotal':
			 	$class = ' style="color:#000000;border-top:2px solid #ccc;background-color:#efefef;"';
				break;
			default:
				$class = '';
			}
		}
		
?>
              <tr>
			   <th<?php echo $class; ?>><?php echo $stat['memo']; ?></th>
                <th<?php echo $class; ?>><?php echo $stat['alltimepts']; ?></th>
               <th><?php echo $stat['alltimetran']; ?></th>			  
               <th><?php echo isset($stat['avg']) ? $stat['avg'] : ''; ?></th>
               <th><?php echo isset($stat['thismonthpts']) ? $stat['thismonthpts'] : '' ; ?></th>
               <th><?php echo isset($stat['thismonthtran']) ? $stat['thismonthtran'] : '' ; ?></th>
			   <th><?php echo $stat['lastmonthpts']; ?></th>
               <th><?php echo $stat['lastmonthtran']; ?></th>
               
	
			  </tr>
   <?php
			
		}
?>
             </tbody>
		</table>
        <!--<p>Distribute <a href="index2.php?option=<?php echo $option; ?>&amp;task=royalty&amp;auto=0">Royalties</a> for current month.</p>//-->
     <?php } else { ?>
    <p>No summary information found.</p>
    <?php } ?>
   
		<?php
	}
	//-----------
	
	function menutop( ) 
	{
	?>
     <div id="submenu-box">
			<div class="t">
				<div class="t">
					<div class="t"></div>
		 		</div>
	 		</div>
			<div class="m">
	<?php
	}
	
	function menubottom( ) 
	{
	?>
	 <div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
		 			<div class="b"></div>
				</div>
			</div>
		</div>
		
				
		<div id="element-box">
			<div class="t">
		 		<div class="t">
					<div class="t"></div>
		 		</div>
			</div>
			<div class="m">
     <?php
	}
	
	//-----------
	
	function edit( &$database, &$row, $option, $history ) 
	{
		UserpointsHTML::menutop();
		?>
       
			<ul id="submenu">
			 	<li><a href="index2.php?option=<?php echo $option; ?>">Summary</a></li> 
                <li><a href="index2.php?option=<?php echo $option; ?>&amp;task=edit"  class="active">Look up User Balance</a></li>
			 	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=config">Configuration</a></li> 
             	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=batch">Batch Transaction</a></li>
			</ul>
       <?php   UserpointsHTML::menubottom(); ?>
       
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.uid.value == ''){
				alert( 'You must fill in a UID' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">
			
			<div class="column1">
				<table class="adminform">
				 <thead>
				  <tr>
				   <th colspan="2">User Details</th>
				  </tr>
				 </thead>
				 <tbody>
				  <tr>
				   <td><label for="uid">UID:</label></td>
				   <td><input type="text" name="uid" id="uid" size="20" maxlength="250" value="<?php echo $row->uid; ?>" /></td>
				  </tr>
				  <tr>
				   <td><label for="raw_tag">Point Balance:</label></td>
				   <td><input type="text" name="balance" id="balance" size="20" maxlength="250" value="<?php echo $row->balance; ?>" /></td>
				  </tr>
				  <tr>
				   <td><label for="alias">Total Earnings:</label></td>
				   <td><input type="text" name="earnings" id="earnings" size="20" maxlength="250" value="<?php echo $row->earnings; ?>" /></td>
				  </tr>
				 </tbody>
				 <thead>
				  <tr>
				   <th colspan="2">New Transaction</th>
				  </tr>
				 </thead>
				 <tbody>
				  <tr>
				   <td><label for="type">Type:</label></td>
				   <td><select name="type" id="type">
						<option>deposit</option>
						<option>withdraw</option>
						<option>creation</option>
				   </select></td>
				  </tr>
				  <tr>
				   <td><label for="amount">Amount:</label></td>
				   <td><input type="text" name="amount" id="amount" size="11" maxlength="11" value="" /></td>
				  </tr>
				  <tr>
				   <td><label for="description">Description:</label></td>
				   <td><input type="text" name="description" id="description" size="20" maxlength="250" value="" /></td>
				  </tr>
                  <tr>
				   <td><label for="category">Category:</label></td>
				   <td><input type="text" name="category" id="category" size="20" maxlength="250" value="" /> <span style="display:block;margin-bottom:1em;">E.g. answers, store, survey, general etc.</span>
                   <input type="submit" name="submit" value="Save changes" style="margin-bottom:1.5em;" />
                   </td>
				  </tr>
				 </tbody>
				</table>
			</div>
			<div class="clear"></div>
			<h3>Transaction History</h3>
<?php
			$html  = '<table class="adminlist">'."\n";
			$html .= ' <thead>'."\n";
			$html .= '  <tr>'."\n";
			$html .= '   <th>Date</td>'."\n";
			$html .= '   <th>Description</td>'."\n";
			$html .= '   <th>Category</td>'."\n";
			$html .= '   <th>Type</td>'."\n";
			$html .= '   <th class="aRight">Amount</td>'."\n";
			$html .= '   <th class="aRight">Balance</td>'."\n";
			$html .= '  </tr>'."\n";
			$html .= ' </thead>'."\n";
			$html .= ' <tbody>'."\n";
			if(count($history)>0) {
				foreach($history as $item)
				{
					$html .= '  <tr>'."\n";
					$html .= '   <td>'.JHTML::_('date',$item->created, '%d %b, %Y %I:%M %p').'</td>'."\n";
					$html .= '   <td>'.$item->description.'</td>'."\n";
					$html .= '   <td>'.$item->category.'</td>'."\n";
					$html .= '   <td>'.$item->type.'</td>'."\n";
					if($item->type == 'withdraw') {
						$html .= '   <td class="aRight"><span style="color: red;">-'.$item->amount.'</span></td>'."\n";
					} else if ($item->type == 'hold') {
						$html .= '   <td class="aRight"><span style="color: #999;"> '.$item->amount.'</span></td>'."\n";
					}
					else {
						$html .= '   <td class="aRight"><span style="color: green;">+'.$item->amount.'</span></td>'."\n";
					}
					$html .= '   <td class="aRight">'.$item->balance.'</td>'."\n";
					$html .= '  </tr>'."\n";
				}
			}
			else {
			$html .= '  <tr>'."\n";
			$html .= '   <td colspan="6">There is no information available on this user\'s transactions.</td>'."\n";
			$html .= '  </tr>'."\n";
			}
			$html .= ' </tbody>'."\n";
			$html .= '</table>'."\n";
			echo $html;
?>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="save" />
        		
		</form>
		<?php
	}

	//-----------
	
	function find( $option ) 
	{
		UserpointsHTML::menutop();
		?>
       
			<ul id="submenu">
			 	<li><a href="index2.php?option=<?php echo $option; ?>">Summary</a></li> 
                <li><a href="index2.php?option=<?php echo $option; ?>&amp;task=edit"  class="active">Look up User Balance</a></li>
			 	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=config">Configuration</a></li> 
             	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=batch">Batch Transaction</a></li>
			</ul>
       <?php   UserpointsHTML::menubottom(); ?>
		<form action="index2.php" method="post" name="adminForm">

		<div class="clear"></div>
		<div class="column1">
				<table class="adminform">
				 <thead>
				  <tr>
				   <th colspan="2">Find User Details</th>
				  </tr>
				 </thead>
				 <tbody>
				  <tr>
				   <td><label for="uid">UID:</label></td>
				   <td><input type="text" name="uid" id="uid" size="30" maxlength="250" value="" /> <input type="submit" value="Go" /></td>
				  </tr>
				 </tbody>
				</table>
			</div>
			<div class="column2">
				<p>Enter a user ID to view their point history and balance.</p>
			</div>
			<div class="clear"></div>
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="edit" />
		</form>
		<?php
	}

	//-----------
	
	function config( &$params, $option ) 
	{
		$rows = 50;
		UserpointsHTML::menutop();
		?>
       
			<ul id="submenu">
			 	<li><a href="index2.php?option=<?php echo $option; ?>">Summary</a></li> 
                <li><a href="index2.php?option=<?php echo $option; ?>&amp;task=edit" >Look up User Balance</a></li>
			 	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=config"  class="active">Configuration</a></li> 
             	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=batch">Batch Transaction</a></li>
			</ul>
       <?php   UserpointsHTML::menubottom(); ?>
		
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
		 <thead>
		  <tr>
		   <th>#</th>
		   <th>Points</th>
		   <th>Alias</th>
		   <th>Description</th>
		  </tr>
		 </thead>
		 <tbody>
<?php
		$i = 1;
		for ( $r = 0; $r < $rows; $r++ ) {
?>
		  <tr>
		   <td>(<?php echo $i; ?>)</td>
		   <td><input type="text" name="points[<?php echo $i; ?>]" value="<?php echo @$params[$i-1]->points; ?>" size="10" maxlength="10" /></td>
		   <td><input type="text" name="alias[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$params[$i-1]->alias ); ?>" size="20" maxlength="50" /></td>
		   <td><input type="text" name="description[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$params[$i-1]->description ); ?>" size="50" maxlength="255" /></td>
<?php
				$i++;
?>
		  </tr>
<?php } ?>
		 </tbody>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
	//-----------
	
	function batch($option ) 
	{
		UserpointsHTML::menutop();
		?>
       
			<ul id="submenu">
			 	<li><a href="index2.php?option=<?php echo $option; ?>">Summary</a></li> 
                <li><a href="index2.php?option=<?php echo $option; ?>&amp;task=edit" >Look up User Balance</a></li>
			 	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=config">Configuration</a></li> 
             	<li><a href="index2.php?option=<?php echo $option; ?>&amp;task=batch"   class="active">Batch Transaction</a></li>
			</ul>
       <?php   UserpointsHTML::menubottom(); ?>
		
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
				 <thead>
				  <tr>
				   <th colspan="2">Process batch transaction</th>
				  </tr>
				 </thead>
				 <tbody>
                  <tr>
				   <td><label for="type">Transaction Type:</label></td>
				   <td><select name="type" id="type">
						<option>deposit</option>
						<option>withdraw</option>
				   </select></td>
				  </tr>
				  <tr>
				   <td><label for="amount">Amount:</label></td>
				   <td><input type="text" name="amount" id="amount"  maxlength="11" value="" /></td>
				  </tr>
				  <tr>
				   <td><label for="description">Description:</label></td>
				   <td><input type="text" name="description" id="description"  maxlength="250"style="width:100%"  value="" /></td>
				  </tr>
                    <tr>
				   <td><label for="users">User list</label></td>
				   <td><textarea name="users" id="users" rows="10" style="width:100%"></textarea>
                   <br /> Enter a comma-separated list of userids.</td>
				  </tr>
                  <thead>
				  <tr>
				   <th colspan="2">Transaction log details</th>
				  </tr>
				 </thead>
                  <tr>
				   <td><label for="com">Category / Component</label></td>
				   <td><input type="text" name="com" id="com" size="30" maxlength="250" value="" />
                   <br />E.g. answers, survey, etc.</td>
				  </tr>
                  <tr>
				   <td><label for="action">Action type</label></td>
				   <td><input type="text" name="action" id="action" size="30" maxlength="250" value="" />
                   <br /> E.g. royalty, setup, etc.</td>
				  </tr>
                  <tr>
				   <td><label for="ref">Reference id (optional)</label></td>
				   <td><input type="text" name="ref" id="ref" size="30" maxlength="250" value="" /></td>
				  </tr>
                 
				 </tbody>
		</table>
        <input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="process_batch" />
        <input type="submit" name="submit" value="Process batch transaction" />	
		</form>
		<?php
	}
	//----------
}
?>