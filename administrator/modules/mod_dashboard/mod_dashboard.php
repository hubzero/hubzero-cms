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

//if (!defined( '_JOS_QUICKICON_MODULE' ))
//{
	/** ensure that functions are declared only once */
	//define( '_JOS_QUICKICON_MODULE', 1 );

		$mosConfig_bankAccounts = 0;
		$database =& JFactory::getDBO();
		
		$xhub =& XFactory::getHub();
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$banking =  $upconfig->get('bankAccounts');
		$hubname = $xhub->getCfg('hubShortName'); 
				
		$threemonths 	= date( 'Y-m-d H:i:s', time() - (92 * 24 * 60 * 60));
		$onemonth 		= date( 'Y-m-d H:i:s', time() - (30 * 24 * 60 * 60) );
		
		if($banking) {		
			// get new store orders
			$database->setQuery( "SELECT count(*) FROM #__orders WHERE status=0");
			$orders = $database->loadResult();
		}
		
		// get open support tickets over 3 months old
		$sql = "SELECT count(*) FROM #__support_tickets WHERE status=1 AND created < '".$threemonths."' AND section!=2 AND type=0";
		$database->setQuery($sql);
		$oldtickets = $database->loadResult();
		
		// get unassigned support tickets
		$sql = "SELECT count(*) FROM #__support_tickets WHERE status=0 AND section!=2 AND type=0 AND owner is NULL AND report is NOT NULL";
		$database->setQuery($sql);
		$newtickets = $database->loadResult();
		
		// get abuse reports
		$sql = "SELECT count(*) FROM #__abuse_reports WHERE state=0";
		$database->setQuery($sql);
		$reports = $database->loadResult();
		
		// get pending resources
		$sql = "SELECT count(*) FROM #__resources WHERE published=3";
		$database->setQuery($sql);
		$pending = $database->loadResult();
		
		// get contribtool entries requiring admin attention
		$sql = "SELECT count(*) FROM #__tool AS t JOIN jos_tool_version as v ON v.toolid=t.id AND v.mw='narwhal' AND v.state=3  WHERE t.state=1 OR t.state=3 OR t.state=5 OR t.state=6";
		$database->setQuery($sql);
		$contribtool = $database->loadResult();
		
		// get recent quotes
		$sql = "SELECT count(*) FROM #__feedback WHERE date > '".$onemonth."'";
		$database->setQuery($sql);
		$quotes = $database->loadResult();
		
		// get wishes from main wishlist - to come
		$wishes = 0;
		
		// Check if component entry is there
		$database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_wishlist' AND enabled=1" );
		$found = $database->loadResult();
		
		if($found) {
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.plan.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.group.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.rank.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.attachment.php' );
			$obj = new Wishlist( $database );
			$objWish = new Wish( $database );
			$juser 	  =& JFactory::getUser();
			
			// Check if main wishlist exists, create one if missing
			$mainlist = $obj->get_wishlistID(1, 'general');
			if(!$mainlist) {
				$mainlist = $obj->createlist('general', 1);	
			}
			$filters = array('filterby'=>'pending', 'sortby'=>'date');
			$wishes = $objWish->get_wishes($mainlist, $filters, 1, $juser);
			$wishes = count($wishes);
		}
		
		// get styles
		ximport('xdocument');
		$document =& JFactory::getDocument();
		$document->addStyleSheet('/administrator/modules/mod_dashboard/dashboard.css');


?>
                <div id="dashboard">
                 <h3 class="hed-abuse"><a href="index.php?option=com_support&amp;task=abusereports">Abuse reports</a></h3>
                   <p class="<?php if ($reports) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($reports==1) ? 'is': 'are'; ?> <?php if($reports) { echo '<a href="index.php?option=com_support&amp;task=abusereports">'; } ?><?php echo $reports ?>  new abuse report<?php echo ($reports==1) ? '': 's'; ?><?php if($reports)  { echo '</a>';} ?>.</p>
                   
                 <h3 class="hed-support"><a href="index.php?option=com_support">Support tickets</a></h3>
                   <p class="<?php if ($newtickets) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($newtickets==1) ? 'is': 'are'; ?> <?php if($newtickets) { echo '<a href="index.php?option=com_support">'; } ?><?php echo $newtickets ?>  unassigned ticket<?php echo ($newtickets==1) ? '': 's'; ?><?php if($newtickets)  { echo '</a>';} ?>.</p>
                   
                   <p class="<?php if ($oldtickets) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($oldtickets==1) ? 'is': 'are'; ?> <?php if($oldtickets) { echo '<a href="index.php?option=com_support">'; } ?><?php echo $oldtickets ?>  ticket<?php echo ($newtickets==1) ? '': 's'; ?><?php if($oldtickets)  { echo '</a>';} ?> open for more than three months.</p>
                   
                 <h3 class="hed-resources"><a href="index.php?option=com_resources&amp;task=pending&amp;status=3">Pending resources</a></h3>
                   <p class="<?php if ($pending) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($pending==1) ? 'is': 'are'; ?> <?php if($pending) { echo '<a href="index.php?option=com_resources&amp;task=pending&amp;status=3">'; } ?><?php echo $pending ?>  pending resource<?php echo ($pending==1) ? '': 's'; ?><?php if($pending)  { echo '</a>';} ?>.</p>
                   
                 <h3 class="hed-contribtool"><a href="../index.php?option=com_contribtool">Tool contributions</a></h3>
                   <p class="<?php if ($contribtool) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($contribtool==1) ? 'is': 'are'; ?> <?php if($contribtool) { echo '<a href="../index.php?option=com_contribtool">'; } ?><?php echo $contribtool ?>  tool contribution<?php echo ($contribtool==1) ? '': 's'; ?><?php if($contribtool)  { echo '</a>';} ?> requiring administrator action.</p>
                   
    <?php if($banking) {?>
                 <h3 class="hed-store"><a href="index.php?option=com_store">Store orders</a></h3>
                   <p class="<?php if ($orders) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($orders==1) ? 'is': 'are'; ?> <?php if($orders) { echo '<a href="index.php?option=com_store&amp;task=orders&amp;filterby=new">'; } ?><?php echo $orders ?> new order<?php echo ($orders==1) ? '': 's'; ?><?php if($orders)  { echo '</a>';} ?> in the store.</p>
                   
    <?php } ?>
                 <h3 class="hed-quote"><a href="index.php?option=com_feedback">Success stories</a></h3>
                   <p class="<?php if ($quotes) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($quotes==1) ? 'is': 'are'; ?> <?php if($quotes) { echo '<a href="index.php?option=com_feedback">'; } ?><?php echo $quotes ?> new success stor<?php echo ($quotes==1) ? 'y': 'ies'; ?><?php if($quotes)  { echo '</a>';} ?> received in the past month.</p>
                   
                 <h3 class="hed-wish">Wishes</h3>
                   <p class="<?php if ($wishes) { echo 'p-attention';} else { echo'p-zeroitems' ;} ?>">There <?php echo ($wishes==1) ? 'is': 'are'; ?> <?php if($wishes) { echo '<a href="../index.php?option=com_wishlist">'; } ?><?php echo $wishes ?> open wish<?php echo ($wishes==1) ? '': 'es'; ?><?php if($wishes)  { echo '</a>';} ?> on the main <?php echo $hubname; ?> Wish List.</p>
                  
                </div>
             
	<?php
//}
