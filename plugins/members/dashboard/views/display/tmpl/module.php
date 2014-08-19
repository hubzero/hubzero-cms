 <?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// create user params registry
$params = new JRegistry($this->module->params);

// load module params fields
$fields = new JForm($this->module->module);
$fields->loadFile(JPATH_ROOT . DS . 'modules' . DS . $this->module->module . DS . $this->module->module . '.xml', true, 'config/fields');

// create settings sub view
$view = $this->view('parameters');
$view->admin  = $this->admin;
$view->module = $this->module;
$view->params = $params->toArray();
$view->fields = $fields->getFieldset('basic');
$settingsHtml = trim($view->loadTemplate());
?>

<div class="module <?php echo strtolower($this->module->module) . ' ' . $params->get('moduleclass_sfx'); ?>  draggable sortable"
	data-row="<?php echo $this->module->positioning->row; ?>"
	data-col="<?php echo $this->module->positioning->col; ?>"
	data-sizex="<?php echo $this->module->positioning->size_x; ?>"
	data-sizey="<?php echo $this->module->positioning->size_y; ?>"
	data-moduleid="<?php echo $this->module->id; ?>">

	<div class="module-title">
		<h3><?php echo $this->escape($this->module->title); ?></h3>
		<ul class="module-links">
			<?php if ($settingsHtml != '') : ?>
				<li>
					<a class="settings" title="Module Settings" href="javascript:void(0);">
						<span><?php echo JText::_('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS'); ?></span>
					</a>
				</li>
			<?php endif; ?>
			<li>
				<a class="remove" title="Remove Module" href="javascript:void(0);">
					<span><?php echo JText::_('PLG_MEMBERS_DASHBOARD_MODULE_REMOVE'); ?></span>
				</a>
			</li>
		</ul>
	</div>

	<div class="module-main">
		<?php echo $settingsHtml; ?>
		<div class="module-content">
			<?php
				if ($this->admin)
				{
					echo '<div class="custom">' . JText::_('PLG_MEMBERS_DASHBOARD_MODULE_ADMIN_CONTENT') . '</div>';
				}
				elseif ($this->module->module == 'mod_custom')
				{
					echo '<div class="custom">' . $this->module->content . '</div>';
				}
				else
				{
					$rparams            = array();
					$rparams['style']   = 'none';
					$this->module->user = false;
					echo JModuleHelper::renderModule($this->module, $rparams);
				}
			?>
		</div>
	</div><!-- /.module-main -->
</div>