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
?>

<div class="module-list">
	<h2 class="section-header">
		<?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES_TITLE'); ?>
	</h2>
	<ul class="module-list-triggers">
		<?php foreach ($this->modules as $module) : ?>
			<?php $cls = (in_array($module->id, $this->mymodules)) ? ' class="installed"' : '' ; ?>
			<li <?php echo $cls; ?>>
				<a href="javascript:void(0);" data-module="<?php echo $module->id; ?>">
					<?php echo $module->title; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<ul class="module-list-content">
		<?php foreach ($this->modules as $module) : ?>
			<?php $xml = JFactory::getXML(JPATH_ROOT.DS.'modules'.DS.$module->module.DS.$module->module.'.xml'); ?>
			<li class="<?php echo $module->id; ?>">
				<div class="module-title-bar">

					<?php if (in_array($module->id, $this->mymodules)) : ?>
						<a href="javascript:void(0);" class="btn button icon-extract" disabled="disabled">
							<?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES_INSTALLED'); ?>
						</a>
					<?php else : ?>
						<a href="javascript:void(0);" data-module="<?php echo $module->id; ?>" class="btn button btn-info icon-extract install-module">
							<?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES_INSTALL'); ?>
						</a>
					<?php endif; ?>

					<h3><?php echo $module->title; ?></h3>
				</div>
				<dl class="module-details">
					<dt><?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_VERSION'); ?></dt>
					<dd><?php echo $xml->attributes()->version; ?></dd>

					<?php if ($xml->description != 'MOD_CUSTOM_XML_DESCRIPTION') : ?>
						<dt><?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_DESCRIPTION'); ?></dt>
						<dd><?php echo $xml->description; ?></dd>
					<?php endif; ?>

					<?php if (count($xml->images->image) > 0) : ?>
						<dt><?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_SCREENSHOTS'); ?></dt>
						<dd>
							<?php foreach ($xml->images->image as $image) : ?>
								<img src="<?php echo $image; ?>" />
							<?php endforeach; ?>
						</dd>
					<?php endif; ?>
				</dl>
			</li>
		<?php endforeach; ?>
	</ul>
</div>