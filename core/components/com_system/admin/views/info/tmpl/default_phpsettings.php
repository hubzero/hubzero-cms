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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
					<?php echo Lang::txt('COM_SYSTEM_INFO_MAGIC_QUOTES'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('boolean', $this->php_settings['magic_quotes_gpc']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_REGISTER_GLOBALS'); ?>
				</th>
				<td>
					<?php echo Html::phpsetting('boolean', $this->php_settings['register_globals']); ?>
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
