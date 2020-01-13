<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$current = $this->current;
$steps = $this->steps;
?>

<ul class="ul-nav">
	<?php foreach ($steps as $text => $url): ?>

		<li <?php if ($current == $text) echo 'class="current"'; ?>>
			<a href="<?php echo $url; ?>">
				<?php echo $text; ?>
			</a>
		</li>

	<?php endforeach; ?>
</ul>
