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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(Lang::txt('COM_OAIPMH_SETTINGS'), 'generic.png');
JToolBarHelper::preferences('com_oaipmh', 500);
JToolBarHelper::spacer();
JToolBarHelper::help('oaipmh');

$this->css();

$lang = \JFactory::getLanguage()->getTag();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_PREFIX'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_FORMAT'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->service->getSchemas() as $name) { ?>
			<tr>
				<?php
				$this->service->setSchema($name);
				$schema = $this->service->getSchema();
				?>
				<th><?php echo $schema->name(); ?></th>
				<td><?php echo $schema->prefix(); ?></td>
				<td><code>&amp;metadataPrefix=<?php echo $schema->prefix(); ?></code></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>