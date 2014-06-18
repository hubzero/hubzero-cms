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

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="grid break4">
			<div id="cartcontent" class="col span8 cf">
		<?php if ($this->getError()) { ?>
				<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
				<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
					<fieldset>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
						<input type="hidden" name="task" value="finalize" />
						<input type="hidden" name="action" value="" />
						<input type="hidden" name="name" value="<?php echo (isset($this->posted['name'])) ? $this->escape($this->posted['name']) : $this->escape($this->juser->get('name')); ?>" />
						<input type="hidden" name="address" value="<?php echo (isset($this->posted['address'])) ? $this->escape($this->posted['address']) : ''; ?>" />
						<input type="hidden" name="address1" value="<?php echo (isset($this->posted['address1'])) ? $this->escape($this->posted['address1']) : ''; ?>" />
						<input type="hidden" name="address2" value="<?php echo (isset($this->posted['address2'])) ? $this->escape($this->posted['address2']) : ''; ?>" />
						<input type="hidden" name="city" value="<?php echo (isset($this->posted['city'])) ? $this->escape($this->posted['city']) : ''; ?>" />
						<input type="hidden" name="state" value="<?php echo (isset($this->posted['state'])) ? $this->escape($this->posted['state']) : ''; ?>" />
						<input type="hidden" name="country" value="<?php echo (isset($this->posted['country'])) ? $this->escape($this->posted['country']) : $this->escape(\Hubzero\Geocode\Geocode::getcountry($this->xprofile->get('countryresident'))); ?>" />
						<input type="hidden" name="postal" value="<?php echo (isset($this->posted['postal'])) ? $this->escape($this->posted['postal']) : ''; ?>" />
						<input type="hidden" name="phone" value="<?php echo (isset($this->posted['phone'])) ? $this->escape($this->posted['phone']) : ''; ?>" />
						<input type="hidden" name="email" value="<?php echo (isset($this->posted['email'])) ? $this->escape($this->posted['email']) : $this->juser->get('email'); ?>" />
						<input type="hidden" name="comments" value="<?php echo (isset($this->posted['comments'])) ? $this->escape($this->posted['comments']) : ''; ?>" />
						
						<h3><?php echo JText::_('COM_STORE_ORDER_WILL_SHIP'); ?></h3>
						<pre><?php echo (isset($this->posted['name'])) ? $this->escape($this->posted['name']) : $this->escape($this->juser->get('name')); ?>
						 
		<?php echo (isset($this->posted['address'])) ? $this->escape($this->posted['address']) : ''; ?>
		
		<?php echo (isset($this->posted['country'])) ? $this->escape($this->posted['country']) : $this->escape(\Hubzero\Geocode\Geocode::getcountry($this->xprofile->get('countryresident'))); ?></pre>
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
						<legend><?php echo JText::_('COM_STORE_ADDITIONAL_COMMENTS'); ?></legend>
						<p><?php echo $this->posted['comments']; ?></p>
					</fieldset>
		<?php } ?>
					<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=cart&action=empty'); ?>" class="actionlink"><?php echo JText::_('COM_STORE_CANCEL_ORDER'); ?></a></p>
					<div class="clear"></div>
					<p class="process"><input type="submit" class="button finalize_order" value="finalize" /></p>
				</form>
			</div><!-- / #cartcontent -->

			<div id="balanceupdate" class="col span4 omega">
				<div class="order_summary">
					<h4><span class="coin">&nbsp;</span><?php echo JText::_('COM_STORE_ORDER_SUMMARY'); ?></h4>
		<?php
			foreach ($this->items as $item)
			{
		?>
					<p>
						<?php echo \Hubzero\Utility\String::truncate($item->title, 28); ?>
		<?php if ($item->selectedsize) { ?>
					</p>
					<p>
						<?php echo JText::_('COM_STORE_SIZE') . ' ' . $item->selectedsize . ' (x ' . $item->quantity . ')'; ?>
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
					<p><a class="actionlink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=cart'); ?>"><?php echo JText::_('COM_STORE_CHANGE_ORDER'); ?></a></p>
				</div><!-- / .order_summary -->
		<?php if (!$this->final) { ?>
				<p class="sidenotes"><?php echo JText::_('COM_STORE_MSG_CHANCE_TO_REVIEW'); ?></p>
				<p class="sidenotes"><span class="sidetitle"><?php echo JText::_('COM_STORE_SHIPPING'); ?></span><?php echo JText::_('COM_STORE_MSG_SHIPPING'); ?></p>
				<p class="sidenotes"><span class="sidetitle"><?php echo JText::_('COM_STORE_NO_RETURNS'); ?></span><?php echo JText::_('COM_STORE_MSG_NO_RETURNS'); ?> <?php echo JText::_('COM_STORE_MSG_CONTACT_SUPPORT'); ?> <a href="<?php echo JRoute::_('index.php?option=com_support'); ?>"><?php echo JText::_('COM_STORE_SUPPORT'); ?></a>.</p>
		<?php } ?>
				<p class="sidenotes"><?php echo JText::_('COM_STORE_CONSULT'); ?> <a href="/legal/terms"><?php echo JText::_('COM_STORE_TERMS'); ?></a></p>
			</div><!-- / #balanceupdate -->
		</div><!-- / .grid -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
