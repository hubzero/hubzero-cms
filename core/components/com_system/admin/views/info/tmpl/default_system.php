<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'); ?></legend>
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
					<?php echo Lang::txt('COM_SYSTEM_INFO_PHP_BUILT_ON'); ?>
				</th>
				<td>
					<?php echo $this->info['php'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DATABASE_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['dbversion'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DATABASE_COLLATION'); ?>
				</th>
				<td>
					<?php echo $this->info['dbcollation'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PHP_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['phpversion'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_WEB_SERVER'); ?>
				</th>
				<td>
					<?php echo Html::system('server', $this->info['server']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_WEBSERVER_TO_PHP_INTERFACE'); ?>
				</th>
				<td>
					<?php echo $this->info['sapi_name'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_CMS_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['version'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PLATFORM_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['platform'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_USER_AGENT'); ?>
				</th>
				<td>
					<?php echo htmlspecialchars($this->info['useragent']);?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
