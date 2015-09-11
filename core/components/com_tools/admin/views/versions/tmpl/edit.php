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

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': '. $text, 'tools.png');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('version');

Html::behavior('modal');
Html::behavior('switcher', 'submenu');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('item-form');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu">
						<li><a href="#" onclick="return false;" id="details" class="active"><?php echo Lang::txt('JDETAILS'); ?></a></li>
						<li><a href="#" onclick="return false;" id="zones"><?php echo Lang::txt('COM_TOOLS_FIELDSET_ZONES'); ?></a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="version-document">
		<?php if ($this->getError()) : ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php endif; ?>
		<div id="page-details" class="tab">
			<div class="col width-60 fltlft">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_TOOLS_FIELD_VERSION_DETAILS'); ?></span></legend>

					<div class="input-wrap">
						<label for="field-command"><?php echo Lang::txt('COM_TOOLS_FIELD_COMMAND'); ?>:</label><br />
						<input type="text" name="fields[vnc_command]" id="field-command" value="<?php echo $this->escape(stripslashes($this->row->vnc_command));?>" size="50" />
					</div>

					<div class="input-wrap">
						<label for="field-timeout"><?php echo Lang::txt('COM_TOOLS_FIELD_TIMEOUT'); ?>:</label><br />
						<input type="text" name="fields[vnc_timeout]" id="field-timeout" value="<?php echo $this->escape(stripslashes($this->row->vnc_timeout));?>" size="50" />
					</div>

					<div class="input-wrap">
						<label for="field-hostreq"><?php echo Lang::txt('COM_TOOLS_FIELD_HOSTREQ'); ?>:</label><br />
						<input type="text" name="fields[hostreq]" id="field-hostreq" value="<?php echo $this->escape(stripslashes(implode(', ', $this->row->hostreq)));?>" size="50" />
					</div>

					<div class="input-wrap">
						<label for="field-mw"><?php echo Lang::txt('COM_TOOLS_FIELD_MIDDLEWARE'); ?>:</label><br />
						<input type="text" name="fields[mw]" id="field-mw" value="<?php echo $this->escape(stripslashes($this->row->mw));?>" size="50" />
					</div>

					<div class="input-wrap">
						<label for="field-params"><?php echo Lang::txt('COM_TOOLS_FIELD_PARAMS'); ?>:</label><br />
						<textarea name="fields[params]" id="field-params" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->params));?></textarea>
					</div>
				</fieldset>
			</div>
			<div class="col width-40 fltrt">
				<table class="meta">
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_TOOLS_FIELD_TITLE'); ?>:</th>
							<td><?php echo $this->escape(stripslashes($this->parent->title));?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_TOOLS_FIELD_TOOLNAME'); ?>:</th>
							<td><?php echo $this->escape(stripslashes($this->parent->toolname));?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_TOOLS_FIELD_VERSION'); ?>:</th>
							<td><?php echo $this->escape($this->row->id);?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="clr"></div>

			<input type="hidden" name="fields[id]" value="<?php echo $this->parent->id; ?>" />
			<input type="hidden" name="fields[version]" value="<?php echo $this->row->id; ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</div>
		<div class="clr"></div>
	</div>
	<div id="page-zones" class="tab">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_TOOLS_FIELDSET_ZONES'); ?></span></legend>
			<?php if ($this->row->get('id')) : ?>
				<iframe width="100%" height="400" name="zoneslist" id="zoneslist" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=versions&task=displayZones&tmpl=component&version=' . $this->row->get('id')); ?>"></iframe>
			<?php endif; ?>
		</fieldset>
	</div>
	<?php echo Html::input('token'); ?>
</form>