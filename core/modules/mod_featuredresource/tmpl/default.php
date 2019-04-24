<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDRESOURCE_MISSING_CLASS'); ?></p>
<?php } else {
	if ($this->row) {
?>
	<div class="<?php echo $this->cls; ?>">
	<?php if ($this->thumb && is_file(PATH_APP . $this->thumb)) { ?>
		<p class="featured-img">
			<a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->id); ?>">
				<img width="50" height="50" src="<?php echo $this->thumb; ?>" alt="" />
			</a>
		</p>
	<?php } ?>
		<p>
			<a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->id); ?>">
				<?php echo $this->escape(stripslashes($this->row->title)); ?>
			</a>:
		<?php if ($this->row->introtext) { ?>
			<?php echo \Hubzero\Utility\Str::truncate($this->escape(strip_tags($this->row->introtext)), $this->txt_length); ?>
		<?php } ?>
		</p>
	</div>
<?php } else { ?>
	<div class="<?php echo $this->cls; ?>">
		<p>
			<?php echo Lang::txt('MOD_FEATUREDRESOURCE_NO_RESULTS'); ?>
		</p>
	</div>
<?php
	}
}
