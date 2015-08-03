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

		echo JFactory::getEditor()->display('fields[meta][eula]', $this->escape(stripslashes($eula)), '', '', 50, 10, false, 'eula');
		?>
	</div>
</fieldset>