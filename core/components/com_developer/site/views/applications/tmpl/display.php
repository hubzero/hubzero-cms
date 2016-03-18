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

$this->css('applications')
     ->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-add" href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>">
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_NEW'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section full">
	<div class="subject">
		<div class="container">
			<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_MINE'); ?></h3>
			<ul class="entries-list applications your-applications">
				<?php if ($this->applications->count() > 0) : ?>
					<?php foreach ($this->applications as $application) : ?>
						<li>
							<h4>
								<a href="<?php echo Route::url($application->link()); ?>">
									<?php echo $this->escape($application->get('name')) ?>
								</a>
							</h4>
							<dl class="meta">
								<dd><?php echo $application->created('date'); ?></dd>
								<dd><?php echo $application->created('time'); ?></dd>
								<dd><?php echo $application->users(); ?> active users</dd>
							</dl>
							<p><?php echo $this->escape(\Hubzero\Utility\String::truncate($application->get('description'), 500)); ?></p>
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li class="empty">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_MINE_NONE', Route::url('index.php?option=com_developer&controller=applications&task=new')); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<hr />

		<div class="container" id="authorized">
			<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED'); ?></h3>
			<ul class="entries-list applications authorized-applications">
				<?php if ($this->tokens->count() > 0) : ?>
					<?php foreach ($this->tokens as $token) : ?>
						<li>
							<?php $application = $token->application(); ?>
							<h4>
								<?php echo $this->escape($application->get('name')) ?>
							</h4>
							<a class="btn btn-secondary revoke confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_REVOKE_ACCESS_CONFIRM'); ?>" href="<?php echo Route::url($application->link('revoke').'&token=' . $token->get('id')); ?>">
								<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_REVOKE_ACCESS'); ?>
							</a>
							<dl class="meta">
								<dd><?php echo Lang::txt('Authorization Date: %s', $token->created('m/d/Y @ g:ia')); ?></dd>
							</dl>
							<p><?php echo $this->escape(\Hubzero\Utility\String::truncate($application->get('description'), 500)); ?></p>
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li class="empty">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_NONE'); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</section>