<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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
				<input type="hidden" name="task" value="process" />
				<h3><?php echo JText::_('COM_STORE_SHIPPING_ADDRESS'); ?></h3>

				<label>
					<?php echo JText::_('COM_STORE_RECEIVER_NAME'); ?> <span class="required"><?php echo JText::_('COM_STORE_REQUIRED'); ?></span>
					<input name="name" id="name" type="text" value="<?php echo (isset($this->posted['name'])) ? $this->posted['name'] : $this->juser->get('name'); ?>" />
				</label>
				
				<label>
					<?php echo JText::_('COM_STORE_COMPLETE_ADDRESS'); ?> <span class="required"><?php echo JText::_('COM_STORE_REQUIRED'); ?></span>
					<textarea name="address" rows="10" cols="50"><?php echo (isset($this->posted['address'])) ? $this->posted['address'] : ''; ?></textarea>
				</label>
				
				<p class="hint"><?php echo JText::_('COM_STORE_ADDRESS_MSG'); ?></p>
				
				<label>
					<?php echo JText::_('COM_STORE_COUNTRY'); ?> <span class="required"><?php echo JText::_('COM_STORE_REQUIRED'); ?></span>
					<select name="country" id="country">
						<option value=""><?php echo JText::_('(select from list)'); ?></option>
<?php 
	$countries = GeoUtils::getcountries();
	$mycountry = (isset($this->posted['country'])) ? htmlentities(($this->posted['country'])) : htmlentities(GeoUtils::getcountry($this->xprofile->get('countryresident')),ENT_COMPAT,'UTF-8');
	foreach ($countries as $country) 
	{
?>
						<option value="<?php echo htmlentities($country['name']); ?>"<?php echo ($country['name'] == $mycountry) ? ' selected="selected"' : ''; ?>><?php echo htmlentities($country['name']); ?></option>
<?php
	}
?>
					</select>
				</label>
			</fieldset>
			<fieldset>
				<h3><?php echo JText::_('COM_STORE_CONTACT_INFO'); ?></h3>
				
				<label>
					<?php echo JText::_('COM_STORE_CONTACT_PHONE'); ?>
					<input name="phone" id="phone" type="text" value="<?php echo (isset($this->posted['phone'])) ? $this->posted['phone'] : htmlentities($this->juser->get('phone'),ENT_COMPAT,'UTF-8'); ?>" />
				</label>
				
				<label>
					<?php echo JText::_('COM_STORE_CONTACT_EMAIL'); ?>
					<input name="email" id="email" type="text" value="<?php echo (isset($this->posted['email'])) ? $this->posted['email'] : $this->juser->get('email'); ?>" />
				</label>
				<p class="hint"><?php echo JText::_('COM_STORE_CONTACT_MSG'); ?></p>
			</fieldset>
			<fieldset>
				<h3><?php echo JText::_('COM_STORE_ADDITIONAL_COMMENTS'); ?></h3>
				
				<label>
					<?php echo JText::_('COM_STORE_DETAILS'); ?>
					<textarea name="comments" rows="10" cols="50"><?php echo (isset($this->posted['comments'])) ? $this->posted['comments'] : ''; ?></textarea>
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