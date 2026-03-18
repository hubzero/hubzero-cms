<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->no_html) {
	$this->css()
	     ->js();
	?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<nav aria-label="<?php echo Lang::txt('COM_USAGE_CATEGORIES'); ?>">
	<ul class="sub-menu">
		<?php
		if ($this->cats) {
			$i = 1;
			$cs = array();
			foreach ($this->cats as $cat)
			{
				$name = key($cat);
				if ($cat[$name] != '') {
					$isActive = (strtolower($name) == $this->task);
		?>
				<li id="sm-<?php echo $i; ?>"<?php if ($isActive) { echo ' class="active"'; } ?>><a class="tab" href="<?php echo Route::url('index.php?option='.$this->option.'&task='.$name); ?>"<?php if ($isActive) { echo ' aria-current="page"'; } ?>><span><?php echo $cat[$name]; ?></span></a></li>
		<?php
					$i++;
					$cs[] = $name;
				}
			}
		}
		?>
	</ul>
</nav>

<?php } ?>

<?php
$h = 'hide';
$c = 'main';
if ($this->sections) {
	$k = 0;
	foreach ($this->sections as $section)
	{
		if ($section != '')
		{
			if ($this->no_html)
			{
				echo $section;
			}
			else
			{
				$cls  = ($c) ? $c.' ' : '';
				if (key($this->cats[$k]) != $this->task)
				{
					$cls .= ($h) ? $h.' ' : '';
				}
				?>
				<section class="<?php echo $cls; ?>section" id="statistics">
					<?php echo $section; ?>
				</section><!-- / #statistics.<?php echo $cls; ?>section -->
				<?php
			}
		}
		$k++;
	}
}
