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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt( 'COM_NEWSLETTER_NEWSLETTER_MAILINGS' ), 'mailing.png');
Toolbar::spacer();
Toolbar::custom('tracking', 'stats', '', 'COM_NEWSLETTER_TOOLBAR_STATS');
Toolbar::custom('stop', 'trash', '', 'COM_NEWSLETTER_TOOLBAR_STOP');
Toolbar::spacer();
Toolbar::preferences($this->option, '550');
?>

<script type="text/javascript">
Joomla.submitbutton = function(pressbutton)
{
	if (pressbutton == 'stop')
	{
		var message = '<?php echo Lang::txt('COM_NEWSLETTER_MAILING_STOP_CHECK'); ?>'
		if (!confirm( message ))
		{
			return;
		}
	}
	submitform( pressbutton );
}
</script>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->mailings); ?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_NEWSLETTER_MAILING_DATE'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_MAILING_PERCENT_COMPLETE'); ?></th>
				<th scope="col"	class="priority-4"><?php echo Lang::txt('COM_NEWSLETTER_MAILING_REOCCUR'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->mailings) > 0) : ?>
				<?php foreach ($this->mailings as $k => $mailing) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k; ?>" value="<?php echo $mailing->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<?php echo $mailing->newsletter->get('name', Lang::txt('COM_NEWSLETTER_UNKNOWN')); ?>
						</td>
						<td class="priority-3">
							<?php echo Date::of($mailing->date)->toLocal("F d, Y @ g:ia"); ?>
						</td>
						<td class="priority-2">
							<?php
								if ($mailing->emails_total != 0)
								{
									echo number_format(($mailing->emails_sent/$mailing->emails_total) * 100, 2) . ' %';
								}
								else
								{
									echo '0%';
								}
							 ?>
							(<?php echo Lang::txt('COM_NEWSLETTER_NUM_OF_EMAILS_SENT', number_format($mailing->emails_sent), number_format($mailing->emails_total)); ?>)
						</td>
						<td class="priority-4">
							<?php
								switch ($mailing->newsletter->get('autogen'))
								{
									case 0:
										echo Lang::txt("N/A");
									break;
									case 1:
										echo Lang::txt("Daily");
									break;
									case 2:
										echo Lang::txt("Weekly");
									break;
									case 3:
										echo Lang::txt("Monthy");
									break;
								}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4">
						<?php echo Lang::txt('COM_NEWSLETTER_NO_MAILINGS'); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
