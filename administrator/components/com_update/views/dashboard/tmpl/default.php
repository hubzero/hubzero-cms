<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('CMS Updater: Dashboard'));
JToolBarHelper::preferences($this->option, '550');

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
							<p>The repository has the following divergence (and cannot be updated)</p>
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
					<a class="action" href="<?php echo JRoute::_('index.php?option=com_update&controller=repository&status='.$logStatus); ?>">View change log</a>
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
				<a class="action" href="<?php echo JRoute::_('index.php?option=com_update&controller=database'); ?>">View migration log</a>
			</div>
		</div>
	</div>
</div>