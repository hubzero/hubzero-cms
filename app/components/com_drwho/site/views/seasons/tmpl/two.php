<?php
$no_html = Request::getInt('no_html');
?>
<?php if (!$no_html) { ?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_DRWHO'); ?>: <?php echo Lang::txt('COM_DRWHO_SEASONS'); ?></h2>
</header>
<section class="main section">
<?php } ?>
	<p>Page Twwwoooooooo</p>
<?php if (!$no_html) { ?>
</section>
<?php } ?>