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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="index.php" method="post" id="hubForm<?php if (JRequest::getInt('no_html', 0)) { echo '-ajax'; }; ?>" class="member-address-form">
	<?php if (!JRequest::getInt('no_html', 0)) : ?>
	<div class="explaination">
		<h3><?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE'); ?></h3>
		<p><?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE_EXPLANATION') ;?></p>
		<p><a class="icon-prev btn" href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=profile&action=manageaddresses'); ?>"><?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE'); ?></a></p>
	</div>
	<?php endif; ?>
	<fieldset>
		<legend><?php echo ($this->address->id != 0) ? JText::_('PLG_MEMBERS_PROFILE_ADDRESS_EDIT') : JText::_('PLG_MEMBERS_PROFILE_ADDRESS_ADD'); ?></legend>
		<p><?php echo Jtext::sprintf('PLG_MEMBERS_PROFILE_ADDRESS_LOCATE', 'javascript:;'); ?></p>
		<label>
			<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_TO'); ?>
			<input type="text" name="address[addressTo]" value="<?php echo $this->escape($this->address->addressTo); ?>" />
		</label>
		<label>
			<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_LINE1'); ?>
			<input type="text" name="address[address1]" id="address1" value="<?php echo $this->escape($this->address->address1); ?>" />
		</label>
		<label>
			<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_LINE2'); ?>
			<input type="text" name="address[address2]" id="address2" value="<?php echo $this->escape($this->address->address2); ?>" />
		</label>
		<label>
			<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_CITY'); ?>
			<input type="text" name="address[addressCity]" id="addressCity" value="<?php echo $this->escape($this->address->addressCity); ?>" />
		</label>
		<label>
			<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_PROVINCE'); ?>
			<input type="text" name="address[addressRegion]" id="addressRegion" value="<?php echo $this->escape($this->address->addressRegion); ?>" />
		</label>
		<label>
			<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_POSTALCODE'); ?>
			<input type="text" name="address[addressPostal]" id="addressPostal" value="<?php echo $this->escape($this->address->addressPostal); ?>" />
		</label>
		<label>
			<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_COUNTRY'); ?>
			<?php
				$countries = \Hubzero\Geocode\Geocode::countries();
			?>
			<select name="address[addressCountry]" id="addressCountry">
				<option value=""><?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS_SELECT_COUNTRY'); ?></option>
				<?php foreach ($countries as $country) : ?>
					<?php $sel = ($country->name == $this->address->addressCountry) ? 'selected="selected"' : ''; ?>
					<option <?php echo $sel; ?> value="<?php echo $country->name; ?>"><?php echo $this->escape($country->name); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<input type="hidden" name="address[addressLatitude]" id="addressLatitude" value="<?php echo $this->escape($this->address->addressLatitude); ?>" />
		<input type="hidden" name="address[addressLongitude]" id="addressLongitude" value="<?php echo $this->escape($this->address->addressLongitude); ?>" />
	</fieldset>
	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_PROFILE_SAVE'); ?>" />
	</p>
	<input type="hidden" name="option" value="com_members" />
	<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
	<input type="hidden" name="active" value="profile" />
	<input type="hidden" name="action" value="saveaddress" />
	<input type="hidden" name="address[id]" value="<?php echo $this->addressId; ?>" />
</form>