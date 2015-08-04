<fieldset class="adminform">
	<legend><span><?php echo 'Software-related options'; ?></span></legend>

	<div class="input-wrap">
		<label for="eula"><?php echo 'EULA (overrides product-level EULA)' ?>: </label><br />
		<?php
		$skuMeta = $this->row->getMeta();

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

		echo JFactory::getEditor()->display('fields[meta][eula]', $this->escape(stripslashes($eula)), '', '', 50, 10, false, 'eula');
		?>
	</div>

	<div class="input-wrap">
		<label for="field-download-file"><?php echo 'Download file'; ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
		<input type="text" name="fields[meta][downloadFile]" id="field-download-file" size="30" maxlength="100" value="<?php echo $downloadFile; ?>" />
	</div>
</fieldset>