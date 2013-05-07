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
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="index.php" method="post" id="hubForm<?php if (JRequest::getInt('no_html', 0)) { echo '-ajax'; }; ?>" class="member-address-form">
	<?php if (!JRequest::getInt('no_html', 0)) : ?>
	<div class="explaination">
		<h3>Manage Addresses</h3>
		<p>Click the link below to go back and manage all my addresses</p>
		<p><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=profile&action=manageaddresses'); ?>">&larr; Manage Addresses</a></p>
	</div>
	<?php endif; ?>
	<fieldset>
		<legend><?php echo ($this->address->id != 0) ? 'Edit': 'Add'; ?> Address</legend>
		<p>Please fill in any of the following fields or <a id="locate-me" class="btn add locate" href="javascript:;" title="Locate Me">Click to Locate Me</a></p>
		<label>
			To/Label:
			<input type="text" name="address[addressTo]" value="<?php echo $this->address->addressTo; ?>" />
		</label>
		<label>
			Line 1:
			<input type="text" name="address[address1]" id="address1" value="<?php echo $this->address->address1; ?>" />
		</label>
		<label>
			Line 2:
			<input type="text" name="address[address2]" id="address2" value="<?php echo $this->address->address2; ?>" />
		</label>
		<label>
			City:
			<input type="text" name="address[addressCity]" id="addressCity" value="<?php echo $this->address->addressCity; ?>" />
		</label>
		<label>
			State/Providence/Region:
			<input type="text" name="address[addressRegion]" id="addressRegion" value="<?php echo $this->address->addressRegion; ?>" />
		</label>
		<label>
			Postal Code:
			<input type="text" name="address[addressPostal]" id="addressPostal" value="<?php echo $this->address->addressPostal; ?>" />
		</label>
		<label>
			Country:
			<?php
				ximport('Hubzero_Geo');
				$countries = Hubzero_Geo::getcountries();
			?>
			<select name="address[addressCountry]" id="addressCountry">
				<option value=""><?php echo JText::_('- Select Country &mdash;'); ?></option>
				<?php foreach($countries as $country) : ?>
					<?php $sel = ($country['name'] == $this->address->addressCountry) ? 'selected="selected"' : ''; ?>
					<option <?php echo $sel; ?> value="<?php echo $country['name']; ?>"><?php echo $country['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<input type="hidden" name="address[addressLatitude]" id="addressLatitude" value="<?php echo $this->address->addressLatitude; ?>" />
		<input type="hidden" name="address[addressLongitude]" id="addressLongitude" value="<?php echo $this->address->addressLongitude; ?>" />
	</fieldset>
	<p class="submit">
		<input type="submit" value="Submit" />
	</p>
	<input type="hidden" name="option" value="com_members" />
	<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
	<input type="hidden" name="active" value="profile" />
	<input type="hidden" name="action" value="saveaddress" />
	<input type="hidden" name="address[id]" value="<?php echo $this->addressId; ?>" />
</form>