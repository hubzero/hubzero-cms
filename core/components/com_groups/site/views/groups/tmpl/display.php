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
 * @author    Christopher Smoak <csmoak@purdue.edu>
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

	<?php if (User::authorise('core.create', $this->option)) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=new'); ?>">
					<?php echo Lang::txt('COM_GROUPS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<?php
	foreach ($this->notifications as $notification)
	{
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<div class="grid">
				<div class="col span-half">
					<h3><?php echo Lang::txt('COM_GROUPS_INTRO_WHAT_ARE_GROUPS_TITLE'); ?></h3>
					<p><?php echo Lang::txt('COM_GROUPS_INTRO_WHAT_ARE_GROUPS_DESC'); ?></p>
				</div>
				<div class="col span-half omega">
					<h3><?php echo Lang::txt('COM_GROUPS_INTRO_HOW_DO_GROUPS_WORK_TITLE'); ?></h3>
					<p><?php echo Lang::txt('COM_GROUPS_INTRO_HOW_DO_GROUPS_WORK_DESC'); ?></p>
				</div>
			</div>
		</div><!-- / .subject -->
		<div class="col span3 omega">
			<h3>Questions?</h3>
			<ul>
				<li>
					<a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=groups&page=index'); ?>">
						<?php echo Lang::txt('COM_GROUPS_INTRO_NEED_HELP'); ?>
					</a>
				</li>
			</ul>
		</div><!-- / .aside -->
	</div>
</section><!-- / #introduction.section -->

<section class="section">
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

	<div class="grid">
		<div class="col span3">
			<h2>
				<?php echo Lang::txt('COM_GROUPS_INTRO_FIND_GROUP'); ?>
			</h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="col span6">
				<form action="<?php echo Route::url('index.php?option='.$this->option.'&task=browse'); ?>" method="get" class="search">
					<fieldset>
						<p>
							<label for="gsearch"><?php echo Lang::txt('COM_GROUPS_INTRO_FIND_GROUP_SEARCH_LABEL'); ?></label>
							<input type="text" name="search" id="gsearch" value="" />
							<input type="submit" value="Search" />
						</p>
						<p><?php echo Lang::txt('COM_GROUPS_INTRO_FIND_GROUP_SEARCH_HELP'); ?></p>
					</fieldset>
				</form>
			</div><!-- / .col -->
			<div class="col span6 omega">
				<div class="browse">
					<p>
						<a class="group-intro-browse" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse'); ?>">
							<?php echo Lang::txt('COM_GROUPS_INTRO_FIND_GROUP_BROWSE_BUTTON_TEXT'); ?>
						</a>
					</p>
					<p><?php echo Lang::txt('COM_GROUPS_INTRO_FIND_GROUP_BROWSE_HELP'); ?></p>
				</div><!-- / .browse -->
			</div><!-- / .col span6 -->
		</div><!-- / .col span9 -->
	</div><!-- / .grid -->

	<?php if (!User::isGuest()) : ?>
		<?php if ($this->config->get("intro_mygroups", 1)) : ?>
			<div class="grid mygroups">
				<div class="col span3">
					<h2><?php echo Lang::txt('COM_GROUPS_INTRO_MY_GROUPS_TITLE'); ?></h2>
				</div><!-- / .col span3 -->
				<div class="col span9 omega">
					<div class="clearfix top">
						<?php
						$mygroups_members = (isset($this->mygroups['members'])) ? $this->mygroups['members'] : array();
						$this->view('_list')
						     ->set('name', 'My Groups')
						     ->set('config', $this->config)
						     ->set('groups', $mygroups_members)
						     ->set('display_private_description', true)
						     ->set('description_char_limit', 0)
						     ->display();
						?>
					</div>
				</div><!-- / .col span9 omega -->
			</div><!-- / .grid -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!User::isGuest()) : ?>
		<?php if ($this->config->get("intro_interestinggroups", 1)) : ?>
			<div class="grid interestinggroups">
				<div class="col span3">
					<h2><?php echo Lang::txt('COM_GROUPS_INTRO_INTERESTING_GROUPS_TITLE'); ?></h2>
				</div><!-- / .col span3 -->
				<div class="col span9 omega">
					<div class="clearfix top">
						<?php
						$this->view('_list')
						     ->set('name', 'Interesting Groups')
						     ->set('config', $this->config)
						     ->set('groups', $this->interestinggroups)
						     ->display();
						?>
					</div>
				</div><!-- / .col span9 omega -->
			</div><!-- / .grid -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($this->config->get("intro_populargroups", 1)) : ?>
		<div class="grid populargroups">
			<div class="col span3">
				<h2><?php echo Lang::txt('COM_GROUPS_INTRO_POPULAR_GROUPS_TITLE'); ?></h2>
			</div><!-- / .col span3 -->
			<div class="col span9 omega">
				<div class="clearfix top">
					<?php
					$this->view('_list')
					     ->set('name', 'Popular Groups')
					     ->set('config', $this->config)
					     ->set('groups', $this->populargroups)
					     ->display();
					?>
				</div>
			</div><!-- / .col span9 omega -->
		</div><!-- / .grid -->
	<?php endif; ?>

	<?php if ($this->config->get("intro_featuredgroups", 1) && count($this->featuredgroups) > 0) : ?>
		<div class="grid featuredgroups">
			<div class="col span3">
				<h2><?php echo Lang::txt('COM_GROUPS_INTRO_FEATURED_GROUPS_TITLE'); ?></h2>
			</div><!-- / .col span3 -->
			<div class="col span9 omega">
				<div class="clearfix top">
					<?php
					$this->view('_list')
					     ->set('name', 'Featured Groups')
					     ->set('config', $this->config)
					     ->set('groups', $this->featuredgroups)
					     ->display();
					?>
				</div>
			</div><!-- / .col span9 omega -->
		</div><!-- / .grid -->
	<?php endif; ?>
</section><!-- / .section -->