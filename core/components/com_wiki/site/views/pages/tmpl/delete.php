<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

<section class="main section">
	<?php if ($this->page->isLocked() && !$this->page->access('manage')) { ?>
		<p class="warning"><?php echo Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
	<?php } else { ?>
		<form action="<?php echo Route::url($this->page->link('base')); ?>" method="post" id="hubForm">
			<div class="explaination">
				<p><?php echo Lang::txt('COM_WIKI_DELETE_PAGE_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?></legend>

				<label for="confirm-delete">
					<input class="option" type="checkbox" name="confirm" id="confirm-delete" value="1" />
					<?php echo Lang::txt('COM_WIKI_FIELD_CONFIRM_DELETE'); ?>
				</label>

				<p class="warning">
					<?php echo Lang::txt('COM_WIKI_FIELD_CONFIRM_DELETE_HINT'); ?>
				</p>

				<input type="hidden" name="pagename" value="<?php echo $this->escape(($this->page->get('path') ? $this->page->get('path') . '/' : '') . $this->page->get('pagename')); ?>" />
				<input type="hidden" name="page_id" value="<?php echo $this->escape($this->page->get('id')); ?>" />

				<?php foreach ($this->page->adapter()->routing('delete') as $name => $val) { ?>
					<input type="hidden" name="<?php echo $this->escape($name); ?>" value="<?php echo $this->escape($val); ?>" />
				<?php } ?>

				<?php echo Html::input('token'); ?>
			</fieldset><div class="clear"></div>

			<p class="submit">
				<input type="submit" class="btn btn-danger" value="<?php echo Lang::txt('COM_WIKI_DELETE'); ?>" />
			</p>
		</form>
	<?php } ?>
</section><!-- / .main section -->
