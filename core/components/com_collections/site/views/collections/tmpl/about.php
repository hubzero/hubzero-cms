<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('help.css');

$base = 'index.php?option=' . $this->option;
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_COLLECTIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
				<span><?php echo Lang::txt('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
			</a>
		</p>
	</div>
</header>

<form method="get" action="<?php echo Route::url($base . '&controller=' . $this->controller . '&task=posts'); ?>" id="collections">
	<?php
	$this->view('_submenu')
	     ->set('option', $this->option)
	     ->set('active', 'about')
	     ->set('collections', $this->collections)
	     ->set('posts', $this->total)
	     ->display();
	?>

	<section class="main section about">

		<p class="tagline"><?php echo Lang::txt('COM_COLLECTIONS_TAGLINE'); ?></p>

		<div class="about-odd posts">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_POST'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_POST_EXPLANATION'); ?>
			</p>
		</div>

		<div class="about-even collections">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_COLLECTION'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_COLLECTION_EXPLANATION'); ?>
			</p>
		</div>

		<div class="about-odd following">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_FOLLOW_EXPLANATION', Route::url('index.php?option=com_members&task=myaccount/collections'), Route::url('index.php?option=com_members&task=myaccount'), Route::url('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

		<div class="about-even unfollowing">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW_EXPLANATION', Route::url('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

		<div class="about-odd livefeed">
			<h3><?php echo Lang::txt('COM_COLLECTIONS_LIVE_FEED'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_COLLECTIONS_LIVE_FEED_EXPLANATION', Route::url('index.php?option=com_members&task=myaccount/collections')); ?>
			</p>
		</div>

	</section>
</form>