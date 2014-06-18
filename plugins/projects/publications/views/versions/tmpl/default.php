<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// Get publication properties
$typetitle = PublicationHelper::writePubCategory($this->pub->cat_alias, $this->pub->cat_name);

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form" >
	<div id="plg-header">
	<?php if($this->project->provisioned == 1 ) { ?>
		<h3 class="prov-header"><a href="<?php echo $this->route; ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <a href="<?php echo $this->url; ?>">"<?php echo $this->pub->title; ?>"</a> &raquo; <?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_VERSIONS')); ?></h3>
	<?php } else { ?>
		<h3 class="publications c-header"><a href="<?php echo $this->route; ?>"><?php echo $this->title; ?></a> &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo $this->url; ?>">"<?php echo $this->pub->title; ?>"</a></span> &raquo; <span class="indlist"> &raquo; <?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_VERSIONS')); ?></span>
		</h3>
	<?php } ?>
	</div>
	<div class="list-editing">
	 <p><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_TOTAL_VERSIONS')); ?>: <span class="prominent"><?php echo count($this->versions); ?></span></p>
	</div>
	<?php if($this->versions) { ?>
		<table class="listing">
		 <thead>
			<tr>
				<th class="tdmini"></th>
				<th class="tdmini"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DOI').'/'.JText::_('PLG_PROJECTS_PUBLICATIONS_ARK'); ?></th>
				<th><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_OPTIONS')); ?></th>
			</tr>
		 </thead>
		 <tbody>
		<?php foreach($this->versions as $v) {
			// Get DOI
			$doi = $v->doi ? 'doi:'.$v->doi : '';
			$ark = $v->ark ? 'ark:'.$v->ark : '';
			if($ark || $doi)
			{
				$doi_notice = $doi ? $doi : $ark;
			}
			else {
				$doi_notice = JText::_('PLG_PROJECTS_PUBLICATIONS_NA');
			}

			// Version status
			$status = PublicationHelper::getPubStateProperty($v, 'status');
			$class = PublicationHelper::getPubStateProperty($v, 'class');
			$date = PublicationHelper::getPubStateProperty($v, 'date');

			$options = '<a href="'.$this->url.'?version='.$v->version_number.'">'
			.JText::_('PLG_PROJECTS_PUBLICATIONS_MANAGE_VERSION').'</a>';

			$options .= '<span class="block"><a href="'.JRoute::_('index.php?option=com_publications'
			.a.'id='.$this->pid).'?v='.$v->version_number.'">'
			.JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_PAGE').'</a></span>';

			?>
			<tr class="mini <?php if($v->main == 1) { echo ' vprime'; } ?>">
				<td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
				<td><?php echo $v->version_label; ?></td>
				<td><?php echo $v->title; ?></td>
				<td>
					<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
					<?php if($date) { echo '<span class="block ipadded faded">'.$date.'</span>';  } ?>
				</td>
				<td><?php echo $doi_notice; ?></td>
				<td><?php echo $options; ?></td>
			</tr>
		<?php } ?>
		 </tbody>
		</table>
	<?php } ?>
</form>