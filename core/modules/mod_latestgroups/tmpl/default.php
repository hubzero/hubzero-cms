<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Display groups
if (!empty($this->groups)) { ?>
	<?php
	foreach ($this->groups as $g)
	{
		?>
		<div class="latestGroup">
			<h4>
				<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $g->cn); ?>">
					<?php echo $this->escape(stripslashes($g->description)); ?>
				</a>
			</h4>
			<p class="groupDescription">
				<?php echo $this->escape(stripslashes($g->public_desc)); ?>
			</p>
		</div>
		<?php
	}
	?>
<?php } else { ?>
	<p><?php echo Lang::txt('MOD_LATESTGROUPS_NO_RESULTS'); ?></p>
<?php } ?>

	<p class="more">
		<a href="<?php echo Route::url('index.php?option=com_groups'); ?>">
			<?php echo Lang::txt('MOD_LATESTGROUPS_ALL'); ?>
		</a>
	</p>