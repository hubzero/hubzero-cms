<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>
<form action="">
	<fieldset>
		<div class="configuration">
			<?php echo Lang::txt('COM_POLL_PREVIEW'); ?>
		</div>
	</fieldset>

	<br /><br />

	<table>
		<caption><?php echo $this->escape($this->poll->get('title')); ?></caption>
		<tfoot>
			<tr>
				<td colspan="2">
					<input type="button" name="submit" value="<?php echo Lang::txt('COM_POLL_VOTE'); ?>">&nbsp;&nbsp;
					<input type="button" name="result" value="<?php echo Lang::txt('COM_POLL_RESULTS'); ?>">
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->options as $option)
			{
				if ($option->get('text') != '')
				{
					?>
					<tr>
						<td><input type="radio" name="poll" value="<?php echo $this->escape($option->get('text')); ?>"></td>
						<td class="poll"><?php echo $this->escape($option->get('text')); ?></td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</form>