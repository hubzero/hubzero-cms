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

$dateFormat = '%b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M d, Y';
	$tz = false;
}

	// Build pub url
	$route = $this->publication->project_provisioned == 1 
		? 'index.php?option=com_publications' . a . 'task=submit'
		: 'index.php?option=com_projects' . a . 'alias=' . $this->publication->project_alias . a . 'active=publications';
	$url = JRoute::_($route . a . 'pid=' . $this->publication->id);

?>
<h3>
	<a name="versions"></a>
	<?php echo JText::_('PLG_PUBLICATION_VERSIONS'); ?> 
</h3>
<?php if($this->authorized && $this->contributable) { ?>
	<p class="info statusmsg"><?php echo JText::_('PLG_PUBLICATION_VERSIONS_ONLY_PUBLIC_SHOWN'); ?> 
		<a href="<?php echo $url . '?action=versions'; ?>"><?php echo JText::_('PLG_PUBLICATION_VERSIONS_VIEW_ALL'); ?></a>
	</p>
<?php } ?>
<?php
if ($this->versions && count($this->versions) > 0) {
	$cls = 'even';
?>
<table class="resource-versions">
	<thead>
		<tr>
			<th><?php echo JText::_('PLG_PUBLICATION_VERSIONS_VERSION'); ?></th>
			<th><?php echo JText::_('PLG_PUBLICATION_VERSIONS_RELEASED'); ?></th>
			<th><?php echo JText::_('PLG_PUBLICATION_VERSIONS_DOI_HANDLE'); ?></th>
			<th><?php echo JText::_('PLG_PUBLICATION_VERSIONS_STATUS'); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ($this->versions as $v) 
	{
		$handle = ($v->doi) ? $v->doi : '' ;

		$cls = (($cls == 'even') ? 'odd' : 'even');
?>
		<tr class="<?php echo $cls; ?>">
			<td <?php if($v->version_number == $this->publication->version_number) { echo 'class="active"'; }  ?>><?php echo $v->version_label; ?></td>
			<td><?php echo ($v->published_up && $v->published_up!='0000-00-00 00:00:00') ? JHTML::_('date',$v->published_up, $dateFormat, $tz) : 'N/A'; ?></td>
			<td><?php echo $v->doi ? $v->doi : JText::_('COM_PUBLICATIONS_NA'); ?></td>
			<td class="<?php echo $v->state == 1 ? 'state_published' : 'state_unpublished'; ?>"><?php echo $v->state == 1 ? JText::_('PLG_PUBLICATION_VERSIONS_PUBLISHED') : JText::_('PLG_PUBLICATION_VERSIONS_UNPUBLISHED'); ?></td>
			<td><a href="<?php echo JRoute::_('index.php?option='
			.$this->option.a.'id='.
			$this->publication->id).'/?v='.$v->version_number; ?>"><?php echo JText::_('PLG_PUBLICATION_VERSIONS_VIEW'); ?></a></td>
		</tr>
<?php
	}
?>
	</tbody>
</table>
<?php } else { ?>
	<p class="nocontent"><?php echo JText::_('PLG_PUBLICATION_VERSIONS_NO_VERIONS_FOUND'); ?></p>
<?php } ?>
