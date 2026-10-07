<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Where to turn when the knowledge base does not answer the question. A
// template that routes help elsewhere overrides this file alone.
?>
<?php if (Component::isEnabled('com_answers')) { ?>
	<div class="container">
		<h3><?php echo Lang::txt('COM_KB_COMMUNITY'); ?></h3>
		<p>
			<?php echo Lang::txt('COM_KB_COMMUNITY_CANT_FIND'); ?> <?php echo Lang::txt('COM_KB_COMMUNITY_TRY_ANSWERS', '<a href="' . Route::url('index.php?option=com_answers') . '">' . Lang::txt('COM_ANSWERS') . '</a>'); ?>
		</p>
	</div><!-- / .container -->
<?php } ?>
<?php if (Component::isEnabled('com_wishlist')) { ?>
	<div class="container">
		<h3><?php echo Lang::txt('COM_KB_FEATURE_REQUEST'); ?></h3>
		<p>
			<?php echo Lang::txt('COM_KB_HAVE_A_FEATURE_REQUEST'); ?> <a href="<?php echo Route::url('index.php?option=com_wishlist'); ?>"><?php echo Lang::txt('COM_KB_FEATURE_TELL_US'); ?></a>
		</p>
	</div><!-- / .container -->
<?php } ?>
<?php if (Component::isEnabled('com_support')) { ?>
	<div class="container">
		<h3><?php echo Lang::txt('COM_KB_TROUBLE_REPORT'); ?></h3>
		<p>
			<?php echo Lang::txt('COM_KB_TROUBLE_FOUND_BUG'); ?> <a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=new'); ?>"><?php echo Lang::txt('COM_KB_TROUBLE_TELL_US'); ?></a>
		</p>
	</div><!-- / .container -->
<?php } ?>
