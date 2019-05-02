<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$text = 'Upload a CSV file';

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_STOREFRONT').': ' . $text, 'storefront');
}

Html::behavior('framework');
?>

<script type="text/javascript">
	function closeAndRefresh(pressbutton)
	{
		window.parent.location='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&sId=<?php echo $this->sId; ?>';
	}

	jQuery(document).ready(function($){
		$(window).on('keypress', function(){
			if (window.event.keyCode == 13) {
				submitbutton('uploadcsv');
			}
		})
	});
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form">
	<?php if ($tmpl == 'component') { ?>
		<fieldset>
			<div class="configuration" >
				<div class="fltrt configuration-options">
					<button type="button" onclick="closeAndRefresh();"><?php echo Lang::txt('Close');?></button>
				</div>
				<?php echo Lang::txt('Upload a file with serial numbers') ?>
			</div>
		</fieldset>
	<?php } ?>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } else { ?>
		<div class="col span12">
			<div class="current">
				<p><?php echo $this->inserted; ?> serial number<?php echo $this->inserted == 1 ? '' : 's'; ?> inserted.</p>

				<?php if (!empty($this->skipped)) { ?>
					<p><?php echo count($this->skipped); ?> duplicate serial number<?php echo count($this->skipped) == 1 ? '' : 's'; ?> skipped.</p>
				<?php } ?>

				<?php if (!empty($this->ignored)) { ?>
					<p><?php echo count($this->ignored); ?> serial number<?php echo count($this->ignored) == 1 ? '' : 's'; ?> <?php echo count($this->ignored) > 1 ? 'were' : 'was'; ?> ignored:</p>
					<ul>
						<?php
						foreach ($this->ignored as $ignore)
						{
							echo '<li>' . $ignore . '</li>';
						}
						?>
					</ul>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</form>