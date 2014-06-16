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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section">
	<div id="cartcontent">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="process" />

			<fieldset>
				<legend><?php echo JText::_('COM_STORE_SHIPPING_ADDRESS'); ?></legend>

				<label for="name">
					<?php echo JText::_('COM_STORE_RECEIVER_NAME'); ?> <span class="required"><?php echo JText::_('COM_STORE_REQUIRED'); ?></span>
					<input name="name" id="name" type="text" value="<?php echo (isset($this->posted['name'])) ? $this->escape($this->posted['name']) : $this->escape($this->juser->get('name')); ?>" />
				</label>
				
				<label for="address">
					<?php echo JText::_('COM_STORE_COMPLETE_ADDRESS'); ?> <span class="required"><?php echo JText::_('COM_STORE_REQUIRED'); ?></span>
					<textarea name="address" id="address" rows="10" cols="50"><?php echo (isset($this->posted['address'])) ? $this->escape($this->posted['address']) : ''; ?></textarea>
				</label>
				
				<p class="hint"><?php echo JText::_('COM_STORE_ADDRESS_MSG'); ?></p>
				
				<label for="country">
					<?php echo JText::_('COM_STORE_COUNTRY'); ?> <span class="required"><?php echo JText::_('COM_STORE_REQUIRED'); ?></span>
					<select name="country" id="country">
						<option value=""><?php echo JText::_('(select from list)'); ?></option>
<?php 
	
	$countries = \Hubzero\Geocode\Geocode::countries();
	
	$mycountry = (isset($this->posted['country'])) ? $this->posted['country'] : \Hubzero\Geocode\Geocode::getcountry($this->xprofile->get('countryresident'));
	foreach ($countries as $country)
	{
?>
						<option value="<?php echo htmlentities($country->name); ?>"<?php echo ($country->name == $mycountry) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($country->name); ?></option>
<?php
	}
?>
					</select>
				</label>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_STORE_CONTACT_INFO'); ?></legend>
				
				<label for="phone">
					<?php echo JText::_('COM_STORE_CONTACT_PHONE'); ?>
					<input name="phone" id="phone" type="text" value="<?php echo (isset($this->posted['phone'])) ? $this->escape($this->posted['phone']) : $this->escape($this->juser->get('phone')); ?>" />
				</label>
				
				<label for="email">
					<?php echo JText::_('COM_STORE_CONTACT_EMAIL'); ?>
					<input name="email" id="email" type="text" value="<?php echo (isset($this->posted['email'])) ? $this->escape($this->posted['email']) : $this->escape($this->juser->get('email')); ?>" />
				</label>
				<p class="hint"><?php echo JText::_('COM_STORE_CONTACT_MSG'); ?></p>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_STORE_ADDITIONAL_COMMENTS'); ?></legend>
				
				<label for="comments">
					<?php echo JText::_('COM_STORE_DETAILS'); ?>
					<textarea name="comments" id="comments" rows="10" cols="50"><?php echo (isset($this->posted['comments'])) ? $this->escape($this->posted['comments']) : ''; ?></textarea>
				</label>
			</fieldset>
			<p class="process"><input type="submit" class="button process_order" value="process" /></p>
			<span class="confirm">(<?php echo JText::_('COM_STORE_NOTE_NOCHARGE'); ?>)</span>
		</form>
	</div>
	
	<div id="balanceupdate">
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
	
	<div class="clear"></div>
</div><!-- / .main section -->
