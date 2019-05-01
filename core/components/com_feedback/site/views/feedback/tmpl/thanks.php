<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="main-page btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
				<?php echo Lang::txt('COM_FEEDBACK_MAIN'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<p class="passed"><?php echo Lang::txt('COM_FEEDBACK_STORY_THANKS'); ?></p>

	<div class="quote">
		<?php if (count($this->addedPictures)) { ?>
			<?php foreach ($this->addedPictures as $img) { ?>
				<img src="<?php echo $this->path . '/' . $img; ?>" alt="" />
			<?php } ?>
		<?php } ?>

		<blockquote cite="<?php echo $this->escape($this->row->get('fullname')); ?>">
			<?php echo $this->escape(stripslashes($this->row->get('quote'))); ?>
		</blockquote>

		<p class="cite">
			<?php
			if ($this->row->get('user_id'))
			{
				echo '<img src="' . $this->row->user->picture() . '" alt="' . $this->escape($this->row->get('fullname')) . '" width="30" height="30" />';
			}
			?>
			<cite><?php echo $this->escape($this->row->get('fullname')); ?></cite><br />
			<?php echo $this->escape($this->row->get('org')); ?>
		</p>
	</div>
</section><!-- / .main section -->
