<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_RELEVANT_PHP_SETTINGS'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SETTING'); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SAFE_MODE'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('boolean', $this->php_settings['safe_mode']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_OPEN_BASEDIR'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('string', $this->php_settings['open_basedir']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DISPLAY_ERRORS'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('boolean', $this->php_settings['display_errors']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SHORT_OPEN_TAGS'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('boolean', $this->php_settings['short_open_tag']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_FILE_UPLOADS'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('boolean', $this->php_settings['file_uploads']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_OUTPUT_BUFFERING'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('boolean', $this->php_settings['output_buffering']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SESSION_SAVE_PATH'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('string', $this->php_settings['session.save_path']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SESSION_AUTO_START'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('integer', $this->php_settings['session.auto_start']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_XML_ENABLED'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('set', $this->php_settings['xml']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_ZLIB_ENABLED'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('set', $this->php_settings['zlib']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_ZIP_ENABLED'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('set', $this->php_settings['zip']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DISABLED_FUNCTIONS'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('string', $this->php_settings['disable_functions']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_MBSTRING_ENABLED'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('set', $this->php_settings['mbstring']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_ICONV_AVAILABLE'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('set', $this->php_settings['iconv']); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
