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

// No direct access
defined('_HZEXEC_') or die();

//add styles and scripts
$this->css();
$this->js();
?>

<?php if ($this->authorized == 'manager') : ?>
	<ul id="page_options">
		<li>
			<a class="icon-add btn add" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=announcements&action=new'); ?>">
				<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_NEW'); ?>
			</a>
		</li>
	</ul>
<?php endif; ?>

<section class="main section">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=announcements'); ?>" method="get">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_LEGEND'); ?></legend>
				<label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_LABEL'); ?></label>
				<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SEARCH_PLACEHOLDER'); ?>" />
			</fieldset>
		</div><!-- / .container -->

		<div class="acontainer">
			<?php if ($this->rows->count() > 0) : ?>
				<?php foreach ($this->rows as $row) : ?>
					<?php
						$this->view('item')
						     ->set('option', $this->option)
						     ->set('group', $this->group)
						     ->set('authorized', $this->authorized)
						     ->set('announcement', $row)
						     ->set('showClose', false)
						     ->display();
					?>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="warning">
					<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_NO_RESULTS'); ?>
				</p>
			<?php endif; ?>

			<?php
			$pageNav = $this->rows->pagination;
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'announcements');
			echo $pageNav;
			?>
			<div class="clearfix"></div>
		</div><!-- / .acontainer -->
	</form>
</section>