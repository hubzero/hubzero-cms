<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
			<button class="btn cancel" type="button"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS_CANCEL'); ?></button>
		</div>
	</form>
</div>