<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2)
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
