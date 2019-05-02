<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_CHECK'), 'support.png');
Toolbar::custom('check', 'purge', '', 'COM_SUPPORT_CHECK', false);

Html::behavior('framework');

$this->view('_submenu')->display();

$this->css('
.spam {
	color:red;
}
.ham {
	color:green;
}
');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check'); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-sample"><?php echo Lang::txt('COM_SUPPORT_ABUSE_SAMPLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<textarea name="sample" id="field-sample" cols="35" rows="20"><?php echo $this->escape($this->sample); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<?php if ($this->results) { ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_SUPPORT_ABUSE_SPAM_REPORT'); ?></span></legend>
					<table>
						<tbody>
							<?php
							foreach ($this->results as $result)
							{
								if (strstr($result['service'], '\\'))
								{
									$parts = explode('\\', $result['service']);
									$result['service'] = (isset($parts[2]) ? $parts[2] : $result['service']);
								}
								?>
								<tr>
									<th><?php echo $result['service']; ?></th>
									<td><?php echo $result['is_spam'] ? '<span class="spam">spam</span>' : '<span class="ham">ham</span>'; ?></td>
									<td><?php echo $result['message'] ? '<span class="detector-message">' . $result['message'] . '</span>' : ''; ?></td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</fieldset>
			<?php } else { ?>
				<p class="info"><?php echo Lang::txt('COM_SUPPORT_ABUSE_CHECK_ABOUT'); ?></p>
			<?php } ?>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="check" />

	<?php echo Html::input('token'); ?>
</form>
