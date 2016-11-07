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

$this->css('introduction.css', 'system')
     ->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if (User::authorise('core.create', $this->option)) : ?>
		<div id="content-header-extra">
			<p>
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
					<?php echo Lang::txt('COM_GROUPS_NEW'); ?>
				</a>
			</p>
		</div><!-- / #content-header-extra -->
	<?php endif; ?>
</header>

<?php
	foreach ($this->notifications as $notification)
	{
		echo '<p class="' . $this->escape($notification['type']) . '">' . $notification['message'] . '</p>';
	}
?>

<section id="introduction" class="section">
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
		<div class="grid">
			<div class="col span8">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<label for="gsearch"><?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_HELP'); ?></label>
						<input type="text" name="search" id="gsearch" value="" placeholder="<?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- / .container -->
				<p><?php echo Lang::txt('COM_GROUPS_INTRO_WHAT_ARE_GROUPS_DESC'); ?></p>
				<p><a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>"><?php echo Lang::txt('COM_GROUPS_INTRO_NEED_HELP'); ?></a></p>
			</div>
			<div class="col span3 offset1 omega">
				<div>
					<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
						<?php echo Lang::txt('COM_GROUPS_INTRO_FIND_GROUP_BROWSE_BUTTON_TEXT'); ?>
					</a>
				</div>
			</div>
		</div>
	</form>
</section><!-- / #introduction.section -->

<section class="section">
	<?php if (!User::isGuest()) : ?>
		<?php if ($this->config->get('intro_mygroups', 1)) : ?>
			<?php if (isset($this->mygroups['invitees']) && count($this->mygroups['invitees']) > 0) : ?>
				<div class="invites">
					<div class="header">
						<h2><?php echo Lang::txt('COM_GROUPS_INTRO_GROUP_INVITES'); ?></h2>
						<p><?php echo Lang::txt('COM_GROUPS_INTRO_GROUP_INVITES_DESC'); ?></p>
					</div>
					<ul>
						<?php foreach ($this->mygroups['invitees'] as $invite) : ?>
							<li><?php echo $invite->description; ?><a href="<?php echo Route::url('index.php?option=com_groups&cn='.$invite->cn.'&task=accept'); ?>">Accept Invite</a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if (isset($this->mygroups['applicants']) && count($this->mygroups['applicants']) > 0) : ?>
				<div class="requests">
					<div class="header">
						<h2><?php echo Lang::txt('COM_GROUPS_INTRO_GROUP_REQUESTS'); ?></h2>
						<p><?php echo Lang::txt('COM_GROUPS_INTRO_GROUP_REQUESTS_DESC'); ?></p>
					</div>
					<ul>
						<?php foreach ($this->mygroups['applicants'] as $applicant) : ?>
							<li><?php echo $applicant->description; ?><a href="<?php echo Route::url('index.php?option=com_groups&cn='.$applicant->cn.'&task=cancel'); ?>">Cancel Request</a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<section class="mygroups">
				<h3><?php echo Lang::txt('COM_GROUPS_INTRO_MY_GROUPS_TITLE'); ?></h3>

				<div class="groups-container">
					<?php $mygroups_members = (isset($this->mygroups['members'])) ? $this->mygroups['members'] : array(); ?>
					<?php if (!count($mygroups_members)) : ?>
						<div class="results-none">
							<p><?php echo Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS'); ?></p>
						</div>
					<?php else : ?>
						<?php
						foreach ($mygroups_members as $group)
						{
							$this->view('_group')
								->set('group', $group)
								->display();
						}
						?>
					<?php endif; ?>
				</div>
			</section><!-- / .mygroups -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!User::isGuest()) : ?>
		<?php if ($this->config->get('intro_interestinggroups', 1)) : ?>
			<section class="interestinggroups">
				<h3><?php echo Lang::txt('COM_GROUPS_INTRO_INTERESTING_GROUPS_TITLE'); ?></h3>

				<div class="groups-container">
					<?php if (!count($this->interestinggroups)) : ?>
						<div class="results-none">
							<p><?php echo Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS'); ?></p>
						</div>
					<?php else : ?>
						<?php
						foreach ($this->interestinggroups as $group)
						{
							$this->view('_group')
								->set('group', $group)
								->display();
						}
						?>
					<?php endif; ?>
				</div>
			</section><!-- / .interestinggroups -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($this->config->get('intro_populargroups', 1)) : ?>
		<section class="populargroups">
			<h3><?php echo Lang::txt('COM_GROUPS_INTRO_POPULAR_GROUPS_TITLE'); ?></h3>

			<div class="groups-container">
				<?php if (!count($this->populargroups)) : ?>
					<div class="results-none">
						<p><?php echo Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS'); ?></p>
					</div>
				<?php else : ?>
					<?php
					foreach ($this->populargroups as $group)
					{
						$this->view('_group')
							->set('group', $group)
							->display();
					}
					?>
				<?php endif; ?>
			</div>
		</section><!-- / .populargroups -->
	<?php endif; ?>

	<?php if ($this->config->get('intro_featuredgroups', 1) && count($this->featuredgroups) > 0) : ?>
		<section class="featuredgroups">
			<h3><?php echo Lang::txt('COM_GROUPS_INTRO_FEATURED_GROUPS_TITLE'); ?></h3>

			<div class="groups-container">
				<?php if (!count($this->featuredgroups)) : ?>
					<div class="results-none">
						<p><?php echo Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS'); ?></p>
					</div>
				<?php else : ?>
					<?php
					foreach ($this->featuredgroups as $group)
					{
						$this->view('_group')
							->set('group', $group)
							->display();
					}
					?>
				<?php endif; ?>
			</div>
		</section><!-- / .featuredgroups -->
	<?php endif; ?>
</section><!-- / .section -->