<?php // no direct access
defined('_HZEXEC_') or die();?>
<form method="get" action="/search" id="searchform"<?php if ($params->get('moduleclass_sfx')) { echo ' class="'.$params->get('moduleclass_sfx').'"'; } ?>>
	<fieldset>
		<legend><?php echo $text; ?></legend>
		<label for="searchword" id="searchword-label"><?php echo $text; ?></label>
		<input type="text" name="terms" id="searchword" size="<?php echo $width; ?>" placeholder="<?php echo $text; ?>" /><?php if ($params->get('button')) { echo '<input type="submit" name="submitquery" id="submitquery" value="' . $params->get('button_text') . '" />'; } ?>
	</fieldset>
</form>