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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('introduction', 'system')
     ->css('intro')
     ->css();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_HOME'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-code" href="<?php echo Route::url('index.php?option=com_developer'); ?>">
				<?php echo Lang::txt('COM_DEVELOPER'); ?>
			</a>
		</p>
	</div>
</header>

<section id="introduction" class="section api">
	<div class="section-inner">
		<div class="grid">
			<div class="col span8">
				<h3><?php echo Lang::txt('COM_DEVELOPER_API_GETSTARTED'); ?></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_GETSTARTED_DESC'); ?></p>
			</div>
			<div class="col span4 hasOnlyButton omega">
				<p>
					<a class="btn icon-docs" href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs'); ?>">
						<?php echo Lang::txt('COM_DEVELOPER_API_LINK_DOCUMENTATION'); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
</section>

<section class="main section">
	<div class="section-inner">
		<div class="grid">
			<div class="col span3">
				<h2><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS'); ?></h2>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_MY_APPLICATIONS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications#authorized'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_AUTHORIZED_APPLICATIONS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications#authorized'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3 omega">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_NEW_APPLICATION_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_CREATE'); ?></a></p>
			</div>
		</div>
		<?php /*<div class="grid">
			<div class="col span3">
				<h2><?php echo Lang::txt('COM_DEVELOPER_API_LEARN'); ?></h2>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_DOCS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_DOCS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=console'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_CONSOLE'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_CONSOLE_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=console'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_MANAGE'); ?></a></p>
			</div>
			<div class="col span3 omega">
				<h3><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=status'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_STATUS'); ?></a></h3>
				<p><?php echo Lang::txt('COM_DEVELOPER_API_STATUS_DESC'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=status'); ?>"><?php echo Lang::txt('COM_DEVELOPER_API_CREATE'); ?></a></p>
			</div>
		</div>*/ ?>
	</div>
</section>