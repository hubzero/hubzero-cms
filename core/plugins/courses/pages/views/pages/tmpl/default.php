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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css()
     ->js();

$base = $this->offering->link() . '&active=pages';

$this->view('default_menu')
     ->set('option', $this->option)
     ->set('controller', $this->controller)
     ->set('course', $this->course)
     ->set('offering', $this->offering)
     ->set('page', $this->page)
     ->set('pages', $this->pages)
     ->display();
?>
<div class="pages-wrap">
<?php
if (!$this->page)
{
	?>
	<div class="pages-content">
		<div id="pages-introduction">
			<div class="instructions">
				<p><?php echo Lang::txt('PLG_COURSES_PAGES_NONE_FOUND'); ?></p>
			</div>
		</div>
	</div><!-- / .pages-content -->
	<?php
}
else
{
	Pathway::append(
		stripslashes($this->page->get('title')),
		$base . '&unit=' . $this->page->get('url')
	);

	$authorized = false;
	if ($this->page->get('offering_id'))
	{
		// If they're a course level manager
		if ($this->offering->access('manage'))
		{
			$authorized = true;
		}
		// If they're a section manager and the page is a section page
		else if ($this->offering->access('manage', 'section') && $this->page->get('section_id'))
		{
			$authorized = true;
		}
	}
	?>
	<?php if ($authorized) { ?>
		<ul class="manager-options">
			<li>
				<a class="icon-delete delete" data-confirm="<?php echo Lang::txt('PLG_COURSES_PAGES_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($base . '&unit=' . $this->page->get('url') . '&b=delete'); ?>" title="<?php echo Lang::txt('PLG_COURSES_PAGES_DELETE'); ?>">
					<?php echo Lang::txt('PLG_COURSES_PAGES_DELETE'); ?>
				</a>
			</li>
			<li>
				<a class="icon-edit edit" href="<?php echo Route::url($base . '&unit=' . $this->page->get('url') . '&b=edit'); ?>" title="<?php echo Lang::txt('PLG_COURSES_PAGES_EDIT'); ?>">
					<?php echo Lang::txt('PLG_COURSES_PAGES_EDIT'); ?>
				</a>
			</li>
		</ul>
	<?php } ?>
	<div class="pages-content">
		<?php echo $this->page->content('parsed'); ?>
	</div><!-- / .pages-content -->
	<?php
}
?>
</div><!-- / .pages-wrap -->