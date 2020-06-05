<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SYSTEM_LDAP_CONFIGURATION'), 'config');
Toolbar::preferences($this->option, '550');

$this->css('ldap')
	->js('ldap');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_SYSTEM_LDAP_HUBCONFIG'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><input type="submit" name="importHubConfig" id="importHubConfig" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_IMPORT'); ?>" /></td>
							<td><?php echo Lang::txt('COM_SYSTEM_LDAP_IMPORT_HUBCONFIG'); ?></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
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
							<td class="key"><input type="submit" name="deleteUsers" id="deleteUsers" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_DELETE_FROM_LDAP'); ?>" /></td>
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
							<td class="key"><input type="submit" name="exportGroups" id="exportGroups" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_EXPORT_TO_LDAP'); ?>" /></td>
							<td><?php echo Lang::txt('COM_SYSTEM_LDAP_EXPORT_GROUPS_TO_LDAP'); ?></td>
						</tr>
						<tr>
							<td class="key"><input type="submit" name="deleteGroups" id="deleteGroups" value="<?php echo Lang::txt('COM_SYSTEM_LDAP_DELETE_FROM_LDAP'); ?>" /></td>
							<td><?php echo Lang::txt('COM_SYSTEM_LDAP_DELETE_GROUPS_FROM_LDAP'); ?></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
</form>