<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$text = 'Upload a CSV file';

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_STOREFRONT').': ' . $text, 'storefront');
	Toolbar::save();
	Toolbar::cancel();
}

Html::behavior('framework');
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$(window).on('keypress', function(){
			if (window.event.keyCode == 13) {
				submitbutton('uploadcsv');
			}
		})
	});
</script>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form" enctype="multipart/form-data">
	<?php if ($tmpl == 'component') { ?>
		<fieldset>
			<div class="configuration" >
				<div class="fltrt configuration-options">
					<button type="button" onclick="submitbutton('uploadcsv');"><?php echo Lang::txt('Import');?></button>
					<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt('Cancel');?></button>
				</div>
				<?php echo Lang::txt('Uplaod a file with users') ?>
			</div>
		</fieldset>
	<?php } ?>
	<div class="col span12">
		<fieldset class="adminform">
			<input type="hidden" name="sId" value="<?php echo $this->sId; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="uploadcsv" />

			<table class="admintable">
				<tbody>
				<tr>
					<td><label for="csvFile">CSV file:</label></td>
					<td><input type="file" name="csvFile" id="csvFile" /></td>
				</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>