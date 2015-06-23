<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
