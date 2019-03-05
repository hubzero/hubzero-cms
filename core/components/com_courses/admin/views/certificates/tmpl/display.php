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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_CERTIFICATE'), 'courses.png');
Toolbar::custom('preview', 'preview', '', 'COM_COURSES_PREVIEW', false);
if ($canDo->get('core.edit'))
{
	Toolbar::spacer();
	Toolbar::apply();
	Toolbar::save();
}
if ($canDo->get('core.delete'))
{
	Toolbar::spacer();
	Toolbar::deleteList();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('certificates');

Html::behavior('framework');

$this->css('certificates.css')
	->js('certificates.js');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>

	<fieldset class="placeholders">
		<div class="grid">
			<div class="col span7">
				<button class="placeholder" data-id="username"><?php echo Lang::txt('COM_COURSES_BTN_USERNAME'); ?></button>
				<button class="placeholder" data-id="name"><?php echo Lang::txt('COM_COURSES_BTN_NAME'); ?></button>
				<button class="placeholder" data-id="date"><?php echo Lang::txt('COM_COURSES_BTN_DATE'); ?></button>
				<button class="placeholder" data-id="email"><?php echo Lang::txt('COM_COURSES_BTN_EMAIL'); ?></button>
				<button class="placeholder" data-id="course"><?php echo Lang::txt('COM_COURSES_BTN_COURSE'); ?></button>
				<button class="placeholder" data-id="offering"><?php echo Lang::txt('COM_COURSES_BTN_OFFERING'); ?></button>
				<button class="placeholder" data-id="section"><?php echo Lang::txt('COM_COURSES_BTN_SECTION'); ?></button>
			</div>
			<div class="col span5">
				<button class="delete" id="clear-canvas" data-id="clear"> <?php echo Lang::txt('COM_COURSES_BTN_CLEAR'); ?></button>
			</div>
		</div>
	</fieldset>

	<div id="certificate" data-width="<?php echo $this->certificate->properties()->width; ?>" data-height="<?php echo $this->certificate->properties()->height; ?>">
		<?php
			$layout = array(
				'width'  => $this->certificate->properties()->width,
				'height' => $this->certificate->properties()->height
			);

			$this->certificate->eachPage(function($src, $idx) use ($layout)
			{
				echo '<img src="' . $src . '" id="page-' . $idx . '" width="' . $layout['width'] . '" height="' . $layout['height'] . '" alt="" />';
			});
		?>
		<canvas id="secondLayer" data-dimensions="" width="<?php echo $this->certificate->properties()->width; ?>" height="<?php echo $this->certificate->properties()->height; ?>"></canvas>
	</div>

	<input type="hidden" name="fields[properties]" id="field-properties" value="<?php echo $this->escape($this->certificate->get('properties')); ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->certificate->get('id'); ?>" />
	<input type="hidden" name="fields[course_id]" value="<?php echo $this->certificate->get('course_id'); ?>" />

	<input type="hidden" name="certificate" value="<?php echo $this->certificate->get('id'); ?>" />
	<input type="hidden" name="course" value="<?php echo $this->certificate->get('course_id'); ?>" />

	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
