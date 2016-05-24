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

// No direct access.
defined('_HZEXEC_') or die();

$attachments = 0;
$authors = 0;
$tags = array();
$state = 'pending';
$type = '';
if ($this->resource->get('id'))
{
	switch ($this->resource->get('published'))
	{
		case 1: $state = 'published';  break;  // published
		case 2: $state = 'draft';      break;  // draft
		case 3: $state = 'pending';    break;  // pending
		case 0:
		default: $state = 'unpublished';  break;  // unpublished
	}

	$type = $this->resource->type()->get('title', Lang::txt('COM_CONTRIBUTE_NONE'));

	$attachments = $this->resource->children()->total();

	$authors =  $this->resource->authors()->total();

	$tags = $this->resource->tags()->count();
}


$this->css('create.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<div class="subject">
		<?php if ($this->getError()) { ?>
			<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

		<p class="passed">
			Thank you for your contribution!
		</p>

		<div class="container">
			<table class="summary">
				<caption>Contribution submitted:</caption>
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('Type'); ?></th>
						<td>
							<?php echo ($type) ? $this->escape(stripslashes($type)) : Lang::txt('(none)'); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('Title'); ?></th>
						<td>
							<?php echo ($this->resource->title) ? $this->escape(\Hubzero\Utility\String::truncate(stripslashes($this->resource->title), 150)) : Lang::txt('(none)'); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('Attachments'); ?></th>
						<td>
							<?php echo $attachments; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('Authors'); ?></th>
						<td>
							<?php echo $authors; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('Tags'); ?></th>
						<td>
							<?php echo $tags; ?>
						</td>
					</tr>
					<tr>
						<th scope="crow"><?php echo Lang::txt('Status'); ?></th>
						<td>
							<span class="<?php echo $state; ?> status"><?php echo $state; ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="container">
			<div class="container-block">
				<h3>Frequently Asked Questions</h3>
				<div class="entry-content">
					<ul class="faq-list">
						<li><a href="#submission">What happens now?</a></li>
					<?php if ($this->config->get('autoapprove', 0) != 1) { ?>
						<li><a href="#status">How will I know when my contribution is accepted?</a></li>
					<?php } ?>
						<li><a href="#retract">Ooops! I missed something and/or submitted too early!</a></li>
					</ul>
				</div>
			<?php if ($this->config->get('autoapprove', 0) != 1) { ?>
				<div class="entry-content" id="submission">
					<h4>What happens now?</h4>
					<p>After submitting your contribution, it will be reviewed for completeness. If all appears satisfactory, the contribution will be approved and immediately appear in the <a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">resources listing</a>.</p>
				</div>
				<div class="entry-content" id="status">
					<h4>How will I know when my contribution is accepted?</h4>
					<p>When a contribution passes the review stage and is published (made publicly available), an email is sent to all authors listed on the contribution.</p>
					<p>You may also continually monitor the status by:</p>
					<ul>
						<li>checking your "contributions" tab under your <a href="<?php echo Route::url('index.php?option=com_members&task=myaccount'); ?>">account</a></li>
						<li>checking the "My Drafts" module on your personalized dashboard (found <a href="<?php echo Route::url('index.php?option=com_members&task=myaccount'); ?>">here</a>). <strong>Note:</strong> The module must be present on your dashboard. If it isn't, you can easily add it from the "personalize dashboard" item.</li>
						<li>visiting the <a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">new contribution</a> page</li>
					</ul>
				</div>
				<div class="entry-content" id="retract">
					<h4>Ooops! I missed something and/or submitted too early!</h4>
					<p>No worries! You can retract a submission by following these steps:</p>
					<ul>
						<li>Visit the <a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">new contribution</a> page.</li>
						<li>You should be presented with a list of your "drafts" and "pending" submissions. Find the (pending) contribution you wish to retract.</li>
						<li>Click "retract".</li>
					</ul>
				</div>
			<?php } else { ?>
				<div class="entry-content" id="submission">
					<h4>What happens now?</h4>
					<p>Your contribution is now published. You may view it <a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id); ?>">here</a>.</p>
				</div>
				<div class="entry-content" id="retract">
					<h4>Ooops! I missed something and/or submitted too early!</h4>
					<p>No worries! You can either <a href="<?php echo Route::url('index.php?option=com_support'); ?>">contact the site administrators</a> and ask the submission be retracted (set back to "draft" status) or modify a submission by following these steps:</p>
					<ul>
						<li>Visit the <a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id); ?>">resource's page</a> while <strong>logged in</strong>.</li>
						<li>You should see an "edit" button or link next to the title of the resource.</li>
						<li>Click "edit" and make the desired edits. Changes on approved resource take effect immediately and do not require approval.</li>
					</ul>
				</div>
			<?php } ?>
			</div><!-- / .container-block -->
		</div><!-- / .container -->
	</div><!-- /.subject -->
	<aside class="aside">
		<p>
			<a class="icon-prev btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">Return to start</a>
		</p>
	</aside><!-- /.aside -->
</section><!-- / .main section -->
