<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
	<h2><?php echo $this->escape($this->page->title); ?></h2>
	<?php
	if (!$this->page->isStatic())
	{
		$this->view('authors', 'pages')
			//->setBasePath($this->base_path)
			->set('page', $this->page)
			->display();
	}
	?>
</header><!-- /#content-header -->

<?php if (!$this->sub) { ?>
<section class="main section">
	<div class="aside">
		<?php
		$this->view('wikimenu')
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

		<?php
		if ($this->page->get('id'))
		{
			$this->view('submenu', 'pages')
				//->setBasePath($this->base_path)
				->set('option', $this->option)
				->set('controller', $this->controller)
				->set('page', $this->page)
				->set('task', $this->task)
				->set('sub', $this->sub)
				->display();
		}
		?>

<?php if ($this->sub) { ?>
<section class="main section">
	<div class="section-inner">
<?php } ?>

		<p class="warning"><?php echo Lang::txt('COM_WIKI_WARNING_NO_REVISION_FOUND', $this->version); ?></p>

	</div>
</section><!-- / .main section -->
