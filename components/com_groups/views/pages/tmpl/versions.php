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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$editPageUrl = 'index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id');

?>

<div id="content-header" class="full">
	<h2><?php echo JText::sprintf('Page Versions: %s', $this->page->get('title')); ?></h2>
</div>
<div id="content-header-extra">
	<ul id="useroptions">
		<li><a class="icon-edit edit btn" href="<?php echo JRoute::_($editPageUrl); ?>">Edit Page</a></li>
	</ul>
</div>


<div class="main section group-page-versions">
	<table>
		<thead>
			<tr>
				<th>Verion</th>
				<th>Created</th>
				<th>Approved</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->page->versions() as $k => $pageVersion) : ?>
				<?php $cls = ($k == 0) ? 'current' : ''; ?>
				<tr class="<?php echo $cls; ?>">
					<td>
						<?php 
							echo $pageVersion->get('version');
							echo ($k == 0) ? ' (current)' : '';
						?>
					</td>
					<td>
						<?php
							$created = 'n/a';
							if ($pageVersion->get('created') != null)
							{
								$created = JHTML::_('date', $pageVersion->get('created'), 'F d, Y @ g:ia');
							}
							
							$created_by = 'n/a';
							if ($pageVersion->get('created_by') == 1000)
							{
								$created_by = 'System';
							}
							else if ($pageVersion->get('created_by') != null && is_numeric($pageVersion->get('created_by')))
							{
								$profile = \Hubzero\User\Profile::getInstance( $pageVersion->get('created_by') );
								$created_by = '<a href="'.JRoute::_('index.php?option=com_members&id=' . $profile->get('uidNumber')).'">'.$profile->get('name').'</a>';
							}
						?>
						<div class="created">
							<?php if ($created_by != 'n/a' && $created_by != 'System') : ?>
								<img align="left" width="40" src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($profile->get('uidNumber')); ?>" />
							<?php endif; ?>
							<span class="created-date"><?php echo $created; ?></span>
							<span class="created-by"><?php echo $created_by; ?></span>
						</div>
					</td>
					
					<td>
						<?php
							$approved_on = 'n/a';
							if ($pageVersion->get('approved_on') != null)
							{
								$approved_on = JHTML::_('date', $pageVersion->get('approved_on'), 'F d, Y @ g:ia');
							}
							
							$approved_by = 'n/a';
							if ($pageVersion->get('approved_by') == 1000)
							{
								$approved_by = 'System';
							}
							else if ($pageVersion->get('approved_by') != null && is_numeric($pageVersion->get('approved_by')))
							{
								$profile = \Hubzero\User\Profile::getInstance( $pageVersion->get('approved_by') );
								$approved_by = '<a href="'.JRoute::_('index.php?option=com_members&id=' . $profile->get('uidNumber')).'">'.$profile->get('name').'</a>';
							}
						?>
						<div class="approved">
							<?php if ($approved_by != 'n/a' && $approved_by != 'System') : ?>
								<img align="left" width="40" src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($profile->get('uidNumber')); ?>" />
							<?php endif; ?>
							<span class="approved-date"><?php echo $approved_on; ?></span>
							<span class="approved-by"><?php echo $approved_by; ?></span>
						</div>
					</td>
					
					<td width="100px">
						<a target="_blank" class="btn btn-secondary" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=raw&pageid='.$this->page->get('id').'&version='.$pageVersion->get('version')); ?>">
							View Raw
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
