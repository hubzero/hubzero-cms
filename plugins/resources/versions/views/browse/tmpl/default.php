<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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