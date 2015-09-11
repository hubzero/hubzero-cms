<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

// add styles & scripts
$this->css()
     ->js()
     ->css('jquery.fancyselect.css', 'system')
     ->js('jquery.fancyselect', 'system')
     ->js('jquery.nestedsortable', 'system');

// has home override
$hasHomeOverride = false;
if (file_exists(PATH_CORE . DS . $this->group->getBasePath() . DS . 'pages' . DS . 'overview.php'))
{
	$hasHomeOverride = true;
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-group group btn popup 1200x600" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component&path=/uploads'); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_UPLOAD_MANAGER'); ?>
				</a>
				<a class="icon-group group btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php foreach ($this->notifications as $notification) : ?>
		<p class="<?php echo $notification['type']; ?>">
			<?php echo $notification['message']; ?>
		</p>
	<?php endforeach; ?>

	<?php if ($this->group->isSuperGroup() && $hasHomeOverride) : ?>
		<p class="info"><?php echo Lang::txt('COM_GROUPS_PAGES_SUPER_GROUP_HAS_HOME_OVERRIDE'); ?></p>
	<?php endif; ?>

	<div class="group-page-manager">
		<ul class="tabs clearfix">
			<li><a data-tab="pages" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages#pages'); ?>"><?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGES'); ?></a></li>
			<li><a data-tab="categories" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages#categories'); ?>"><?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGE_CATEGORIES'); ?></a></li>
			<?php if ($this->group->isSuperGroup() || $this->config->get('page_modules', 0) == 1) : ?>
				<li><a data-tab="modules" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages#modules'); ?>"><?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_MODULES'); ?></a></li>
			<?php endif ;?>
		</ul>

		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm" class="full">
			<fieldset data-tab-content="pages">
				<?php
					$this->view('display')
					     ->set('group', $this->group)
					     ->set('categories', $this->categories)
					     ->set('pages', $this->pages)
					     ->set('config', $this->config)
					     ->display();
				?>
			</fieldset>

			<fieldset data-tab-content="categories">
				<?php
					$this->view('display', 'categories')
					     ->set('group', $this->group)
					     ->set('categories', $this->categories)
					     ->display();
				?>
			</fieldset>

			<?php if ($this->group->isSuperGroup() || $this->config->get('page_modules', 0) == 1) : ?>
				<fieldset data-tab-content="modules">
					<?php
						$this->view('display', 'modules')
						     ->set('group', $this->group)
						     ->set('modules', $this->modules)
						     ->display();
					?>
				</fieldset>
			<?php endif; ?>
		</form>
	</div>
</section>