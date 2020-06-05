<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css('factors.css');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_USERS_FACTOR_VERIFICATION'); ?></h2>
</header>

<section class="main section factors">
	<?php foreach ($this->factors as $factor) : ?>
		<div class="factor-wrap">
			<div class="factor">
				<?php echo $factor->html; ?>
			</div>
		</div>
	<?php endforeach; ?>
</section>