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

defined('_HZEXEC_') or die();

$this->css()
     ->js('jquery.infinitescroll', 'com_collections')
     ->js();

$no_html = Request::getInt('no_html', 0);

// get all sessions
$online = array();
$sessions = Hubzero\Session\Helper::getAllSessions(array(
	'guest'    => 0,
	'distinct' => 1
));
if ($sessions)
{
	// see if any session matches our userid
	foreach ($sessions as $session)
	{
		$online[] = $session->userid;
	}
}

if (!$no_html) { ?>
<div class="activities">
	<form action="<?php echo Route::url($this->member->link() . '&active=activity'); ?>" method="get">
		<fieldset class="filters">
			<div class="grid">
				<div class="col span6">
					<input type="text" name="q" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH_PLACEHOLDER'); ?>" />
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH'); ?>" />
				</div>
				<div class="col span6 omega">
					<?php if ($this->filters['filter'] == 'starred') { ?>
						<a class="icon-star tooltips" href="<?php echo Route::url($this->member->link() . '&active=activity'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_ALL'); ?>">
							<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_ALL'); ?>
						</a>
					<?php } else { ?>
						<a class="icon-star-empty tooltips" href="<?php echo Route::url($this->member->link() . '&active=activity&filter=starred'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_STARRED'); ?>">
							<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_STARRED'); ?>
						</a>
					<?php } ?>
					<?php if ($this->digests) { ?>
						<a class="icon-config tooltips" href="<?php echo Route::url($this->member->link() . '&active=activity&action=settings'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS'); ?>">
							<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS'); ?>
						</a>
					<?php } ?>
				</div>
			</div>
		</fieldset>
<?php } ?>

		<?php if ($this->rows->count()) { ?>
			<ul class="activity-feed" data-url="<?php echo Route::url($this->member->link() . '&active=activity'); ?>">
				<?php
				foreach ($this->rows as $row)
				{
					$this->view('default_item')
						->set('member', $this->member)
						->set('row', $row)
						->set('online', $online)
						->display();
				}
				?>
			</ul>
			<?php
			//echo $this->rows->pagination;
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
			$pageNav->setAdditionalUrlParam('active', 'activity');
			if ($this->filters['filter'])
			{
				$pageNav->setAdditionalUrlParam('filter', $this->filters['filter']);
			}
			if ($this->filters['search'])
			{
				$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
			}
			echo $pageNav;
			?>
		<?php } else { ?>
			<div class="results-none">
				<div class="messages">
					<p><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_NO_RESULTS'); ?></p>
				</div>
				<div class="questions">
					<p>
						<strong><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_ABOUT_TITLE'); ?></strong><br />
						<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_ABOUT'); ?>
					<p>
				</div>
			</div>
		<?php } ?>

<?php if (!$no_html) { ?>
	</form>
</div>
<?php }
