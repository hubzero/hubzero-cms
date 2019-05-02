<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework');
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form">
	<fieldset>
		<div class="configuration" >
			<?php echo Lang::txt('Add new users') ?>
		</div>
	</fieldset>

	<div class="col span12">
		<fieldset class="adminform">
			<div class="current">
			<?php

			echo '<p><strong>' . $this->matched . '</strong> user(s) added.</p>';
			if (count($this->noUserMatch))
			{
				echo '<p><strong>' . count($this->noUserMatch) . '</strong> user(s) could not be added:<br>';
			}

			$i = 0;
			foreach ($this->noUserMatch as $usr)
			{
				if ($i)
				{
					echo ', ';
				}
				echo $usr;
				$i = 1;
			}

			echo '</p>';

			?>
			</div>

		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>
