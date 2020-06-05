<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDMEMBER_MISSING_CLASS'); ?></p>
<?php } else {
	if ($this->row) { ?>
		<div class="<?php echo $this->cls; ?>">
			<?php if ($this->filters['show'] == 'contributors') { ?>
				<h3><?php echo Lang::txt('MOD_FEATUREDMEMBER_PROFILE'); ?></h3>
			<?php } else { ?>
				<h3><?php echo Lang::txt('MOD_FEATUREDMEMBER'); ?></h3>
			<?php } ?>
			<?php if (is_file(PATH_APP . $this->row->picture())) { ?>
				<p class="featured-img">
					<a href="<?php echo Route::url($this->row->link()); ?>">
						<img width="50" height="50" src="<?php echo $this->row->picture(); ?>" alt="<?php echo $this->escape(stripslashes($this->row->get('name', $this->row->get('givenName') . ' ' . $this->row->get('surname')))); ?>" />
					</a>
				</p>
			<?php } ?>
			<p>
				<a href="<?php echo Route::url($this->row->link()); ?>">
					<?php echo $this->escape(stripslashes($this->row->get('name', $this->row->get('givenName') . ' ' . $this->row->get('surname')))); ?>
				</a>:
				<?php if ($txt = $this->row->get('bio')) { ?>
					<?php echo \Hubzero\Utility\Str::truncate($this->escape(strip_tags($txt)), $this->txt_length); ?>
				<?php } ?>
			</p>
		</div>
	<?php
	}
}
