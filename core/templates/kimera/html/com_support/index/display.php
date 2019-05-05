<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('index.css');
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="section">
	<div class="tagline">
		<p>We offer several ways of finding content and encourage exploring our knowledge base and engaging the community for support.</p>
	</div>

	<?php if (Component::isEnabled('com_kb')) { ?>
	<div class="about odd kb">
		<h3>Knowledge Base</h3>
		<p><a href="<?php echo Route::url('index.php?option=com_kb'); ?>">Find</a> answers to frequently asked questions, helpful tips, and any other information we thought might be useful.</p>
	</div>
	<?php } ?>

	<div class="about even report">
		<h3>Report problems</h3>
		<p><a href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>">Report problems</a> with our form and have your problem entered into our <a href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">ticket tracking system</a>. We guarantee a response!</p>
	</div>

	<div class="about odd tickets">
		<h3>Track Tickets</h3>
		<p>Have a problem entered into our <a href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>">ticket tracking system</a>? Track its progress, add comments and notes, or close resolved issues.</p>
	</div>
</section>
