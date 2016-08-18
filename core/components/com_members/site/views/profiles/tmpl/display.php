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
     ->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if (User::isGuest()) { ?>
		<div id="content-header-extra">
			<p>
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_members&controller=register'); ?>"><?php echo Lang::txt('COM_MEMBERS_REGISTER_NOW'); ?></a>
			</p>
		</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<div class="grid">
				<div class="col span6">
					<h3><?php echo Lang::txt('COM_MEMBERS_WHY_BECOME_MEMBER'); ?></h3>
					<p><?php echo Lang::txt('COM_MEMBERS_WHY_BECOME_MEMBER_EXPLANATION'); ?></p>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<h3><?php echo Lang::txt('COM_MEMBERS_HOW_TO_BECOME_MEMBER'); ?></h3>
					<p><?php echo Lang::txt('COM_MEMBERS_HOW_TO_BECOME_MEMBER_EXPLANATION'); ?></p>
				</div><!-- / .col span6 -->
			</div>
		</div>
		<div class="col span3 omega">
			<ul>
				<li>
					<a href="<?php echo Route::url('index.php?option=com_users&view=remind'); ?>"><?php echo Lang::txt('COM_MEMBERS_FORGOT_USERNAME'); ?></a>
				</li>
				<li>
					<a href="<?php echo Route::url('index.php?option=com_users&view=reset'); ?>"><?php echo Lang::txt('COM_MEMBERS_FORGOT_PASSWORD'); ?></a>
				</li>
				<li>
					<a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=members'); ?>"><?php echo Lang::txt('COM_MEMBERS_NEED_HELP'); ?></a>
				</li>
				<li>
					<a href="<?php echo Route::url('index.php?option=com_groups'); ?>"><?php echo Lang::txt('COM_GROUPS'); ?></a>
				</li>
			</ul>
		</div>
	</div><!-- / .grid -->
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="gsearch"><?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS_SEARCH_LABEL'); ?></label>
								<input type="hidden" name="q[0][field]" value="name" />
								<input type="hidden" name="q[0][operator]" value="like" />
								<input type="text" name="q[0][value]" id="gsearch" value="" />
								<input type="submit" value="<?php echo Lang::txt('Search'); ?>" />
							</p>
							<p>
								<?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS_BY_SEARCH'); ?>
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<div class="browse">
						<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS_BY_BROWSING'); ?></a></p>
						<p><?php echo Lang::txt('COM_MEMBERS_FIND_MEMBERS_LISTING'); ?></p>
					</div><!-- / .browse -->
				</div><!-- / .col span6 -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

	<?php /*if ($this->contribution_counting) { ?>
		<div class="grid">
			<div class="col span3">
				<h2><?php echo Lang::txt('COM_MEMBERS_TOP_CONTRIBUTOR'); ?></h2>
			</div><!-- / .col span3 -->
			<div class="col span9 omega">
				<div class="grid">
					<?php
					$rows = \Components\Members\Models\Member::all()
						->whereEquals('block', 0)
						->whereEquals('activation', 1)
						->where('approved', '>', 0)
						->order('contributions', 'desc')
						->limit(4)
						->rows();

					if ($rows->count())
					{
						$i = 0;
						foreach ($rows as $contributor)
						{
							if ($i == 2)
							{
								$i = 0;
							}

							switch ($i)
							{
								case 2: $cls = ''; break;
								case 1: $cls = 'omega'; break;
								case 0:
								default: $cls = ''; break;
							}
							?>
							<div class="col span-half <?php echo $cls; ?>">
								<div class="contributor">
									<p class="contributor-photo">
										<a href="<?php echo Route::url($contributor->link()); ?>">
											<img src="<?php echo $contributor->picture(); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_TOP_CONTRIBUTOR_PICTURE', $this->escape(stripslashes($contributor->get('name')))); ?>" />
										</a>
									</p>
									<div class="contributor-content">
										<h4 class="contributor-name">
											<a href="<?php echo Route::url($contributor->link()); ?>">
												<?php echo $this->escape(stripslashes($contributor->get('name'))); ?>
											</a>
										</h4>
										<?php if ($org = $contributor->get('organization')) { ?>
											<p class="contributor-org">
												<?php echo $this->escape(stripslashes($org)); ?>
											</p>
										<?php } ?>
										<div class="clearfix"></div>
									</div>
									<p class="course-instructor-bio">
										<?php if ($bio = $contributor->get('bio')) { ?>
											<?php echo Hubzero\Utility\String::truncate(strip_tags($bio), 200); ?>
										<?php } else { ?>
											<em><?php echo Lang::txt('COM_MEMBERS_TOP_CONTRIBUTOR_NO_BIO'); ?></em>
										<?php } ?>
									</p>
								</div>
							</div><!-- / .col span-third -->
							<?php if ($i == 1) { ?>
							</div><!-- / .grid -->
							<div class="grid">
							<?php } ?>
							<?php
							$i++;
						}
					}
					else
					{
						?>
						<p><?php echo Lang::txt('COM_MEMBERS_TOP_CONTRIBUTOR_NO_RESULTS', Route::url('index.php?option=com_resources&task=new')); ?></p>
						<?php
					}
					?>
				</div>
			</div><!-- / .col span9 omega -->
		</div><!-- / .grid -->
	<?php }*/ ?>
</section><!-- / .section -->
