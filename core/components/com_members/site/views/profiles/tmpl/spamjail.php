<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$params = Plugin::params('system', 'spamjail');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_SPAM_DETECTED'); ?></h2>
</header>

<section class="section">
	<p><?php echo Lang::txt('COM_MEMBERS_SPAM_MESSAGE'); ?></p>

	<?php if ($video = $params->get('spam_video', false)) : ?>
		<p><?php echo Lang::txt('COM_MEMBERS_SPAM_VIDEO'); ?></p>
		<div class="video align-center">
			<iframe width="420" height="315" src="https://www.youtube.com/embed/<?php echo $video; ?>" frameborder="0" allowfullscreen></iframe>
		</div>
	<?php endif; ?>
</section>