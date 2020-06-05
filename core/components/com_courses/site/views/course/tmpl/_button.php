<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
	<div class="btn-group-wrap">
		<div class="btn-group dropdown">
			<?php if ($this->course->isManager()) { ?>
				<a class="btn" href="<?php echo Route::url($this->offering->link('enter')); ?>"><?php echo $this->escape(stripslashes($this->offering->get('title'))); ?></a>
			<?php } else { ?>
				<a class="btn" href="<?php echo Route::url($this->offering->link('enter')); ?>"><?php echo $this->escape(stripslashes($this->section->get('title'))); ?></a>
			<?php } ?>
			<span class="btn dropdown-toggle"></span>
			<ul class="dropdown-menu">
			<?php
			foreach ($this->sections as $key => $section)
			{
				// Skip the first one
				if ($key == 0 && $this->course->isStudent())
				{
					continue;
				}
				// Set the section
				$this->offering->section($section);
				?>
				<li>
					<a href="<?php echo Route::url($this->offering->link()); ?>">
						<?php echo $this->escape(stripslashes($section->get('title'))); ?>
					</a>
				</li>
				<?php
			}
			?>
			</ul>
			<div class="clear"></div>
		</div><!-- /btn-group -->
	</div>