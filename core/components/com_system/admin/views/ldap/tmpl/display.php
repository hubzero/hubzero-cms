<?php
/**
 * HUBzero CMS
 *
 * Copyright 2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SYSTEM_LDAP_CONFIGURATION'), 'config.png');
Toolbar::preferences($this->option, '550');

$this->css('ldap')
	->js('ldap');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><?php echo Lang::txt('COM_SYSTEM_LDAP_HUBCONFIG'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><input type="submit" name="importHubConfig" id="importHubConfig" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_IMPORT'); ?>" onclick="submitbutton('importHubConfig');" /></td>
						<td><?php echo Lang::txt('COM_SYSTEM_LDAP_IMPORT_HUBCONFIG'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<p class="warning"><?php echo Lang::txt('COM_SYSTEM_LDAP_WARNING_IRREVERSIBLE'); ?></p>

		<fieldset class="adminform">
			<legend><?php echo Lang::txt('COM_SYSTEM_LDAP_USERS'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><!-- onclick="submitbutton('exportUsers');" -->
							<input type="submit" name="exportUsers" id="exportUsers" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_EXPORT_TO_LDAP'); ?>" data-delay="3" data-start="0" data-progress="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=exportusersbatch&' . Session::getFormToken() . '=1&no_html=1&limit=' . $this->config->get('batch_limit', 1000) . '&start='); ?>" />
						</td>
						<td>
							<?php echo Lang::txt('COM_SYSTEM_LDAP_EXPORT_USERS_TO_LDAP'); ?>
							<div class="progress-container">
								<strong><?php echo Lang::txt('COM_SYSTEM_LDAP_RUN_PROGRESS'); ?> <span class="progress-percentage">0%</span></strong>
								<div class="progress"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="key"><input type="submit" name="deleteUsers" id="deleteUsers" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_DELETE_FROM_LDAP'); ?>" onclick="submitbutton('deleteUsers');" /></td>
						<td><?php echo Lang::txt('COM_SYSTEM_LDAP_DELETE_USERS_FROM_LDAP'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo Lang::txt('COM_SYSTEM_LDAP_GROUPS'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><input type="submit" name="exportGroups" id="exportGroups" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_EXPORT_TO_LDAP'); ?>" onclick="submitbutton('exportGroups');" /></td>
						<td><?php echo Lang::txt('COM_SYSTEM_LDAP_EXPORT_GROUPS_TO_LDAP'); ?></td>
					</tr>
					<tr>
						<td class="key"><input type="submit" name="deleteGroups" id="deleteGroups" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_DELETE_FROM_LDAP'); ?>" onclick="submitbutton('deleteGroups');" /></td>
						<td><?php echo Lang::txt('COM_SYSTEM_LDAP_DELETE_GROUPS_FROM_LDAP'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
</form>