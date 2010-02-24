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

if (!$this->getError()) {
	// Build the HTML
	$juser =& JFactory::getUser();
	if (is_array($this->lines) && count($this->lines['return']) > 0) {
?>
	<table class="recommendations" summary="<?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_TBL_SUMMARY'); ?>">
		<thead>
			<tr>
				<th><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_RECOMMENDATION'); ?></th>
				<th><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_RESULT_TYPE'); ?></th>
				<th><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_RESULT_RELEVANCE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach ($this->lines['return'] as $r)
		{
			if (strstr($r['nanoUrl'], '/contributors/')) {
				$r['nanoUrl'] = str_replace( '/contributors/', '/members/', $r['nanoUrl'] );
			}
?>
			<tr>
				<td><a href="<?php echo $r['nanoUrl']; ?>?rec=<?php echo $this->rid; ?>"><?php echo $r['label']; ?></a></td>
				<td class="type"><?php echo ucfirst($r['type']); ?></td>
				<td><?php echo ($r['score'] * 10); ?>%</td>
			</tr>
<?php
		}
?>
		</tbody>
	</table>
	<div class="clear"></div><!-- Used only to fix some strange IE 6 display problems -->
<?php
		if ($juser->get('guest')) {
?>
		<p class="warning"><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_LOGIN_TO_GET_BETTER_RESULTS'); ?></p>
<?php
		}
	} else { ?>
		<p><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_NONE_FOUND'); ?></p>
<?php
	}
} else { ?>
	<p class="warning"><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_UNABLE_TO_GET'); ?></p>
<?php } ?>