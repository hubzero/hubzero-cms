<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" id="hubForm<?php echo (Request::getInt('no_html', 0)) ? '-ajax' : ''; ?>" class="member-address-form">
	<?php if (!Request::getInt('no_html', 0)) : ?>
	<div class="explaination">
		<h3><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE'); ?></h3>
		<p><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE_EXPLANATION'); ?></p>
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