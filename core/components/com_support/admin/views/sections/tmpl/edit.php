<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$text = ( $this->task == 'edit' ? Lang::txt('Edit') : Lang::txt('New'));
Toolbar::title(Lang::txt('Ticket Section') . ': ' . $text, 'addedit.png');
Toolbar::save();
Toolbar::cancel();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancelsec') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	if ($('section').value == '') {
		alert( '<?php echo Lang::txt('SECTION_ERROR_NO_TEXT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="section"><?php echo Lang::txt('SECTION_TEXT'); ?>:</label></td>
					<td><input type="text" name="sec[section]" id="section" value="<?php echo $this->escape($this->row->section); ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="sec[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
