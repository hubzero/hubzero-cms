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
<h3>
	<a name="versions"></a>
	<?php echo JText::_('PLG_RESOURCES_VERSIONS'); ?> 
</h3>
<?php
if ($this->rows) {
	$config =& JComponentHelper::getParams( $this->option );
	$hubDOIpath = $config->get('doi');

	$cls = 'even';
?>
<table class="resource-versions" summary="<?php echo JText::_('PLG_RESOURCES_VERSIONS_TBL_SUMMARY'); ?>">
	<thead>
		<tr>
			<th><?php echo JText::_('PLG_RESOURCES_VERSIONS_VERSION'); ?></th>
			<th><?php echo JText::_('PLG_RESOURCES_VERSIONS_RELEASED'); ?></th>
			<th><?php echo JText::_('PLG_RESOURCES_VERSIONS_DOI_HANDLE'); ?></th>
			<th><?php echo JText::_('PLG_RESOURCES_VERSIONS_PUBLISHED'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ($this->rows as $v) 
	{
		$handle = ($v->doi) ? $hubDOIpath.'r'.$this->resource->id.'.'.$v->doi : '' ;

		$cls = (($cls == 'even') ? 'odd' : 'even');
?>
		<tr class="<?php echo $cls; ?>">
			<td>
				<?php echo ($v->version) ? '<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id).'?rev='.$v->revision.'">'.$v->version.'</a>' : 'N/A'; ?>
			</td>
			<td>
				<?php echo ($v->released && $v->released!='0000-00-00 00:00:00') ? JHTML::_('date',$v->released, '%d %b %Y') : 'N/A'; ?>
			</td>
			<td>
				<?php echo ($handle) ? '<a href="http://hdl.handle.net/'.$handle.'">'.$handle.'</a>' : 'N/A'; ?>
			</td>
			<td>
				<span class="<?php echo ($v->state=='1') ? 'toolpublished' : 'toolunpublished'; ?>">
					<?php echo ($v->state=='1') ? JText::_('PLG_RESOURCES_VERSIONS_YES') : JText::_('PLG_RESOURCES_VERSIONS_NO'); ?>
				</span>
			</td>
		</tr>
<?php
	}
?>
	</tbody>
</table>
<?php } else { ?>
	<p><?php echo JText::_('PLG_RESOURCES_VERSIONS_NO_VERIONS_FOUND'); ?></p>
<?php } ?>
