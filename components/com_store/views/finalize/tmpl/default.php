<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section">
	<div id="cartcontent">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option); ?>">
			<fieldset>
				<input type="hidden" name="task" value="finalize" />
				<input type="hidden" name="action" value="" />
				<input type="hidden" name="name" value="<?php echo (isset($this->posted['name'])) ? htmlentities($this->posted['name'],ENT_COMPAT,'UTF-8') : htmlentities($this->juser->get('name'),ENT_COMPAT,'UTF-8'); ?>" />
				<input type="hidden" name="address" value="<?php echo (isset($this->posted['address'])) ? htmlentities($this->posted['address'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				<input type="hidden" name="address1" value="<?php echo (isset($this->posted['address1'])) ? htmlentities($this->posted['address1'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				<input type="hidden" name="address2" value="<?php echo (isset($this->posted['address2'])) ? htmlentities($this->posted['address2'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				<input type="hidden" name="city" value="<?php echo (isset($this->posted['city'])) ? htmlentities($this->posted['city'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				<input type="hidden" name="state" value="<?php echo (isset($this->posted['state'])) ? htmlentities($this->posted['state'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				<input type="hidden" name="country" value="<?php echo (isset($this->posted['country'])) ? htmlentities($this->posted['country'],ENT_COMPAT,'UTF-8') : htmlentities(Hubzero_Geo::getcountry($this->xprofile->get('countryresident')),ENT_COMPAT,'UTF-8'); ?>" />
				<input type="hidden" name="postal" value="<?php echo (isset($this->posted['postal'])) ? htmlentities($this->posted['postal'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				<input type="hidden" name="phone" value="<?php echo (isset($this->posted['phone'])) ? htmlentities($this->posted['phone'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				<input type="hidden" name="email" value="<?php echo (isset($this->posted['email'])) ? htmlentities($this->posted['email'],ENT_COMPAT,'UTF-8') : $this->juser->get('email'); ?>" />
				<input type="hidden" name="comments" value="<?php echo (isset($this->posted['comments'])) ? htmlentities($this->posted['comments'],ENT_COMPAT,'UTF-8') : ''; ?>" />
				
				<h3><?php echo JText::_('COM_STORE_ORDER_WILL_SHIP'); ?></h3>				
				 <pre><?php echo (isset($this->posted['name'])) ? htmlentities($this->posted['name'],ENT_COMPAT,'UTF-8') : htmlentities($this->juser->get('name'),ENT_COMPAT,'UTF-8'); ?>
				 
<?php echo (isset($this->posted['address'])) ? htmlentities($this->posted['address'],ENT_COMPAT,'UTF-8') : ''; ?>

<?php echo (isset($this->posted['country'])) ? htmlentities($this->posted['country'],ENT_COMPAT,'UTF-8') : htmlentities(Hubzero_Geo::getcountry($this->xprofile->get('countryresident')),ENT_COMPAT,'UTF-8'); ?></pre>				
				<p><a class="actionlink" href="javascript:void(0);" id="change_address"><?php echo JText::_('COM_STORE_CHANGE_ADDRESS'); ?></a></p>
			</fieldset>
			<fieldset>
				<h3><?php echo JText::_('COM_STORE_CONTACT_INFO'); ?></h3>
				<p>
<?php if (isset($this->posted['phone'])) { ?>
					<?php echo JText::_('Phone'); ?>: <?php echo $this->posted['phone']; ?><br />
<?php } ?>
<?php if (isset($this->posted['email'])) { ?>
					<?php echo JText::_('Email'); ?>: <?php echo $this->posted['email']; ?>
<?php } ?>
				</p>
			</fieldset>
<?php if (isset($this->posted['comments']) && $this->posted['comments'] != '') { ?>
			<fieldset>
				<h3><?php echo JText::_('COM_STORE_ADDITIONAL_COMMENTS'); ?></h3>
				<p><?php echo $this->posted['comments']; ?></p>
			</fieldset>
<?php } ?>
			<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=cart&action=empty'); ?>" class="actionlink"><?php echo JText::_('COM_STORE_CANCEL_ORDER'); ?></a></p>
			<div class="clear"></div>
			<p class="process"><input type="submit" class="button finalize_order" value="finalize" /></p>
		</form>
	</div><!-- / #cartcontent -->
	
	<div id="balanceupdate">
		<div class="order_summary">
			<h4><span class="coin">&nbsp;</span><?php echo JText::_('COM_STORE_ORDER_SUMMARY'); ?></h4>
<?php
	foreach ($this->items as $item)
	{
?>
			<p>
				<?php echo Hubzero_View_Helper_Html::shortenText($item->title, 28, 0); ?>
<?php if ($item->selectedsize) { ?>
			</p>
			<p>
				<?php echo JText::_('COM_STORE_SIZE').' '.$item->selectedsize.' (x '.$item->quantity.')'; ?>
<?php } else if ($item->category != 'service') { ?>
				(x <?php echo $item->quantity; ?>)
<?php } ?>
				<span><?php echo ($item->price*$item->quantity); ?></span>
			</p>
<?php 
	}
?>
			<p><?php echo JText::_('COM_STORE_SHIPPING'); ?>: <span>0</span></p>
			<p class="totals"><?php echo JText::_('COM_STORE_TOTAL_POINTS'); ?>: <span><?php echo $this->cost; ?></span></p>
			<p><a class="actionlink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=cart'); ?>"><?php echo JText::_('COM_STORE_CHANGE_ORDER'); ?></a></p>
		</div><!-- / .order_summary -->
<?php if (!$this->final) { ?>
		<p class="sidenotes"><?php echo JText::_('COM_STORE_MSG_CHANCE_TO_REVIEW'); ?></p>
		<p class="sidenotes"><span class="sidetitle"><?php echo JText::_('COM_STORE_SHIPPING'); ?></span><?php echo JText::_('COM_STORE_MSG_SHIPPING'); ?></p>
		<p class="sidenotes"><span class="sidetitle"><?php echo JText::_('COM_STORE_NO_RETURNS'); ?></span><?php echo JText::_('COM_STORE_MSG_NO_RETURNS'); ?> <?php echo JText::_('COM_STORE_MSG_CONTACT_SUPPORT'); ?> <a href="<?php echo JRoute::_('index.php?option=com_support'); ?>"><?php echo JText::_('COM_STORE_SUPPORT'); ?></a>.</p>
<?php } ?>
		<p class="sidenotes"><?php echo JText::_('COM_STORE_CONSULT'); ?> <a href="/legal/terms"><?php echo JText::_('COM_STORE_TERMS'); ?></a></p>
	</div><!-- / #balanceupdate -->
	
	<div class="clear"></div>
</div><!-- / .main section -->
