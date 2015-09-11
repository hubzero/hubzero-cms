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

if ($this->subscriptionCode && $this->employer)
{
	$this->title .= ' ' . Lang::txt('FROM') . ' ' . $this->employer->companyName;
}

?>
<header id="content-header">
	<h2><?php echo $this->mini ? Lang::txt('COM_JOBS_LATEST_POSTINGS') : $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if (User::isGuest()) { ?>
			<li><?php echo Lang::txt('COM_JOBS_PLEASE') . ' <a class="btn" href="' . Route::url('index.php?option=' . $this->option . '&task=view') . '?action=login">' . Lang::txt('COM_JOBS_ACTION_LOGIN') . '</a> ' . Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
		<?php } else if ($this->emp && $this->config->get('allowsubscriptions', 0)) {  ?>
			<li><a class="myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
			<li><a class="shortlist btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes') . '?filterby=shortlisted'; ?>"><?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?></a></li>
		<?php } else if ($this->admin) { ?>
			<li>
				<?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
				<a class="icon-dashboard btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?></a>
			</li>
		<?php } else { ?>
			<li><a class="myresume btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addresume'); ?>"><?php echo Lang::txt('COM_JOBS_MY_RESUME'); ?></a></li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
			<?php
			$sortbys = array(
				'category' => Lang::txt('COM_JOBS_CATEGORY'),
				'opendate' => Lang::txt('COM_JOBS_POSTED_DATE'),
				'type'     => Lang::txt('COM_JOBS_TYPE')
			);
			$filterbys = array(
				'all'   => Lang::txt('COM_JOBS_ALL'),
				'open'  => Lang::txt('COM_JOBS_ACTIVE'),
				'closed'=> Lang::txt('COM_JOBS_EXPIRED')
			);
			?>
	<?php if (count($this->jobs) > 0) { ?>
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
				<fieldset class="entry-search">
					<legend></legend>
					<label for="entry-search-field"><?php echo Lang::txt('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="limitstart" value="0" />
					<input type="hidden" name="performsearch" value="1" />
				</fieldset>
				<div class="clearfix"></div>
				<div class="container-block">
		<?php // Display List of items

			$view = new \Hubzero\Component\View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_jobs' . DS . 'site',
				'name'      => 'jobs',
				'layout'    => '_list'
				)
			);
			$view->set('option', $this->option)
		     ->set('filters', $this->filters)
		     ->set('config', $this->config)
		     ->set('task', $this->task)
		     ->set('emp', $this->emp)
		     ->set('mini', $this->mini)
			 ->set('jobs', $this->jobs)
		     ->set('admin', $this->admin)
		     ->display();
		?>
				</div>
			</div>
		<?php } else { ?>
		<p>
		<?php
		echo Lang::txt('COM_JOBS_NO_JOBS_FOUND');
		if ($this->subscriptionCode)
		{
			if ($this->employer)
			{
				echo ' ' . Lang::txt('COM_JOBS_FROM') . ' ' . Lang::txt('COM_JOBS_EMPLOYER') . ' ' . $this->employer->companyName . ' (' . $this->subscriptionCode . ')';
			}
			else
			{
				echo ' ' . Lang::txt('COM_JOBS_FROM') . ' ' . Lang::txt('COM_JOBS_REQUESTED_EMPLOYER') . ' (' . $this->subscriptionCode . ')';
			}
			echo '. <a href="' . Route::url('index.php?option=' . $this->option . '&task=browse') . '"">' . Lang::txt('COM_JOBS_ACTION_BROWSE_ALL_JOBS') . '</a>';
		}
		?>
		</p>
		<?php } ?>
		<?php
		$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
		echo $this->pageNav->render();
		?>
	</form>
</section>
