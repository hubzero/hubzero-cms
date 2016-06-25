<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" id="hubForm<?php if (Request::getInt('no_html', 0)) { echo '-ajax'; }; ?>" class="member-address-form">
	<?php if (!Request::getInt('no_html', 0)) : ?>
	<div class="explaination">
		<h3><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE'); ?></h3>
		<p><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE_EXPLANATION') ;?></p>
		<p><a class="icon-prev btn" href="<?php echo Route::url('index.php?option=com_members&id='.$this->member->get('id').'&active=profile&action=manageaddresses'); ?>"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE'); ?></a></p>
	</div>
	<?php endif; ?>
	<fieldset>
		<legend><?php echo ($this->address->id != 0) ? Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_EDIT') : Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_ADD'); ?></legend>
		<p><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_LOCATE', 'javascript:;'); ?></p>
		<label>
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_TO'); ?>
			<input type="text" name="address[addressTo]" value="<?php echo $this->escape($this->address->addressTo); ?>" />
		</label>
		<label>
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_LINE1'); ?>
			<input type="text" name="address[address1]" id="address1" value="<?php echo $this->escape($this->address->address1); ?>" />
		</label>
		<label>
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_LINE2'); ?>
			<input type="text" name="address[address2]" id="address2" value="<?php echo $this->escape($this->address->address2); ?>" />
		</label>
		<label>
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_CITY'); ?>
			<input type="text" name="address[addressCity]" id="addressCity" value="<?php echo $this->escape($this->address->addressCity); ?>" />
		</label>
		<label>
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_PROVINCE'); ?>
			<input type="text" name="address[addressRegion]" id="addressRegion" value="<?php echo $this->escape($this->address->addressRegion); ?>" />
		</label>
		<label>
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_POSTALCODE'); ?>
			<input type="text" name="address[addressPostal]" id="addressPostal" value="<?php echo $this->escape($this->address->addressPostal); ?>" />
		</label>
		<label>
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_COUNTRY'); ?>
			<?php
				$countries = \Hubzero\Geocode\Geocode::countries();
			?>
			<select name="address[addressCountry]" id="addressCountry">
				<option value=""><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_SELECT_COUNTRY'); ?></option>
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
		<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_SAVE'); ?>" />
	</p>
	<input type="hidden" name="option" value="com_members" />
	<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
	<input type="hidden" name="active" value="profile" />
	<input type="hidden" name="action" value="saveaddress" />
	<input type="hidden" name="address[id]" value="<?php echo $this->addressId; ?>" />
	<?php echo Html::input('token'); ?>
</form>