<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>

	<fieldset class="batch">
		<legend><?php echo Lang::txt('COM_REDIRECT_HEADING_UPDATE_LINKS'); ?></legend>

		<div class="grid">
			<div class="col span8">
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>">
					<label for="new_url"><?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL'); ?></label>
					<input type="text" name="new_url" id="new_url" value="" size="50" title="<?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>">
					<label for="comment"><?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL'); ?></label>
					<input type="text" name="comment" id="comment" value="" size="50" title="<?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>" />
				</div>
			</div>
			<div class="col span4">
				<div class="input-wrap">
					<button type="button" id="update-links"><?php echo Lang::txt('COM_REDIRECT_BUTTON_UPDATE_LINKS'); ?></button>
				</div>
			</div>
		</div>
	</fieldset>
