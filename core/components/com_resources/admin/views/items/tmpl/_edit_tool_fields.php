<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<div class="input-wrap">
	<p class="warning"><?php echo Lang::txt('COM_RESOURCES_WARNING_TOOLS_USE_PIPELINE'); ?></p>
</div>
<div class="input-wrap">
	<label for="field-alias"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
	<input type="text" name="fields[alias]" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
</div>
<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_FIELD_CANONICAL_HINT'); ?>">
	<label for="attrib-canonical"><?php echo Lang::txt('COM_RESOURCES_FIELD_CANONICAL'); ?>:</label><br />
	<input type="text" name="attrib[canonical]" id="attrib-canonical" maxlength="250" value="<?php echo $this->row->attribs->get('canonical', ''); ?>" />
	<span class="hint"><?php echo Lang::txt('COM_RESOURCES_FIELD_CANONICAL_HINT'); ?></span>
</div>
<input type="hidden" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
<input type="hidden" name="fields[type]" id="type" value="7" />
