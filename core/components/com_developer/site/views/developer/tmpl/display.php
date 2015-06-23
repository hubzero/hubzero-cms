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

// include css
$this->css('introduction', 'system')
	 ->css('intro')
	 ->css();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER'); ?></h2>
</header>

<section class="main section developer-section api">
	<div class="grid">
		<div class="col span8">
			<h3><?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT'); ?></h3>
			<?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_DESC'); ?>
			<a href="<?php echo Route::url('index.php?option=com_developer&controller=api'); ?>" class="btn btn-info icon-go opposite section-link-home">
				<?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_HOME'); ?>
			</a>
		</div>
		<div class="col span4 omega">
			<div class="icon-api"></div>
		</div>
	</div>
</section>

<section class="main section developer-section web">
	<div class="grid">
		<div class="col span4">
			<div class="icon-web"></div>
		</div>
		<div class="col span8 omega">
			<h3><?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT'); ?></h3>
			<?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT_DESC'); ?>
			<a href="<?php echo Route::url('index.php?option=com_developer&controller=web'); ?>" class="btn btn-info icon-go opposite section-link-home">
				<?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT_HOME'); ?>
			</a>
		</div>
	</div>
</section>

<section class="main section developer-section tool">
	<div class="grid">
		<div class="col span8">
			<h3><?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT'); ?></h3>
			<?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_DESC'); ?>
			<a href="<?php echo Route::url('index.php?option=com_developer&controller=tools'); ?>" class="btn btn-info icon-go opposite section-link-home">
				<?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_HOME'); ?>
			</a>
		</div>
		<div class="col span4 omega">
			<div class="icon-tool"></div>
		</div>
	</div>
</section>