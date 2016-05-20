<fieldset class="adminform">
	<legend><span>General Software-related Options</span></legend>

	<div class="input-wrap">
		<label for="eula"><?php echo 'EULA (overrides product-level EULA)' ?>: </label><br />
		<?php
		$skuMeta = $this->skuMeta;

		$eula = '';
		if (isset($skuMeta['eula']) && !empty($skuMeta['eula']))
		{
			$eula = $skuMeta['eula'];
		}

		$downloadFile = '';
		if (isset($skuMeta['downloadFile']) && !empty($skuMeta['downloadFile']))
		{
			$downloadFile = $skuMeta['downloadFile'];
		}

		$serialManagement = '';
		if (isset($skuMeta['serialManagement']) && !empty($skuMeta['serialManagement']))
		{
			$serialManagement = $skuMeta['serialManagement'];
		}

		$serial = '';
		if (isset($skuMeta['serial']) && !empty($skuMeta['serial']))
		{
			$serial = $skuMeta['serial'];
		}

		$downloadLimit = '';
		if (isset($skuMeta['downloadLimit']) && !empty($skuMeta['downloadLimit']))
		{
			$downloadLimit = $skuMeta['downloadLimit'];
		}

		$globalDownloadLimit = '';
		if (isset($skuMeta['globalDownloadLimit']) && !empty($skuMeta['globalDownloadLimit']))
		{
			$globalDownloadLimit = $skuMeta['globalDownloadLimit'];
		}

		//echo JFactory::getEditor()->display('fields[meta][eula]', $this->escape(stripslashes($eula)), '', '', 50, 10, false, 'eula');
		echo $this->editor('fields[meta][eula]', $this->escape(stripslashes($eula)), 50, 10, 'eula', array('buttons' => false));
		?>
	</div>

	<div class="input-wrap">
		<label for="field-download-file"><?php echo 'Download file'; ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
		<input type="text" name="fields[meta][downloadFile]" id="field-download-file" size="30" maxlength="100" value="<?php echo $downloadFile; ?>" />
	</div>

	<div class="input-wrap">
		<label for="field-globalDownloadLimit"><?php echo 'Total Downloads Limit'; ?>:</label><br />
		<input type="text" name="fields[meta][globalDownloadLimit]" id="field-globalDownloadLimit" size="30" maxlength="100" value="<?php echo $globalDownloadLimit; ?>" />
	</div>

	<div class="input-wrap">
		<label for="field-downloadLimit"><?php echo 'Downloads Limit per Single User'; ?>:</label><br />
		<input type="text" name="fields[meta][downloadLimit]" id="field-downloadLimit" size="30" maxlength="100" value="<?php echo $downloadLimit; ?>" />
	</div>

</fieldset>

<fieldset class="adminform">
	<legend><span>Serial numbers</span></legend>

	<div class="input-wrap">
		<label for="field-serialManagement">Serial Number Management</label>
		<select name="fields[meta][serialManagement]" id="field-serialManagement">
			<option value=""<?php if (!$serialManagement) { echo ' selected="selected"'; } ?>>No management</option>
			<option value="single"<?php if ($serialManagement == "single") { echo ' selected="selected"'; } ?>>Single Universal Number</option>
			<option value="multiple"<?php if ($serialManagement == "multiple") { echo ' selected="selected"'; } ?>>Multiple Unique Numbers</option>
		</select>
	</div>

	<div class="input-wrap" data-hint="When 'Single Number' is selected from the 'Serial Number Management'">
		<label for="field-serial"><?php echo 'Single Serial Number'; ?>:</label><br />
		<input type="text" name="fields[meta][serial]" id="field-serial" size="30" maxlength="100" value="<?php echo $serial; ?>" />
	</div>

	<?php
	if ($serialManagement == "multiple") {
	?>
		<p>
			<a class="options-link" href="<?php echo 'index.php?option=' . $this->parent->option . '&controller=serialst&id=' . $this->parent->row->getId(); ?>">Manage multiple serial numbers</a>
		</p>
	<?php
	}
	?>
</fieldset>