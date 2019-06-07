<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
Toolbar::title(Lang::txt( 'COM_NEWSLETTER_NEWSLETTER_TOOLS' ), 'tools');

// add jquery
Html::behavior('framework');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY'); ?></span></legend>

				<table class="admintable">
					<tbody>
						<tr>
							<td colspan="2">
								<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_DESC'); ?></span>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_IMAGE_FILE'); ?></th>
							<td>
								<input type="file" name="image-file" />
							</td>
						</tr>
						<tr>
							<td colspan="2">&mdash;&mdash;&mdash; or &mdash;&mdash;&mdash;</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_IMAGE_URL'); ?></th>
							<td>
								<input type="text" name="image-url" />
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_MOSAIC_SIZE'); ?></th>
							<td>
								<select name="mosaic-size">
									<option value="1">1</option>
									<option value="3">3</option>
									<option selected="selected" value="5">5</option>
									<option value="10">10</option>
									<option value="15">15</option>
									<option value="20">20</option>
									<option value="25">25</option>
									<option value="30">30</option>
									<option value="35">35</option>
									<option value="40">40</option>
									<option value="45">45</option>
									<option value="50">50</option>
								</select>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type="submit" value="<?php echo Lang::txt('Submit'); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
			<?php if ($this->code != '') : ?>
				<h3><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_ORIGINAL'); ?></h3>
				<img src="<?php echo str_replace(PATH_APP, '', $this->original); ?>" alt="" />

				<h3><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_MOZIFIED'); ?></h3>
				<iframe id="preview-iframe"></iframe>
				<div id="preview-code" class="hide"><?php echo $this->preview; ?></div>

				<h3><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_CODE'); ?></h3>
				<textarea id="code"><?php echo str_replace("\n", "", $this->code); ?></textarea>
			<?php endif; ?>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="mozify" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>