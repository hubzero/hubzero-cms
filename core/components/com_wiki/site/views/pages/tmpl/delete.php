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
?>
<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<?php if (count($this->parents)) { ?>
		<p class="wiki-crumbs">
			<?php foreach ($this->parents as $parent) { ?>
				<a class="wiki-crumb" href="<?php echo Route::url($parent->link()); ?>"><?php echo $parent->title; ?></a> /
			<?php } ?>
		</p>
	<?php } ?>

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

	<?php if ($this->page->isLocked() && !$this->page->access('manage')) { ?>
		<p class="warning"><?php echo Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
	<?php } else { ?>
		<form action="<?php echo Route::url($this->page->link('base')); ?>" method="post" id="hubForm" class="full">
			<fieldset>
				<legend><?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?></legend>

				<div class="form-group form-check">
					<label for="confirm-delete" class="form-check-label">
						<input class="option form-check-input" type="checkbox" name="confirm" id="confirm-delete" value="1" />
						<?php echo Lang::txt('COM_WIKI_FIELD_CONFIRM_DELETE'); ?>
					</label>
				</div>

				<p class="warning">
					<?php echo Lang::txt('COM_WIKI_FIELD_CONFIRM_DELETE_HINT'); ?>
				</p>

				<input type="hidden" name="pagename" value="<?php echo $this->escape(($this->page->get('path') ? $this->page->get('path') . '/' : '') . $this->page->get('pagename')); ?>" />
				<input type="hidden" name="page_id" value="<?php echo $this->escape($this->page->get('id')); ?>" />

				<?php foreach ($this->page->adapter()->routing('delete') as $name => $val) { ?>
					<input type="hidden" name="<?php echo $this->escape($name); ?>" value="<?php echo $this->escape($val); ?>" />
				<?php } ?>

				<?php echo Html::input('token'); ?>
			</fieldset>

			<p class="submit">
				<input type="submit" class="btn btn-danger" value="<?php echo Lang::txt('COM_WIKI_DELETE'); ?>" />
			</p>
		</form>
	<?php } ?>

	</div>
</section><!-- / .main section -->
