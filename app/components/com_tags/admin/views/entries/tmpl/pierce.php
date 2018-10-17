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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_PIERCE'), 'tags');
if ($canDo->get('core.edit'))
{
	Toolbar::save('pierce');
}
Toolbar::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<p class="warning"><?php echo Lang::txt('COM_TAGS_PIERCED_EXPLANATION'); ?></p>

	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TAGS_PIERCING'); ?></span></legend>

				<div class="input-wrap">
					<ul>
						<?php
						foreach ($this->tags as $tag)
						{
							echo '<li>' . $this->escape(stripslashes($tag->get('raw_tag'))) . ' (' . $this->escape($tag->get('tag')) . ' - ' . $tag->objects()->total() . ')</li>' . "\n";
						}
						?>
					</ul>
				</div>
			</fieldset>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TAGS_PIERCE_TO'); ?></span></legend>

				<div class="input-wrap">
					<label for="newtag"><?php echo Lang::txt('COM_TAGS_NEW_TAG'); ?>:</label><br />
					<?php
					$tf = Event::trigger(
						'hubzero.onGetMultiEntry',
						array(
							array('tags', 'newtag', 'newtag')
						)
					);
					echo (count($tf) ? implode("\n", $tf) : '<input type="text" name="newtag" id="newtag" size="25" value="" />');
					?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="ids" value="<?php echo $this->idstr; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
	<input type="hidden" name="task" value="pierce" />

	<?php echo Html::input('token'); ?>
</form>