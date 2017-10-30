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

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOSTS') . ': '. $text, 'tools.png');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('host');

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-hostname"><?php echo Lang::txt('COM_TOOLS_FIELD_NAME'); ?>:</label><br />
					<input type="text" name="fields[hostname]" id="field-hostname" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->hostname)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-hosttype"><?php echo Lang::txt('COM_TOOLS_FIELD_TYPES'); ?>:</label><br />
					<select multiple="multiple" size="10" name="hosttype[]" id="field-hosttype">
					<?php
						for ($i=0; $i<count($this->hosttypes); $i++)
						{
							$r = $this->hosttypes[$i];
							if ((int)$r->value & (int)$this->row->provisions) { ?>
							<option selected="selected" value="<?php echo $r->name; ?>"><?php echo $r->name; ?></option>
							<?php } else { ?>
							<option value="<?php echo $r->name; ?>"><?php echo $r->name; ?></option>
							<?php }
						}
					?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-zone_id"><?php echo Lang::txt('COM_TOOLS_FIELD_ZONE'); ?>:</label><br />
					<select name="fields[zone_id]" id="field-zone_id">
						<option value="0"><?php echo Lang::txt('COM_TOOLS_SELECT'); ?></option>
						<?php
							if ($this->zones)
							{
								foreach ($this->zones as $zone)
								{
									?>
									<option<?php if ($zone->id == $this->row->zone_id) { echo ' selected="selected"'; } ?> value="<?php echo $zone->id; ?>"><?php echo $this->escape(stripslashes($zone->zone)); ?></option>
									<?php
								}
							}
						?>
					</select>
				</div>
			</fieldset>
		</div>
		<div class="col span6">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_FIELD_STATUS'); ?></th>
						<td><?php echo $this->escape($this->row->status); ?></td>
					</tr>
				</tbody>
			</table>

			<?php if (isset($this->toolCounts) && count($this->toolCounts) > 0) : ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_TOOLS_FIELDSET_SESSIONS'); ?></span></legend>

					<table class="admintable">
						<tbody>
							<?php foreach ($this->toolCounts as $c) : ?>
								<tr>
									<td><?php echo $c->appname; ?></td>
									<td><?php echo $c->count; ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</fieldset>
			<?php endif; ?>

			<?php if (isset($this->statusCounts) && count($this->statusCounts) > 0) : ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_TOOLS_FIELDSET_CONTAINERS'); ?></span></legend>

					<table class="admintable">
						<tbody>
							<?php foreach ($this->statusCounts as $c) : ?>
								<tr>
									<td><?php echo $c->status; ?></td>
									<td><?php echo $c->count; ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</fieldset>
			<?php endif; ?>
		</div>
	</div>

	<input type="hidden" name="fields[status]" value="<?php echo ($this->row->status) ? $this->row->status : 'check'; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->hostname; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
