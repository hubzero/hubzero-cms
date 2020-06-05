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

<nav>
	<ul class="sub-menu">
		<?php
		if ($this->cats) {
			$i = 1;
			$cs = array();
			foreach ($this->cats as $cat)
			{
				$name = key($cat);
				if ($cat[$name] != '') {
		?>
				<li id="sm-<?php echo $i; ?>"<?php if (strtolower($name) == $this->task) { echo ' class="active"'; } ?>><a class="tab" rel="<?php echo $name; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task='.$name); ?>"><span><?php echo $cat[$name]; ?></span></a></li>
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
		$k++;
	}
}
