<?php
$width  = intval( $params->get( 'width', 20 ) );
$text   = JText::_('SEARCH_BOX');
$clasfx = null;
?>
<form method="get" action="/search" id="searchform"<?php if ($clasfx) { echo ' class="'.$clasfx.'"'; } ?>>
	<fieldset>
		<legend><?php echo $text; ?></legend>
		<label for="searchword"><?php echo $text; ?></label>
		<input type="text" name="terms" id="searchword" size="<?php echo $width; ?>" value="<?php echo $text; ?>" />
	</fieldset>
</form>
