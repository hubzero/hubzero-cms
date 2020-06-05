<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->sub)
{
	$this->css();
}
$this->js();
?>
<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<h2><?php echo $this->page->title; ?></h2>
</header><!-- /#content-header -->

<?php if (!$this->sub) { ?>
<section class="main section">
	<div class="aside">
		<?php
		$this->view('wikimenu', 'pages')
			->set('option', $this->option)
			->set('controller', $this->controller)
			->set('page', $this->page)
			->set('task', $this->task)
			->set('sub', $this->sub)
			->display();
		?>
	</div>
	<div class="subject">
<?php } ?>

		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<?php
		$this->view('submenu', 'pages')
			//->setBasePath($this->base_path)
			->set('option', $this->option)
			->set('controller', $this->controller)
			->set('page', $this->page)
			->set('task', $this->task)
			->set('sub', $this->sub)
			->display();
		?>

<?php if ($this->sub) { ?>
<section class="main section">
	<div class="section-inner">
<?php } ?>

		<article>
			<?php
			$this->view(strtolower($this->layout), 'special')
				//->setBasePath($this->base_path)
				->set('option', $this->option)
				->set('controller', $this->controller)
				->set('page', $this->page)
				->set('task', $this->task)
				->set('sub', $this->sub)
				->set('book', $this->book)
				->display()
			?>
		</article>
	</div>
</section><!-- / .main section -->
