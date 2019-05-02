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

$orauthor = $this->or->creator()->get('name', Lang::txt('COM_WIKI_UNKNOWN'));
$drauthor = $this->dr->creator()->get('name', Lang::txt('COM_WIKI_UNKNOWN'));
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

		<div class="grid">
			<div class="col span-half">
				<dl class="diff-versions">
					<dt><?php echo Lang::txt('COM_WIKI_VERSION') . ' ' . $this->or->get('version'); ?><dt>
					<dd><?php echo Lang::txt('COM_WIKI_HISTORY_CREATED_BY', '<time datetime="' . $this->or->get('created') . '">' . $this->or->get('created') . '</time>', $this->escape($orauthor)); ?><dd>

					<dt><?php echo Lang::txt('COM_WIKI_VERSION') . ' ' . $this->dr->get('version'); ?><dt>
					<dd><?php echo Lang::txt('COM_WIKI_HISTORY_CREATED_BY', '<time datetime="' . $this->dr->get('created') . '">' . $this->dr->get('created') . '</time>', $this->escape($drauthor)); ?><dd>
				</dl>
			</div><!-- / .aside -->
			<div class="col span-half omega">
				<p class="diff-deletedline"><?php echo Lang::txt('COM_WIKI_HISTORY_DELETIONS'); ?></p>
				<p class="diff-addedline"><?php echo Lang::txt('COM_WIKI_HISTORY_ADDITIONS'); ?></p>
			</div><!-- / .subject -->
		</div><!-- / .section -->

		<?php echo $this->content; ?>
	</div>
</section><!-- / .main section -->
