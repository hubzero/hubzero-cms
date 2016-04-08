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
	<div class="section-inner">
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
