<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="module-list">
	<h2 class="section-header">
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_TITLE'); ?>
	</h2>
	<p class="warning"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_WARNING'); ?></p>
	<form action="index.php" method="post">
		<fieldset class="adminform">
			<div class="input-wrap">
				<label><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_TITLE'); ?> <span class="required"><?php echo Lang::txt('required'); ?></span></label><br />
				<select name="module">
					<option value=""><?php echo Lang::txt('- Select Module to Push -'); ?></option>
					<?php foreach ($this->modules as $module) : ?>
						<option value="<?php echo $module->id; ?>"><?php echo $module->title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_COLUMN'); ?></label><br />
					<select name="column">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_POSITION'); ?></label><br />
					<select name="position">
						<option value="first"><?php echo Lang::txt('First'); ?></option>
						<option value="last"><?php echo Lang::txt('Last'); ?></option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_WIDTH'); ?></label><br />
					<select name="width">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap" data-hint="<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_HEIGHT_HINT'); ?>">
					<label><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_HEIGHT'); ?></label><br />
					<select name="height">
						<option value="1">1</option>
						<option selected="selected" value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
					</select>
					<span class="hint"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_HEIGHT_HINT'); ?></span>
				</div>
			</div>
			<p class="submit">
				<button class="button dopush" type="submit"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_BUTTON'); ?></button>
			</p>

		</fieldset>
		<input type="hidden" name="option" value="com_members" />
		<input type="hidden" name="controller" value="plugins" />
		<input type="hidden" name="plugin" value="dashboard" />
		<input type="hidden" name="task" value="dopush" />
	</form>
</div>