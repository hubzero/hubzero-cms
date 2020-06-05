<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="<?php echo $this->container; ?>">
	<div class="panes">
		<div class="panes-content">
			<?php
			if ($this->content)
			{
				$i = 1;
				$panes = $this->content;
				foreach ($panes as $pane)
				{
					?>
					<div class="pane" id="<?php echo $this->container . '-pane' . $i; ?>">
						<div class="pane-wrap" id="<?php echo $pane->alias; ?>">
							<?php echo stripslashes($pane->introtext); ?>
						</div><!-- / .pane-wrap #<?php echo $pane->alias; ?> -->
					</div><!-- / .pane #<?php echo $this->container . '-pane' . $i; ?> -->
					<?php
					$i++;
				}
			}
			?>
		</div><!-- / .panes-content -->
	</div><!-- / .panes -->
</div><!-- / #pane-sliders -->
