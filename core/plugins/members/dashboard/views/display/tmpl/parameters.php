<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// check to make sure we have some member dashboard params
$count = 0;
foreach ($this->fields as $field)
{
	if ($field->getAttribute('member_dashboard', 0) == 1)
	{
		$count++;
	}
}

// make sure we have at least one
if ($count < 1 || $this->admin)
{
	return '';
}

?>
<div class="module-settings">
	<h4><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MODULES_SETTINGS', $this->escape($this->module->title)); ?></h4>
	<form action="<?php echo Route::url('index.php?option=' . Request::getCmd('option', 'com_members')); ?>" method="post">
		<?php $i = 0; ?>
		<?php foreach ($this->fields as $field) : ?>
			<?php
				if (strtolower($field->type) == 'spacer')
				{
					continue;
				}

				if (!$field->getAttribute('member_dashboard', 0))
				{
					continue;
				}

				// set value based on hub & user pref
				$name = trim(str_replace('params[', '', rtrim($field->name, ']')));
				if (isset($this->params[$name]))
				{
					$field->setValue($this->params[$name]);
				}

				$i++;
			?>
			<label>
				<span class="tooltips" title="<?php echo Lang::txt($field->description); ?>">
					<?php echo $field->title; ?>:
				</span>
				<?php echo $field->input; ?>
			</label>
		<?php endforeach; ?>

		<?php echo Html::input('token'); ?>

		<div class="form-controls">
			<button class="btn btn-success save" type="submit"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS_SAVE'); ?></button>
			<button class="btn cancel" type="button"><?php echo Lang::txt('JCANCEL'); ?></button>
		</div>
	</form>
</div>