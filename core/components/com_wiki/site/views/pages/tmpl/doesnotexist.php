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
$this->js();

$templates = $this->book->templates()
	->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
	->rows();
?>

<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<h2><?php echo $this->escape($this->page->title); ?></h2>
</header><!-- /#content-header -->

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
	<p class="warning">
		<?php echo Lang::txt('COM_WIKI_WARNING_PAGE_DOES_NOT_EXIST_CREATE_IT', Route::url($this->page->link('new'))); ?>
	</p>
	<?php if ($templates->count()) { ?>
		<p>
			<?php echo Lang::txt('COM_WIKI_CHOOSE_TEMPLATE'); ?>
		</p>
		<ul>
			<?php foreach ($templates as $template) { ?>
				<li>
					<a href="<?php echo Route::url($this->page->link('new') . '&tplate=' . stripslashes($template->get('pagename'))); ?>">
						<?php echo $this->escape(stripslashes($template->title)); ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	<?php } ?>
</section><!-- / .main section -->
