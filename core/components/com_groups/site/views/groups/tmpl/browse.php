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

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('browse');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_GROUPS'); ?>: <?php echo $this->title; ?></h2>

	<?php if (User::authorise('core.create', $this->option)) : ?>
		<div id="content-header-extra">
			<p>
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=new'); ?>">
					<?php echo Lang::txt('COM_GROUPS_NEW'); ?>
				</a>
			</p>
		</div><!-- / #content-header-extra -->
	<?php endif; ?>
</header>

<?php
	foreach ($this->notifications as $notification)
	{
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<form action="<?php echo Route::url('index.php?option='.$this->option.'&task=browse'); ?>" method="get">
	<section class="main section">
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_HELP'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="policy" value="<?php echo $this->escape($this->filters['policy']); ?>" />
					<input type="hidden" name="index" value="<?php echo $this->escape($this->filters['index']); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<nav class="entries-filters">
					<?php
						$fltrs  = ($this->filters['index'])  ? '&index=' . $this->escape($this->filters['index'])   : '';
						$fltrs .= ($this->filters['policy']) ? '&policy=' . $this->escape($this->filters['policy']) : '';
						$fltrs .= ($this->filters['search']) ? '&search=' . $this->escape($this->filters['search']) : '';
					?>
					<ul class="entries-menu order-options" data-label="<?php echo Lang::txt('COM_GROUPS_BROWSE_SORT'); ?>">
						<li><a class="sort-title<?php echo ($this->filters['sortby'] == 'title') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&sortby=title' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SORT_BY_TITLE'); ?>"><?php echo Lang::txt('COM_GROUPS_GROUP_TITLE'); ?></a></li>
						<li><a class="sort-alias<?php echo ($this->filters['sortby'] == 'alias') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&sortby=alias' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SORT_BY_ALIAS'); ?>"><?php echo Lang::txt('COM_GROUPS_GROUP_ALIAS'); ?></a></li>
					</ul>
					<?php
					$fltrs  = ($this->filters['index'])  ? '&index=' . $this->escape($this->filters['index'])   : '';
					$fltrs .= ($this->filters['sortby']) ? '&sortby=' . $this->escape($this->filters['sortby']) : '';
					$fltrs .= ($this->filters['search']) ? '&search=' . $this->escape($this->filters['search']) : '';
					?>
					<ul class="entries-menu filter-options" data-label="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW'); ?>">
						<?php /*<li><a class="filter-all<?php echo ($this->filters['policy'] == '') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_ALL'); ?>"><?php echo Lang::txt('JALL'); ?></a></li>
						<li><a class="filter-open<?php echo ($this->filters['policy'] == 'open') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=open' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN'); ?></a></li>
						<li><a class="filter-restricted<?php echo ($this->filters['policy'] == 'restricted') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=restricted' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED'); ?></a></li>
						<li><a class="filter-invite<?php echo ($this->filters['policy'] == 'invite') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=invite' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY'); ?></a></li>
						<li><a class="filter-closed<?php echo ($this->filters['policy'] == 'closed') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=closed' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED'); ?></a></li>
						<li>
							<select name="index">
								<option value="">- All -</option>
								<?php
								$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
								foreach ($letters as $letter)
								{
									echo '<option value="' . strtolower($letter) . '"';
									if ($this->filters['index'] == strtolower($letter))
									{
										echo ' selected="selected"';
									}
									echo '>' . $letter . '</option>';
								}
								?>
							</select>
						</li>*/?>
						<li>
							<select name="published">
								<option value="1" <?php echo ($this->filters['published'] == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_BROWSE_STATE_ACTIVE'); ?></option>
								<option value="2" <?php echo ($this->filters['published'] == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED'); ?></option>
							</select>
						</li>
						<li>
							<select name="policy">
								<option value=""><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_ALL'); ?></option>
								<option value="open" <?php echo ($this->filters['policy'] == 'open') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN'); ?></option>
								<option value="restricted" <?php echo ($this->filters['policy'] == 'restricted') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED'); ?></option>
								<option value="invite" <?php echo ($this->filters['policy'] == 'invite') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY'); ?></option>
								<option value="closed" <?php echo ($this->filters['policy'] == 'closed') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED'); ?></option>
							</select>
						</li>
					</ul>
				</nav>

				<div class="groups-container">
					<?php
					if ($this->groups)
					{
						foreach ($this->groups as $group)
						{
							$this->view('_group')
								->set('group', $group)
								->display();
						} // for loop
						?>
					<?php } else { ?>
						<div class="results-none">
							<p><?php echo Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS'); ?></p>
						</div>
					<?php } ?>
				</div><!-- / .groups -->
				<?php if ($this->groups) : ?>
					<?php
					// Initiate paging
					$pageNav = $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					$pageNav->setAdditionalUrlParam('index', $this->filters['index']);
					$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
					$pageNav->setAdditionalUrlParam('policy', $this->filters['policy']);
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);

					echo $pageNav->render();
					?>
				<?php endif; ?>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_TITLE'); ?></h3>
				<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_ONE'); ?></p>
				<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_TWO'); ?></p>
				<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_THREE'); ?></p>
			</div><!-- / .container -->

			<?php if (Component::isEnabled('com_members')) : ?>
				<div class="container">
					<h3><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_TITLE'); ?></h3>
					<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_DEATAILS', Route::url('index.php?option=com_members')); ?></p>
				</div><!-- / .container -->
			<?php endif; ?>
		</aside><!-- / .aside -->
	</section><!-- / .main section -->
</form>
