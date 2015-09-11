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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('CMS Updater: Dashboard'));
Toolbar::preferences($this->option, '550');

$this->css();
$logStatus = 'all';
?>

<div class="widget code">
	<div class="inner">
		<div class="title"><div>Repository Status</div></div>
		<div class="sub-title">
			<div class="sub-title-inner">Version: <?php echo $this->repositoryVersion; ?></div>
		</div>
		<div class="sub-title">
			<div class="sub-title-inner">Mechanism: <?php echo $this->repositoryMechanism; ?></div>
		</div>
		<div class="content">
			<div class="content-inner">
				<div class="status">
					<?php if (empty($this->status)) : ?>
						<div class="status-message">
							<div class="good"></div>
							<?php if (!empty($this->upcoming)) : ?>
								<?php $logStatus = 'upcoming'; ?>
								<p>The repository is clean and can be updated.</p>
								<p>The repository is behind by <span class="emphasize"><?php echo count($this->upcoming); ?></span> items.</p>
							<?php else : ?>
								<?php $logStatus = 'installed'; ?>
								<p>The repository is clean and up-to-date.</p>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<div class="alert"></div>
						<div class="status-message">
							<?php if (!empty($this->upcoming)) : ?>
								<?php $logStatus = 'upcoming'; ?>
								<p>The repository is behind by <span class="emphasize"><?php echo count($this->upcoming); ?></span> items.</p>
							<?php else : ?>
								<?php $logStatus = 'installed'; ?>
								<p>The repository is up-to-date.</p>
							<?php endif; ?>
							<p>The repository has the following divergence.</p>
						</div>
						<div class="status-items">
							<?php foreach ($this->status as $key => $items) : ?>
								<div class="status-item-header"><?php echo ucfirst($key); ?>:</div>
								<?php foreach ($items as $item) : ?>
									<div class="status-item"><?php echo $item; ?></div>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<a class="action" href="<?php echo Route::url('index.php?option=com_update&controller=repository&status='.$logStatus); ?>">View change log</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="widget database">
	<div class="inner">
		<div class="title"><div>Database Status</div></div>
		<div class="sub-title">
			<div class="sub-title-inner">Version: <?php echo $this->databaseVersion; ?></div>
		</div>
		<div class="sub-title">
			<div class="sub-title-inner">Mechanism: <?php echo $this->databaseMechanism; ?></div>
		</div>
		<div class="content">
			<div class="content-inner">
				<?php if (empty($this->migration)) : ?>
					<div class="good"></div>
					<div class="status-message">
						<p>
							All migrations have been run
						</p>
					</div>
				<?php else : ?>
					<div class="alert"></div>
					<div class="status-message">
						<p>
							The database is missing the following migrations
						</p>
					</div>
					<div class="status-items">
						<?php foreach ($this->migration as $migrationType => $files) : ?>
							<div class="status-item-header"><?php echo ucfirst($migrationType); ?>:</div>
							<?php foreach ($files as $file) : ?>
								<div class="status-item">
									<?php echo $file; ?>
								</div>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<a class="action" href="<?php echo Route::url('index.php?option=com_update&controller=database'); ?>">View migration log</a>
			</div>
		</div>
	</div>
</div>